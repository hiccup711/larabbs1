<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function(){
    Route::get('version', function (){
        return 'This is api v1';
    })->name('version');
}); 
