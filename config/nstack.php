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
    'url' => 'https://nstack.io/hook/attempt',

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
        'appId' => null,
        'masterKey' => null
    ],

    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    |
    | The role they users should get in NStack in the company the app belongs to
    | Possible is user, admin
    |
    */
    'role' => 'user',
];
