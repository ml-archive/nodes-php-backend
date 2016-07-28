<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table with reset password tokens
    |--------------------------------------------------------------------------
    |
    | The table where we should handle and administrate
    | our reset password tokens. Remember to migrate the table.
    |
    */
    'table' => 'backend_reset_password_tokens',

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
        'name' => 'Backend',
        'email' => 'no-reply@nodes.dk',
    ],

    /*
    |--------------------------------------------------------------------------
    | Subject of e-mail
    |--------------------------------------------------------------------------
    |
    | Enter the subject of which the reset password emails
    | should be sent with.
    |
    */
    'subject' => 'Reset password',

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
        'html' => 'nodes.backend::reset-password.emails.html',
        'text' => 'nodes.backend::reset-password.emails.text',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token lifetime
    |--------------------------------------------------------------------------
    |
    | Set the lifetime of each generated token in minutes.
    | Default: 60 minutes
    |
    */
    'expire' => 60,
];
