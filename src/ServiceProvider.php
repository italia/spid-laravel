<?php
/**
 * This class implements a Laravel Service Provider for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot(Router $router)
    {
        $configAuth = dirname(__DIR__) . '/config/spid-auth.php';
        $configSAML = dirname(__DIR__) . '/config/spid-saml.php';
        $configIdps = dirname(__DIR__) . '/config/spid-idps.php';
        $assets = dirname(__DIR__) . '/resources/assets';

        $this->mergeConfigFrom($configAuth, 'spid-auth');
        $this->mergeConfigFrom($configSAML, 'spid-saml');
        $this->mergeConfigFrom($configIdps, 'spid-idps');

        $this->loadRoutesFrom(dirname(__DIR__) . '/routes/spid-auth.php');
        $this->loadViewsFrom(dirname(__DIR__) . '/resources/views', 'spid-auth');
        $this->loadTranslationsFrom(dirname(__DIR__) . '/resources/lang', 'spid-auth');

        $this->publishes([$configAuth => config_path('spid-auth.php')], 'spid-config');
        $this->publishes([$assets => public_path('vendor/spid-auth')], 'spid-assets');

        $router->aliasMiddleware('spid.auth', \Italia\SPIDAuth\Middleware::class);

        View::share('SPIDActionUrl', route('spid-auth_do-login'));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('SPIDAuth', function ($app) {
            return new SPIDAuth();
        });

        $this->commands(\Italia\SPIDAuth\Console\CommandExample::class);
    }
}
