<?php

Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => '/'], function() {

    // Landing page when landing on the root domain,
    // If this does not work, it might be cause /app/Http/routes.php has it by default also
    Route::get('/', [
        'as' => 'nodes.backend',
        'uses' => 'AuthController@login'
    ]);
});