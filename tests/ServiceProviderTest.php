<?php

namespace Italia\SPIDAuth\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase;
use Italia\SPIDAuth\SPIDAuth;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Italia\SPIDAuth\ServiceProvider'];
    }
    
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
          'spid-auth_providers'
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
        $this->assertArrayHasKey('SPIDActionUrl', View::getShared());
    }
    
    public function testIfCommandExampleExists()
    {
        $this->assertArrayHasKey('spid-auth:example', Artisan::all());
    }
}
