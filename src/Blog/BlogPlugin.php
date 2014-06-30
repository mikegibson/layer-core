<?php

namespace Sentient\Blog;

use Sentient\Action\PaginationAction;
use Sentient\Blog\Node\BlogPostListNode;
use Sentient\Node\ControllerNodeInterface;
use Sentient\Plugin\Plugin;
use Silex\Application;

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

		$app['blog.url_fragment'] = 'blog';

		$app['blog.actions.list_posts'] = $app->share(function() use($app) {
			return new PaginationAction($app['blog.repositories.blog_posts'], '@blog/view/list_posts');
		});

		$app['blog.root_node'] = $app->share(function() use($app) {
			return new BlogPostListNode($app['blog.repositories.blog_posts'], 'app', null, 'blog', $app['blog.title']);
		});

		$app['app.home_node'] = $app->share($app->extend('app.home_node', function(ControllerNodeInterface $node) use($app) {
			$node->wrapChildNode($app['blog.root_node'], $app['blog.url_fragment']);
			return $node;
		}));

	}

	public function boot(Application $app) {
		$app['blog.repositories.blog_posts'];
		$app['blog.repositories.blog_categories'];
	}

}