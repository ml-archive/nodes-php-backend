<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL of NStack
    |--------------------------------------------------------------------------
    |
    | Url of NStacks hook
    |
    */
    'url'         => 'https://nstack.io/deeplink',

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Your application credentials on NStack. These are required
    | to perform any kinds of actions with NStack.
    |
    */
    'credentials' => [
        'default' => [
            'appId'     => null,
            'masterKey' => null,
        ],
    ],
    /*
       |--------------------------------------------------------------------------
       | Defaults
       |--------------------------------------------------------------------------
       |
       | Default values regarding nstack
       |
       */
    'defaults'    => [
        'application' => 'default',
    ],
];
