<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/backend-users', 'middleware' => ['web', 'backend.ssl', 'backend.auth']], function () {
    // List all users
    Route::get('/', [
        'as' => 'nodes.backend.users',
        'uses' => 'UsersController@index',
    ]);

    // Create new user form
    Route::get('/create', [
        'as' => 'nodes.backend.users.create',
        'uses' => 'UsersController@create',
    ]);

    // Save new user
    Route::post('/store', [
        'as' => 'nodes.backend.users.store',
        'uses' => 'UsersController@store',
    ]);

    // Edit user form
    Route::get('/{id}/edit', [
        'as' => 'nodes.backend.users.edit',
        'uses' => 'UsersController@edit',
    ])->where('id', '[0-9]+');

    // Update user
    Route::patch('/update', [
        'as' => 'nodes.backend.users.update',
        'uses' => 'UsersController@update',
    ]);

    // Edit authenticated user
    Route::get('/profile', [
        'as' => 'nodes.backend.users.profile',
        'uses' => 'UsersController@profile',
    ]);

    // Delete user
    Route::delete('/delete/{id}', [
        'as' => 'nodes.backend.users.destroy',
        'uses' => 'UsersController@delete',
    ])->where('id', '[0-9]+');

    // Change password
    Route::get('/change-password', [
        'as' => 'nodes.backend.users.change-password',
        'uses' => 'UsersController@changePassword',
    ]);

    // Update users password
    Route::patch('/update-password', [
        'as' => 'nodes.backend.users.update-password',
        'uses' => 'UsersController@updatePassword',
    ]);
});
