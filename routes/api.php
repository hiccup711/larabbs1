<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    // 短信验证码
    Route::post('verificationCodes', 'Api\VerificationCodesController@store')
        ->name('verificationCodes.store');

    Route::post('users', 'Api\UsersController@store')->name('users.store');
});
