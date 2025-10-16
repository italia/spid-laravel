<?php

namespace Italia\SPIDAuth\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Italia\SPIDAuth\SPIDAuth;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    public function testIfSPIDAuthIsAvailable()
    {
        $SPIDAuth = $this->app->make('SPIDAuth');
        $this->assertInstanceOf(SPIDAuth::class, $SPIDAuth);
    }

    public function testIfRoutesExists()
    {
        $allRoutes = Route::getRoutes();
        $spidRoutesNames = [
          'spid-auth_login',
          'spid-auth_do-login',
          'spid-auth_logout',
          'spid-auth_acs',
          'spid-auth_metadata',
          'spid-auth_providers',
        ];

        foreach ($spidRoutesNames as $routeName) {
            $this->assertTrue($allRoutes->hasNamedRoute($routeName));
        }
    }

    public function testIfMiddlewareExists()
    {
        $this->assertArrayHasKey('spid.auth', Route::getMiddleware());
    }

    public function testIfSharedViewDataExists()
    {
        $view = view('spid-auth::spid-button');
        $view->render();
        $data = $view->getData();
        $this->assertArrayHasKey('SPIDActionUrl', $data);
    }

    public function testIfCommandExampleExists()
    {
        $this->assertArrayHasKey('spid-auth:example', Artisan::all());
    }

    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }
}
