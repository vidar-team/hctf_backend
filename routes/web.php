<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'IndexController@index');
Route::group(["prefix" => "User"], function(){
//   Route::get("index", "UserController@index");
    Route::post('login', 'TeamController@login');
    Route::post('register', 'TeamController@register');
    Route::group(['middleware' => 'jwt.auth.mod'], function () {
        Route::get('info', 'TeamController@getAuthInfo');
    });
});
