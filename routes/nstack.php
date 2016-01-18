<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => '/nstack', 'middleware' => ['backend.ssl', 'backend.auth']], function() {

    // NStack hook
    Route::get('/', [
        'as' => 'nodes.backend.nstack',
        'uses' => 'NStackController@hook'
    ]);
});