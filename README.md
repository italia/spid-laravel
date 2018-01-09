# SPID authentication package for Laravel 5

[![Join the #spid-laravel channel](https://img.shields.io/badge/Slack%20channel-%23spid--laravel-blue.svg?logo=slack&colorB=0066cc)](https://developersitalia.slack.com/messages/C8HCL6UDS/)
[![Get invited](https://img.shields.io/badge/No%20slack%3F-Get%20your%20invite-blue.svg?logo=slack&colorB=0066cc)](https://slack.developers.italia.it/)
[![SPID on forum.italia.it](https://img.shields.io/badge/Forum-SPID-blue.svg?colorB=0066cc)](https://forum.italia.it/c/spid)
[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?colorB=0066cc)](https://github.com/php-pds/skeleton)

**This package is under development and is not suitable for production use.**

This is a package to provide a simple SPID authentication system to web
applications based on [Laravel 5](https://www.laravel.com).

## Installation

1. Before installing this package patching must be enabled in `composer.json`.
This is necessary because
[this patch](https://cdn.rawgit.com/italia/spid-laravel/887c53d1/patches/php-saml-remove_mcrypt-spid.patch)
has to be applied to [onelogin/php-saml](https://github.com/onelogin/php-saml)
for SPID compatibility.

   Edit your `composer.json` like this:
   ```json
   ...
   "extra": {
      "enable-patching": "true"
   },
   ...
   ```
   or simply run:

   `composer config extra.enable-patching true`.

2. Since Laravel 5.5 requires PHP >= 7.0.0, this package depends on the
   [`dev-remove_mcrypt` branch](https://github.com/onelogin/php-saml/tree/remove_mcrypt)
   of onelogin/php-saml because [`mcrypt`](http://php.net/manual/en/intro.mcrypt.php)
   extension is deprecated in PHP 7.1.0 and will be removed in PHP 7.2.0.

   In order to allow composer to properly resolve dependencies the
   `minimum-stability` option must be set to `dev` and the `prefer-stable`
   option must be set to `true` in `composer.json`.

   These options can be set by running:

   ```console
   composer config minimum-stability dev
   composer config prefer-stable true
   ```

   **This installation step will be removed before the first stable release of
   this package.**

3. Require this package with composer.

   `composer require italia/spid-laravel:dev-master`

4. [Exclude the URIs](https://laravel.com/docs/5.5/csrf#csrf-excluding-uris)
used by this package from CSRF protection because the the Identity Providers
can't know what CSRF token include in their POST requests sent to your routes.

   In your `app/Http/Middleware/VerifyCsrfToken.php` set `'/spid/*'` as an
   element of the `$except` array.
   ```php
   <?php

   namespace App\Http\Middleware;

   use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

   class VerifyCsrfToken extends Middleware
   {
       /**
        * The URIs that should be excluded from CSRF verification.
        *
        * @var array
        */
       protected $except = [
           '/spid/*'
       ];
   }
   ```
## Configuration

Publish the configuration with:

```console
php artisan vendor:publish --provider="Italia\SPIDAuth\ServiceProvider"
```

This will create a `spid-auth.php` file in your `config` directory where you can
set these options:

**Service Provider options**

- `sp_entity_id`: Service Provider Entity ID;
- `sp_organization_name`: Service Provider Organization Name;
- `sp_organization_display_name`: Service Provider Organization Display Name;
- `sp_organization_url`: Service Provider Organization URL;
- `sp_requested_attributes`: SPID attributes requested to Identity Providers.

These options must be set accordingly to the official SPID
[technical rules](http://spid-regole-tecniche.readthedocs.io/en/latest/index.html)
and [notices](http://www.agid.gov.it/agenda-digitale/infrastrutture-architetture/spid/avvisi).

The values entered in the config file will be used to generate the SAML Service
Provider metadata at runtime. The generated metadata will be available in XML
format at `/spid/metadata`.

**Application options**

- `middleware_group`:
  the [middleware group](https://laravel.com/docs/5.5/middleware#middleware-groups)
  assigned to the packages routes. The default value is `web` which comes with
  Laravel out of the box and provides some common features like session
  management.
- `routes_prefix`:
  the [route prefix](https://laravel.com/docs/5.5/routing#route-group-prefixes)
  applied to the package routes. If you change the default `spid` value make
  sure to reflect this change in the `app/Http/Middleware/VerifyCsrfToken.php`
  file as described above. *Please note that in this document the value is
  assumed to be `spid`*.
- `login_view`:
  the view to display when the user is redirected by the package `spid.auth`
  middleware (see [Middleware section](#middleware) below). The default view is
  [`spid-auth::login-spid`](https://github.com/italia/spid-laravel/blob/master/resources/views/login-spid.blade.php).
- `after_login_url`:
  the URL to redirect to after a successful login. If the login process is
  triggered by the package `spid.auth` middleware, the user will be redirected
  to the original destination. Default value is `/`.
- `after_logout_url`:
  the URL to redirect to after a successful logout. Default value is `/`.

*Please note that the `php artisan vendor:publish --provider="Italia\SPIDAuth\ServiceProvider"`
command will copy some static assets to your `public` directory. You can publish
configuration and assets separately adding `--tag=spid-config` and
`--tag=spid-assets` options on the command line.*

## Usage

The SPID authentication process is completely agnostic about the authentication
system of your application. If you plan to integrate your authentication system
with SPID, you can listen to the `LoginEvent` and `LogoutEvent` (see
[Events](#events) and [Example](#example)).

### Button

You can display a simple SPID access button by including the
`spid-auth::spid-button` view in your template:

```blade
@include('spid-auth::spid-button')
```

Optionally you can specify the button size (`s`, `m`, `l` or `xl`):

```blade
@include('spid-auth::spid-button', ['size' => 'm'])
```

**Scenario**

1. The user clicks on the button and a list of Identity Provider is displayed;
2. The user choose an Identity Provider and is redirected to the corresponding
   login page;
3. After a successful login the user is redirected to the URL specified in the
   `after_login_url` option and a `LoginEvent` is triggered.

### Middleware

You can assign the `spid.auth` middleware to specific routes like so:

```php
Route::get('private', 'PrivateController@show')->middleware('spid.auth');
```

Or you can assign the `spid.auth` middleware to application controllers:

```php
class PrivateController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('spid.auth');
    }
}
```

**Scenario**

1. The user requests a resource which `spid.auth` middleware is assigned to.
2. The user is redirected to `/spid/login` and the view specified in
   `login_view` option is displayed;
3. The user choose an Identity Provider and is redirected to the corresponding
   login page;
4. After a successful login the user is redirected to the URL of the original
   resource and a `LoginEvent` is triggered.

### Events

`LoginEvent` and `LogoutEvent` can be listened to get some useful information
about the authenticated user. Both events share these methods:
- `getSPIDUser()` returns a `SPIDUser` object filled with the attributes
  specified in the `sp_requested_attributes` option and returned by the
  Identity Provider;
- `getIdp()` returns the entityName of the Identity Provider.

To listen to both events using the same object, you can use an
[Event Subscriber](https://laravel.com/docs/5.5/events#event-subscribers) class
that can be defined as follow:

```php
<?php

namespace App\Listeners;

use Italia\SPIDAuth\Events\LoginEvent;
use Italia\SPIDAuth\Events\LogoutEvent;

class SPIDEventSubscriber
{
    /**
     * Handle SPID login events.
     */
    public function onSPIDLogin($event) {
        // $event->getSPIDUser() and $event->getIdp() are available here
        // your application logic goes here
    }

    /**
     * Handle SPID logout events.
     */
    public function onSPIDLogout($event) {
        // $event->getSPIDUser() and $event->getIdp() are available here
        // your application logic goes here
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Italia\SPIDAuth\Events\LoginEvent',
            'App\Listeners\SPIDEventSubscriber@onSPIDLogin'
        );

        $events->listen(
            'Italia\SPIDAuth\Events\LogoutEvent',
            'App\Listeners\SPIDEventSubscriber@onSPIDLogout'
        );
    }

}
```

The `SPIDEventSubscriber` class must be added to the `$subscribe` array in
`app/Providers/EventServiceProvider.php`:

```php
protected $subscribe = [
    'App\Listeners\SPIDEventSubscriber'
];
```

The `SPIDUser` class provides `<attribute>` properties for the attributes
specified in the `sp_requested_attributes` option (e.g. for the `name` attribute
you find a `SPIDUser->name` property). If the attribute is not available `null`
is returned.

### Logout

Simply provide your users a link pointing to `/spid/logout`.

**Scenario**

1. The user clicks on a link pointing to `/spid/logout`;
2. After a successful logout the user is redirected to the URL specified in the
   `after_logout_url` option and a `LogoutEvent` is triggered.

## Example

This package comes with a simple set of controllers, views and routes that can
be run as an example in a fresh installed Laravel application.

To publish the needed files run the command:

```console
php artisan spid-auth:example
```

This will create the following files:

- `app/Http/Controllers/HomeController.php`
- `app/Http/Controllers/PrivateController.php`
- `app/Listeners/SPIDEventSubscriber.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/home.blade.php`
- `resources/views/private.blade.php`

Next add the `SPIDEventSubscriber` class in
`app/Providers/EventServiceProvider.php` as described above.

To run the example:

```console
php artisan serve
```

and browse to <http://localhost:8000/>.

Open `storage/logs/laravel.log` to read some example informations logged by the
`SPIDEventSubscriber`.

## Notes

### Test Identity Provider

In the
[`spid-idps.php`](https://github.com/italia/spid-laravel/blob/master/config/spid-idps.php)
file are defined the official
[SPID Identity Providers](https://registry.spid.gov.it/identity-providers).
For testing purposes, this file includes also the Test Identity Provider
available at [idp.spid.gov.it:9443](https://idp.spid.gov.it:9443).

**Remove it before using in production**.

### Service Provider certificate and private key

In the
[`spid-saml.php`](https://github.com/italia/spid-laravel/blob/master/config/spid-saml.php)
file are defined the X.509 certificate and the private key of the Service
Provider. Please note that the values provided are only for testing purposes and
can't be used in production.

**Change the values and keep the private key secret**.

The Service Provider is configured to properly work with the Test Identity
Provider using the following credentials:

- username: `laravel.test`
- password: `laravel.test`

### SPID smart button

This package uses the
[SPID Smart Button](https://github.com/italia/spid-smart-button) as proposed in
this [pull request](https://github.com/italia/spid-smart-button/pull/10).

## Licenses

`BSD-3-Clause License` is generally applied to all the code in this repository if not otherwise specified.

`MIT License` is applied to some portions of code as reported in
[this README](https://github.com/italia/spid-laravel/blob/master/src/Console/README.md).

`SIL Open Font License 1.1` is applied to the Titillium font included from CSS files.
