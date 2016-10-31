<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/backend-users/roles', 'middleware' => ['web', 'backend.ssl', 'backend.auth']], function () {
    // List all roles
    Route::get('/', [
        'as' => 'nodes.backend.users.roles',
        'uses' => 'RolesController@index',
    ]);

    // Create new role
    Route::post('/store', [
        'as' => 'nodes.backend.users.roles.store',
        'uses' => 'RolesController@store',
    ]);

    // Update role
    Route::patch('/{id}/update', [
        'as' => 'nodes.backend.users.roles.update',
        'uses' => 'RolesController@update',
    ])->where('id', '[0-9]+');

    // Delete role
    Route::delete('/{id}/destroy', [
        'as' => 'nodes.backend.users.roles.destroy',
        'uses' => 'RolesController@destroy',
    ])->where('id', '[0-9]+');

    Route::post('/{id}/default', [
        'as' => 'nodes.backend.users.roles.default',
        'uses' => 'RolesController@setDefault',
    ])->where('id', '[0-9]+');
});
