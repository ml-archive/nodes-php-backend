<?php
Route::group(['prefix' =>'/admin/login/reset'], function() {

    // Generate reset password token
    Route::post('/', [
        'uses' => 'ResetPasswordController@generateResetToken',
        'as' => 'nodes.backend.reset-password.token'
    ]);

    // Request reset password token form
    Route::get('/', [
        'uses' => 'ResetPasswordController@index',
        'as' => 'nodes.backend.reset-password.form'
    ]);

    // Confirmation page of e-mail has been sent
    Route::get('/sent', [
        'uses' => 'ResetPasswordController@sent',
        'as' => 'nodes.backend.reset-password.sent'
    ]);

    // Reset password form
    Route::get('/{token}', [
        'uses' => 'ResetPasswordController@resetForm',
        'as' => 'nodes.backend.reset-password.reset'
    ])->where('token', '[[:alnum:]]{64}');

    // Change password
    Route::post('/update', [
        'uses' => 'ResetPasswordController@resetPassword',
        'as' => 'nodes.backend.reset-password.change'
    ]);

    // Reset password done
    Route::get('/done', [
        'uses' => 'ResetPasswordController@done',
        'as' => 'nodes.backend.reset-password.done'
    ]);
});
