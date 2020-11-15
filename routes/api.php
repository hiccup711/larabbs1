<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function () {
//    登录相关
    Route::middleware('throttle:'. config('api.rate_limits.sign'))->group(function (){
//      短信验证码
        Route::post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
//      用户注册
        Route::post('users', 'UsersController@store')->name('api.users.store');
//        短信验证码
        Route::post('captchas', 'CaptchasController@store')->name('api.captchas.store');
//        第三方登录
        Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
            ->where('social_type', 'wechat')
            ->name('api.socials.authorizations.store');
//        登录
        Route::post('authorizations', 'AuthorizationsController@store')
            ->name('api.authorizations.store');
//        刷新Token
        Route::put('authorizations/current', 'AuthorizationsController@update')
            ->name('api.authorizations.update');
//        删除Token
        Route::delete('authorizations/current', 'AuthorizationsController@destroy')
            ->name('api.authorizations.destroy');
    });

//    访问相关
    Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function (){
//        游客可以访问的接口
        Route::get('users/{user}', 'UsersController@show')->name('users.show');

//        登录后可以访问的接口
        Route::middleware('auth:api')->group(function (){
            Route::get('user', 'UsersController@me')->name('user.show');
        });
    });
});
