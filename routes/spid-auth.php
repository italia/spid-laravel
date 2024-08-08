<?php
/**
 * This file contains the routes needed for SPIDAuth Package.
 *
 * @license BSD-3-clause
 */
Route::group([
        'prefix' => config('spid-auth.routes_prefix'),
        'middleware' => config('spid-auth.middleware_group'),
    ], function () {
        Route::get('login', [
            'as' => 'spid-auth_login',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@login',
        ]);
        Route::post('login', [
            'as' => 'spid-auth_do-login',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@doLogin',
        ]);
        Route::match(['get', 'post'], 'logout', [
            'as' => 'spid-auth_logout',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@logout',
        ]);
        Route::post('acs', [
            'as' => 'spid-auth_acs',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@acs',
        ]);
        Route::get('metadata', [
            'as' => 'spid-auth_metadata',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@metadata',
        ]);
        Route::get('providers', [
            'as' => 'spid-auth_providers',
            'uses' => 'Italia\SPIDAuth\SPIDAuth@providers',
        ]);
    }
);
