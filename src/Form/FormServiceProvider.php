<?php

namespace Sentient\Form;

use Silex\Application;

class FormServiceProvider extends \Silex\Provider\FormServiceProvider {

	public function register(Application $app) {

		parent::register($app);

		$app['form.secret'] = $app->share(function() use($app) {
			return md5('form_secret' . $app['config']->read('salt'));
		});

		$app['html_purifier.config'] = $app->share(function() use($app) {
			$config = new \HTMLPurifier_Config(\HTMLPurifier_ConfigSchema::instance());
			$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
			$config->set('HTML.AllowedElements', implode(',', [
				'p', 'ul', 'ol', 'li', 'strong', 'em', 'img', 'sub', 'sup', 'blockquote', 'table', 'thead', 'tbody',
				'tr', 'th', 'td', 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'address', 'br', 'dl', 'dt', 'dd'
			]));
			$config->set('HTML.AllowedAttributes', implode(',', [
				'*.class', 'img.src', 'img.alt', 'a.href', 'a.title', 'td.abbr', 'td.colspan', 'td.rowspan', 'th.abbr',
				'th.colspan', 'th.rowspan', 'table.summary'
			]));
			$config->set('AutoFormat.RemoveEmpty', true);
			$config->set('AutoFormat.AutoParagraph', true);
			$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
			$cachePath = $app['paths.cache'] . '/htmlpurifier';
			if(!is_dir($cachePath)) {
				mkdir($cachePath, 0777, true);
			}
			$config->set('Cache.SerializerPath', $cachePath);
			return $config;
		});

		$app['html_purifier'] = $app->share(function() use($app) {
			return new \HTMLPurifier($app['html_purifier.config']);
		});

		$app['form.html_purifier'] = $app->share(function() use($app) {
			return new HtmlPurifier($app['html_purifier']);
		});

		$app['form.html_extension'] = $app->share(function() use($app) {
			return new HtmlExtension(
				new HtmlType($app['form.html_purifier']),
				new HtmlTypeGuesser($app['annotations.reader'])
			);
		});

		$app['form.extensions'] = $app->share($app->extend('form.extensions', function(array $extensions) use($app) {
			$extensions[] = $app['form.html_extension'];
			return $extensions;
		}));

		$app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function(array $extensions) use($app) {
			$extensions[] = new FormTypeExtension();
			$extensions[] = new DateTypeExtension();
			$extensions[] = new TimeTypeExtension();
			return $extensions;
		}));

	}

}