<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function () {
//    登录相关
    Route::middleware('throttle:'. config('api.rate_limits.sign'))->group(function (){
//      短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store')
            ->name('verificationCodes.store');
//      用户注册
        Route::post('users', 'UsersController@store')->name('users.store');
//        短信验证码
        Route::post('captchas', 'CaptchasController@store')->name('captchas.store');
//        第三方登录
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->where('social_type', 'wechat')
            ->name('socials.authorizations.store');
    });

//    访问相关
    Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function (){

    });
});
