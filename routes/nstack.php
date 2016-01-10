<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => '/nstack'], function() {

    // NStack hook
    Route::get('/', [
        'as' => 'nodes.backend.nstack',
        'uses' => 'NStackController@hook'
    ]);
});