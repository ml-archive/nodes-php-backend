<?php
Route::group(['namespace' => 'Nodes\Backend\Http\Controllers', 'prefix' => 'admin/failed-jobs', 'middleware' => ['ssl', 'backend']], function() {

    // List of all failed jobs
    Route::get('/', [
        'as' => 'nodes.backend.failed-jobs',
        'uses' => 'FailedJobsController@index'
    ]);

    // Restart one
    Route::post('/{id}/restart', [
        'as' => 'nodes.backend.failed-jobs.restart',
        'uses' => 'FailedJobsController@restart'
    ])->where('id', '[0-9]+');

    // Restart all
    Route::post('/restart-all', [
        'as' => 'nodes.backend.failed-jobs.restart-all',
        'uses' => 'FailedJobsController@restartAll'
    ]);

    // Forget one
    Route::post('/{id}/forget', [
        'as' => 'nodes.backend.failed-jobs.forget',
        'uses' => 'FailedJobsController@forget'
    ])->where('id', '[0-9]+');
});
