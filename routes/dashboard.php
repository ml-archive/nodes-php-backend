<?php
Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/', 'middleware' => ['ssl', 'backend']], function() {

    // Dashboard
    Route::get('/', [
        'as' => 'nodes.backend.dashboard',
        'uses' => 'DashboardController@index'
    ]);
});
