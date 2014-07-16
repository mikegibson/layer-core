<?php

namespace Sentient\Blog;

use Sentient\Blog\Action\ListPostsAction;
use Sentient\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BlogPlugin extends Plugin {

	public function getName() {
		return 'blog';
	}

	public function register(Application $app) {

		$app['blog.title'] = 'Blog';

		$app['blog.entity_classes.blog_posts'] = 'Sentient\\Blog\\Entity\\BlogPost';
		$app['blog.entity_classes.blog_categories'] = 'Sentient\\Blog\\Entity\\BlogCategory';

		$app['blog.repositories.blog_posts'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['blog.entity_classes.blog_posts']);
		});

		$app['blog.repositories.blog_categories'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['blog.entity_classes.blog_categories']);
		});

		$app['blog.templates.list_posts'] = '@blog/view/list_posts';
		$app['blog.templates.view_post'] = '@blog/view/view_post';

		$app['blog.actions.list_posts'] = $app->share(function() use($app) {
			return new ListPostsAction(
				$app['blog.repositories.blog_posts'],
				$app['blog.repositories.blog_categories'],
				$app['blog.templates.list_posts']
			);
		});

		$app['blog.controllers'] = $app->share(function() use($app) {
			$controllers = $app['controllers_factory'];
			$dispatchListPosts = $app['actions.dispatch']($app['blog.actions.list_posts']);
			$controllers
				->get('/', $dispatchListPosts)
				->bind('blog');
			$controllers
				->get('/categories/{slug}', $dispatchListPosts)
				->beforeMatch(function(array $attr) use($app) {
					$category = $app['blog.repositories.blog_categories']->findOneBy(['slug' => $attr['slug']]);
					if(!$category) {
						return false;
					}
					$attr['category'] = $category;
					return $attr;
				})
				->bind('blog_category');
			$controllers
				->get('/{slug}', function(Request $request) use($app) {
					return $app['twig.view']->render($app['blog.templates.view_post'], ['post' => $request->get('post')]);
				})
				->beforeMatch(function(array $attrs) use($app) {
					$post = $app['blog.repositories.blog_posts']->findOneBy(['slug' => $attrs['slug']]);
					if(!$post) {
						return false;
					}
					$attrs['post'] = $post;
					return $attrs;
				})
				->bind('blog_post');
			return $controllers;
		});

	}

	public function boot(Application $app) {
		$app['blog.repositories.blog_posts'];
		$app['blog.repositories.blog_categories'];
	}

}