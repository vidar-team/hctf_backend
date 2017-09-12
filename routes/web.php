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
Route::group(["prefix" => "User", "middleware" => "throttle:60,1"], function () {
//   Route::get("index", "UserController@index");
    Route::post('login', 'TeamController@login');
    Route::post('register', 'TeamController@register');
//    Route::get('token', 'TeamController@refreshToken')->middleware('jwt.refresh');

    Route::group(['middleware' => 'jwt.auth.mod'], function () {
        Route::get('info', 'TeamController@getAuthInfo')->middleware(['jwt.refresh']);
        // Method Need Auth
    });

    Route::group(['middleware' => 'AdminCheck'], function() {
       Route::get('list', 'TeamController@listTeams');
       Route::post('ban', 'TeamController@banTeam');
       Route::post('setAdmin', 'TeamController@setAdmin');
       Route::post('forceResetPassword', 'TeamController@forceResetPassword');
    });
});

Route::group(["prefix" => "Category", "middlewaire" => ""], function(){
   Route::get("list", "CategoryController@list");

    Route::group(['middleware' => 'AdminCheck'], function() {
        Route::post("create", "CategoryController@create");
    });
});
