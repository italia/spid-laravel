<?php

/**
 * This class implements a Laravel Service Provider for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */

namespace Italia\SPIDAuth;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Italia\SPIDAuth\Contracts\TransactionStoreContract;
use Italia\SPIDAuth\Events\SPIDAuthenticationRequestEvent;
use Italia\SPIDAuth\Events\SPIDAuthenticationResponseEvent;
use Italia\SPIDAuth\Helpers\TransactionLogHelper;
use Italia\SPIDAuth\Listeners\QueuedTransactionLogListener;
use Italia\SPIDAuth\Listeners\TransactionLogListener;
use Italia\SPIDAuth\TransactionStore\DatabaseTransactionStore;
use Italia\SPIDAuth\TransactionStore\LogTransactionStore;
use RuntimeException;

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
        $migrationStub = dirname(__DIR__) . '/database/migrations/create_spid_transactions_table.php.stub';

        $this->mergeConfigFrom($configAuth, 'spid-auth');
        $this->mergeConfigFrom($configSAML, 'spid-saml');
        $this->mergeConfigFrom($configIdps, 'spid-idps');

        $this->loadRoutesFrom(dirname(__DIR__) . '/routes/spid-auth.php');
        $this->loadViewsFrom(dirname(__DIR__) . '/resources/views', 'spid-auth');
        $this->loadTranslationsFrom(dirname(__DIR__) . '/resources/lang', 'spid-auth');

        $this->publishes([$configAuth => config_path('spid-auth.php')], 'spid-config');
        $this->publishes([$assets => public_path('vendor/spid-auth')], 'spid-assets');
        $this->publishes([
            $migrationStub => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_spid_transactions_table.php'),
        ], 'spid-migrations');

        $router->aliasMiddleware('spid.auth', Middleware::class);

        View::composer('*', function ($view) {
            $view->with('SPIDActionUrl', route('spid-auth_do-login'));
        });

        // Register transaction log listener if enabled
        if (TransactionLogHelper::isEnabled()) {
            $listenerClass = config('spid-auth.transaction_log.queue', false)
                ? QueuedTransactionLogListener::class
                : TransactionLogListener::class;

            Event::listen(
                [SPIDAuthenticationRequestEvent::class, SPIDAuthenticationResponseEvent::class],
                $listenerClass
            );
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('SPIDAuth', function ($app) {
            return new SPIDAuth();
        });

        // Bind TransactionStoreContract to implementation based on driver config
        $this->app->bind(TransactionStoreContract::class, function ($app) {
            $driver = config('spid-auth.transaction_log.driver', 'database');

            return match ($driver) {
                'database' => new DatabaseTransactionStore(),
                'log' => new LogTransactionStore(),
                default => throw new RuntimeException("Unsupported transaction log driver: {$driver}")
            };
        });

        $this->commands([
            Console\CommandExample::class,
            Console\SPIDPruneTransactionsCommand::class,
            Console\SPIDTransactionStatsCommand::class,
        ]);
    }
}
