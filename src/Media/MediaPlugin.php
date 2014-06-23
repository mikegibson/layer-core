<?php

namespace Layer\Media;

use Layer\Action\ActionEvent;
use Layer\Media\File\FileResponse;
use Layer\Media\Image\FilteredImageResponse;
use Layer\Media\Image\FilteredImageWriter;
use Layer\Media\Image\FilterRegistry;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;
use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MediaPlugin extends Plugin {

	public function getName() {
		return 'media';
	}

	public function register(Application $app) {

		$app['media.entity_classes.files'] = 'Layer\\Media\\File\\File';
		$app['media.entity_classes.images'] = 'Layer\\Media\\Image\\Image';

		$app['paths.uploads'] = $app['paths.storage'] . '/uploads';

		$app['paths.cache.images'] = $app['paths.cache'] . '/images';

		$app['media.url_fragment'] = 'media';

		$app['media.repositories.files'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['media.entity_classes.files']);
		});

		$app['media.repositories.images'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['media.entity_classes.images']);
		});

		$app['media.controllers'] = $app->share(function() use($app) {

			$media = $app['controllers_factory'];

			$media
				->get('/{filename}', function(Request $request) {
					return new FileResponse($request->get('file'));
				})
				->assert('filename', '.+')
				->beforeMatch(function(array $attrs) use($app) {
					$result = $app['media.repositories.files']->findOneBy([
						'filename' => $attrs['filename'],
						'webAccessible' => true
					]);
					if(!$result) {
						return false;
					}
					$attrs['file'] = $result;
					return $attrs;
				})->bind('media');

			return $media;

		});

		if (class_exists('\Gmagick')) {
			$app['imagine.driver'] = 'Gmagick';
		} elseif (class_exists('\Imagick')) {
			$app['imagine.driver'] = 'Imagick';
		} else {
			$app['imagine.driver'] = 'Gd';
		}

		$app['imagine'] = $app->share(function(Application $app) {
			$classname = sprintf('Imagine\%s\Imagine', $app['imagine.driver']);

			return new $classname;
		});

		$app['images.url_fragment'] = 'images';

		$app['images.controllers'] = $app->share(function() use($app) {

			$images = $app['controllers_factory'];

			$images
				->get('/{filter}/{filename}', function(Request $request) use($app) {
					return new FilteredImageResponse(
						$request->get('image'),
						$request->get('filter'),
						$app['images.filter_writer']
					);
				})
				->assert('filename', '.+')
				->beforeMatch(function(array $attrs) use($app) {
					if(!$app['images.filters']->hasFilter($attrs['filter'])) {
						return false;
					}
					$attrs['filterName'] = $attrs['filter'];
					$attrs['filter'] = $app['images.filters']->getFilter($attrs['filterName']);
					$result = $app['media.repositories.images']->createQueryBuilder()
						->select('image')
						->from($app['media.entity_classes.images'], 'image')
						->innerJoin('image.file', 'file')
						->where('file.filename = :filename')
						->setParameter('filename', $attrs['filename'])
						->setMaxResults(1)
						->getQuery()->getResult();
					if(!$result) {
						return false;
					}
					$attrs['image'] = current($result);
					return $attrs;
				})
				->bind('image');

			return $images;

		});

		$app['images.filters'] = $app->share(function() use($app) {
			return new FilterRegistry();
		});

		$app['images.filter_writer'] = $app->share(function() use($app) {
			return new FilteredImageWriter($app['imagine'], $app['paths.cache.images']);
		});

		$app['cms.media_node'] = $app->share(function() use($app) {
			return new ControllerNode('cms', null, null, 'media', 'Media', null, true, false);
		});

		$app['cms.root_node'] = $app->share($app->extend('cms.root_node',
			function(ControllerNodeInterface $rootNode) use($app) {
				$rootNode->wrapChildNode($app['cms.media_node']);
				return $rootNode;
			}
		));

	}

	public function boot(Application $app) {
		$app['orm.em']->getEventManager()->addEventSubscriber(new UploadListener(
			$app['media.repositories.files'],
			$app['media.repositories.images'],
			$app['paths.uploads'],
			'/' . $app['media.url_fragment'] . '/'
		));
		$app->mount('/' . $app['media.url_fragment'], $app['media.controllers']);
		$app->mount('/' . $app['images.url_fragment'], $app['images.controllers']);
		$app['dispatcher']->addListener(ActionEvent::BEFORE_RENDER, function(ActionEvent $event) use($app) {
			$result = $event->getResult();
			if(
				empty($result['node']) ||
				empty($result['repository']) ||
				$result['repository'] !== $app['media.repositories.files'] ||
				!$result['node'] instanceof ControllerNodeInterface ||
				$result['node']->getRouteName() !== 'cms' ||
				$result['node']->getActionName() !== 'edit'
			) {
				return;
			}
			$event->setTemplate('@media/cms/edit_file');
		});
	}

}