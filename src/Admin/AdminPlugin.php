<?php

namespace Layer\Admin;

use Assetic\AssetManager;
use Layer\Admin\Controller\CmsController;
use Layer\Admin\View\AdminHelper;
use Layer\Admin\View\TwigAdminExtension;
use Layer\Asset\AssetFactory;
use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminProvider
 *
 * @package Layer\Admin\Provider
 */
class AdminPlugin extends Plugin {

    protected $name = 'admin';

    protected $depends = ['data'];

    /**
     * @param Application $app
     */
    public function register() {

        $app = $this->app;

        $app['admin.controllers'] = $app->share(function () use ($app) {

            $admin = $app['controllers_factory'];

            $admin->match('/{namespace}/{type}/{action}', 'admin.controllers.cms_controller:dispatch')
                ->before(function (Request $request) use ($app) {

                    // @todo Find a way to reject a URL match by closure
                    if (
                        !($namespace = $request->get('namespace')) ||
                        !($type = $request->get('type')) ||
                        !($dataType = $app['data']->get($namespace . '/' . $type))
                    ) {
                        $app->abort(404);
                    }

                    $request->attributes->add(compact('dataType'));

                })->bind('admin_scaffold');

            return $admin;

        });

        $app['admin.controllers.cms_controller'] = $app->share(function () use ($app) {

            return new CmsController($app);

        });

        $app['admin.helper'] = $app->share(function () use ($app) {

            return new AdminHelper($app);

        });

        $app['twig'] = $app->share(
            $app->extend('twig', function (\Twig_Environment $twig) use ($app) {

                $twig->addExtension(new TwigAdminExtension($app['admin.helper']));

                return $twig;

            })
        );

        $app['assets.js_admin'] = $app->share(function() use($app) {
            $asset = $app['assetic.factory']->createAsset([
                '@layer/js/jquery.js',
                '@admin/js/admin.js'
            ], [
                '?uglifyjs'
            ], [
                'output' => 'js/admin/admin.js'
            ]);
            return $asset;
        });

        $app['assets.css_admin'] = $app->share(function() use($app) {
            $asset = $app['assetic.factory']->createAsset([
                '@admin/scss/admin.scss'
            ], [
                'compass',
                '?uglifycss'
            ], [
                'output' => 'css/admin/admin.css'
            ]);
            return $asset;
        });

    }

    public function boot() {

        $this->app['assetic.asset_manager']->set('js_admin', $this->app['assets.js_admin']);
        $this->app['assetic.asset_manager']->set('css_admin', $this->app['assets.css_admin']);

    }

}