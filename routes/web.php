<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the 'web' middleware group. Now create something great!
|
*/

Route::get('/', 'IndexController@index');
Route::group(['prefix' => 'User'], function () {
    Route::group(['middleware' => 'throttle:60,1'], function(){
        Route::post('login', 'TeamController@login');
        //Route::post('register', 'TeamController@register');
    });
    Route::get('select', 'TeamController@publicListTeams');
    Route::get('ranking', 'TeamController@getRanking');

    Route::group(['middleware' => 'jwt.auth.mod'], function () {
        Route::get('info', 'TeamController@getAuthInfo')->middleware(['jwt.refresh']);
        // Method Need Auth
    });

    Route::group(['middleware' => ['jwt.auth.mod', 'AdminCheck']], function () {
        Route::get('list', 'TeamController@listTeams');
        Route::post('ban', 'TeamController@banTeam');
        Route::post('unban', 'TeamController@unbanTeam');
        Route::post('setAdmin', 'TeamController@setAdmin');
        Route::post('forceResetPassword', 'TeamController@forceResetPassword');
    });
});

Route::group(['prefix' => 'Category', 'middlewaire' => ''], function () {
    Route::group(['middleware' => ['jwt.auth.mod', 'AdminCheck']], function () {
        Route::get('list', 'CategoryController@list');
        Route::post('create', 'CategoryController@create');
        Route::post('deleteCategory', 'CategoryController@deleteCategory');
    });
});

Route::group(['prefix' => 'Level'], function () {
    Route::group(['middleware' => ['jwt.auth.mod', 'AdminCheck']], function () {
        Route::get('info', 'LevelController@info');
        Route::post('setName', 'LevelController@setName');
        Route::post('setReleaseTime', 'LevelController@setReleaseTime');
        Route::post('setRules', 'LevelController@setRules');
        Route::post('deleteLevel', 'LevelController@deleteLevel');
        Route::post('create', 'LevelController@create');
    });
});

Route::group(['prefix' => 'Challenge'], function () {
    Route::group(['middleware' => ['jwt.auth.mod', 'AdminCheck']], function () {
        Route::post('create', 'ChallengeController@create');
        Route::get('info', 'ChallengeController@info');
        Route::post('edit', 'ChallengeController@editChallenge');
        Route::get('getFlags', 'ChallengeController@getFlagsInfo');
        Route::post('deleteFlags', 'ChallengeController@deleteAllFlags');
        Route::post('addFlags', 'ChallengeController@addFlags');
        Route::post('resetScore', 'ChallengeController@resetScore');
        Route::post('delete', 'ChallengeController@deleteChallenge');
    });

    Route::group(['middleware' => ['jwt.auth.mod', 'TimeCheck']], function () {
        Route::get('list', 'ChallengeController@list');
    });

    Route::group(['middleware' => ['jwt.auth.mod', 'TimeCheck', 'BlockCheck']], function(){
       Route::post('submitFlag', 'ChallengeController@submitFlag');
    });
});

Route::group(['prefix' => 'Flag', 'middleware' => ['jwt.auth.mod', 'AdminCheck']], function(){
   Route::post('delete', 'FlagController@deleteFlag');
   Route::post('edit', 'FlagController@editFlag');
});

Route::group(['prefix' => 'SystemLog', 'middleware' => ['jwt.auth.mod', 'AdminCheck']], function(){
   Route::get('list', 'SystemLogController@list');
});

Route::group(['prefix' => 'System'], function(){
   Route::get('meta', 'SystemController@getMetaInfo');
});