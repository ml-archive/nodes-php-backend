<?php
return [
    /*
    |--------------------------------------------------------------------------
    | E-mail sender details
    |--------------------------------------------------------------------------
    |
    | Enter the name and e-mail of which the reset password emails
    | should be sent as.
    |
    */
    'from' => [
        'name' => 'Riide',
        'email' => 'no-reply@nodes.dk'
    ],

    /*
    |--------------------------------------------------------------------------
    | Route
    |--------------------------------------------------------------------------
    |
    | Route which will be put in the mail
    |
    */
    'route' => 'nodes.backend.login.form',

    /*
    |--------------------------------------------------------------------------
    | Subject of e-mail
    |--------------------------------------------------------------------------
    |
    | Enter the subject of which the reset password emails
    | should be sent with.
    |
    */
    'subject' => 'Welcome to Riide backend',

    /*
    |--------------------------------------------------------------------------
    | E-mail templates
    |--------------------------------------------------------------------------
    |
    | Set the view path to e-mail templates, that will be used
    | to generate the reset password e-mails
    |
    */
    'views' => [
        'html' => 'nodes.backend::backend-users.emails.html',
        'text' => 'nodes.backend::backend-users.emails.text'
    ],
];