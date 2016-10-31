<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    |
    | The user model that should be used for authentication.
    |
    | If you in a project needs to extend the nodes.backend.backend user model,
    | you need to change this to the namespace of your custom nodes.backend.backend user model.
    |
    */
    'model' => 'Nodes\Backend\Models\User\User',

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    |
    | The user repository that should be used for authentication
    |
    | If you in a project needs to extend the nodes.backend.backend user repository,
    | you need to change this to the namespace of your custom nodes.backend.backend user repository.
    |
    */
    'repository' => 'Nodes\Backend\Models\User\UserRepository',

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Routes used within authentication. I.e. where should the user be redirected
    | to when he/her is successfully authenticated.
    |
    | Note: Values needs to be route aliases.
    |
    */
    'routes' => [
        'success' => 'nodes.backend.dashboard',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gates
    |--------------------------------------------------------------------------
    |
    | There is defined a handful of gates for the standard backend
    |
    | Note: Setting this to false, means you have to define gates your self, or remove all the gate checks
    |
    */
    'gates' => [
        'define' => true,
    ],
    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | The authentication providers that should be used when attempting to
    | authenticate an incoming API request.
    |
    */
    'providers' => [
        'token' => function ($app) {
            return new Nodes\Backend\Auth\Providers\Token;
        },
        'session' => function ($app) {
            return new Nodes\Backend\Auth\Providers\Session($app['nodes.backend.auth.model'], app(\Illuminate\Session\Store::class));
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Token provider
    |--------------------------------------------------------------------------
    |
    | Settings needed by the token provider.
    |
    */
    'token' => [

        /*
        |--------------------------------------------------------------------------
        | Database table
        |--------------------------------------------------------------------------
        |
        | Name of database where access tokens are located.
        |
        */
        'table' => 'backend_user_tokens',

        /*
        |--------------------------------------------------------------------------
        | Columns
        |--------------------------------------------------------------------------
        |
        | Mapping of columns. These are needed to reference owner of token,
        | the unique token it self and the expire time of token.
        |
        */
        'columns' => [
            'user_id' => 'user_id',
            'token' => 'token',
            'expire' => 'expire',
        ],

        /*
        |--------------------------------------------------------------------------
        | Lifetime
        |--------------------------------------------------------------------------
        |
        | Set the lifetime of a token. This used by used as a "literal" time.
        | I.e. "+1 week" or "+1 month".
        |
        | @see http://php.net/manual/en/datetime.formats.relative.php
        |
        */
        'lifetime' => null,
    ],
];
