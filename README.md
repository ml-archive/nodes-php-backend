## Backend

A easy and clean backend to [Laravel](http://laravel.com/docs).

[![Total downloads](https://img.shields.io/packagist/dt/nodes/backend.svg)](https://packagist.org/packages/nodes/backend)
[![Monthly downloads](https://img.shields.io/packagist/dm/nodes/backend.svg)](https://packagist.org/packages/nodes/backend)
[![Latest release](https://img.shields.io/packagist/v/nodes/backend.svg)](https://packagist.org/packages/nodes/backend)
[![Open issues](https://img.shields.io/github/issues/nodes-php/backend.svg)](https://github.com/nodes-php/backend/issues)
[![License](https://img.shields.io/packagist/l/nodes/backend.svg)](https://packagist.org/packages/nodes/backend)
[![Star repository on GitHub](https://img.shields.io/github/stars/nodes-php/backend.svg?style=social&label=Star)](https://github.com/nodes-php/backend/stargazers)
[![Watch repository on GitHub](https://img.shields.io/github/watchers/nodes-php/backend.svg?style=social&label=Watch)](https://github.com/nodes-php/backend/watchers)
[![Fork repository on GitHub](https://img.shields.io/github/forks/nodes-php/backend.svg?style=social&label=Fork)](https://github.com/nodes-php/backend/network)

## 📝 Introduction
One thing we at [Nodes](http://nodesagency.com) have been missing in [Laravel](http://laravel.com/docs) is a fast implemented backend which is easy to build on top of

## 📦 Installation

To install this package you will need:

* Laravel 5.1+
* PHP 5.5.9+

You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```
"require": {
    "nodes/backend": "^1.0"
}
```

Or you can run the composer require command from your terminal.

```
composer require nodes/backend
```
## 🔧 Setup

Setup service providers in config/app.php

```
Nodes\Backend\ServiceProvider::class,
Nodes\Assets\ServiceProvider::class,
Nodes\Validation\ServiceProvider::class,
Nodes\Cache\ServiceProvider::class,
Collective\Html\HtmlServiceProvider::class,
```

Setup alias in config/app.php

```
'NodesBackend'   => Nodes\Backend\Support\Facades\Backend::class, 
'Input'          => Illuminate\Support\Facades\Input::class,
'Form'           => Collective\Html\FormFacade::class,
'Html'           => Collective\Html\HtmlFacade::class,
```

Publish config file all config files at once, 
```
php artisan vendor:publish
```

Publish config file for backend plugin only
```
php artisan vendor:publish --provider="Nodes\Backend\ServiceProvider"
```

Overwrite config file for backend plugin only
```
php artisan vendor:publish --provider="Nodes\Backend\ServiceProvider" --force
```

Add following to your /database/seeds/DatabaseSeeder.php
```
$this->call('NodesBackendSeeder');
```

Now you can call php artisan migrate --seed
Which will add the new tables and seed the roles/users to get going

Add to config/nodes/autoload.php
```
'project/Routes/Backend/',
```

Run bower, npm & gulp to build css & js
```
bower install && npm install && gulp build
```

Set up CSRF by pass in App\Http\Middleware\VerifyCsrfToken.php

```
    protected $except = [
        'admin/manager_auth',
    ];
```

Set up TokenMissMatch by pass in App\Exceptions\Handler.php

```
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            return redirect()->back()->with('error', 'Token miss match, try again')->send();
        }
        
        ....
    }
```

## ⚙ Usage

Commands

```
nodes:backend:assets                   Copy backend assets to public folder
nodes:backend:install                  Install Nodes Backend within your project
nodes:backend:routes                   Copy backend routes to project folder
nodes:backend:tools                    Install backend tools to compile CSS and Javascript.
nodes:backend:views                    Copy backend views to project folder
```

Global function
```
backend_auth - Access all other function on mananger
backend_user - Retrieve user object
backend_user_check - Check if there is authed user
backend_user_authenticate - Try to auth with current request, pass [] as providers are registered
backend_user_login - Force login another user
backend_user_logout - Logout user
backend_attempt - Attempt to authenticate a user using the given credentials
query_restorer - Use to restore query params from cookie, handy for routing between views with queries
backend_router - Access all other router functions
backend_router_pattern - Used fx for selecting navigation item by path 
backend_router_alias - Used fx for selecting navigation item by route
```

## 🏆 Credits

This package is developed and maintained by the PHP team at [Nodes](http://nodesagency.com)

[![Follow Nodes PHP on Twitter](https://img.shields.io/twitter/follow/nodesphp.svg?style=social)](https://twitter.com/nodesphp) [![Tweet Nodes PHP](https://img.shields.io/twitter/url/http/nodesphp.svg?style=social)](https://twitter.com/nodesphp)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
