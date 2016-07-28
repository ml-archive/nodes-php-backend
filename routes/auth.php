<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/login', 'middleware' => ['web', 'backend.ssl']], function () {
    // Login form
    Route::get('/', [
        'as' => 'nodes.backend.login.form',
        'uses' => 'AuthController@login',
    ]);

    // Authenticate login
    Route::post('/', [
        'as' => 'nodes.backend.login.authenticate',
        'uses' => 'AuthController@authenticate',
    ]);

    // SSO form
    Route::get('/sso', [
        'as' => 'nodes.backend.login.sso',
        'uses' => 'AuthController@sso',
    ]);

    // SSO authenticate
    Route::post('/sso', [
        'as' => 'nodes.backend.login.sso.authenticate',
        'uses' => 'AuthController@ssoAuthenticate',
    ]);

    // Log authenticated user out
    Route::get('/logout', [
        'as' => 'nodes.backend.login.logout',
        'uses' => 'AuthController@logout',
    ]);
});

// Manager auth
Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/manager_auth/', 'middleware' => ['web', 'backend.ssl']], function () {
    // Authenticate Nodes Manager
    Route::post('/', [
        'as' => 'nodes.backend.login.manager',
        'uses' => 'AuthController@manager',
    ]);
});
