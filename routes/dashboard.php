<?php
Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/', 'middleware' => ['web', 'backend.ssl', 'backend.auth']], function() {

    // Dashboard
    Route::get('/', [
        'as' => 'nodes.backend.dashboard',
        'uses' => 'DashboardController@index'
    ]);
});
