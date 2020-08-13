<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// DOWNLOAD
Route::get('/avatar/{page}/{file}', 'DownloadController@avatar')->where('page', '[0-9]+');
Route::get('/media/{page}/{post}/{file}', 'DownloadController@media')->where(['page' => '[0-9]+', 'post' => '[0-9]+']);

// AUTHORIZATION
Route::prefix('auth')->group(function () {
    // Unlogged
    Route::post('register', 'Auth\AuthController@store');
    Route::post('login', 'Auth\AuthController@login');
    Route::post('forgotpassword', 'Auth\AuthController@forgotPassword');

    // Logged
    Route::middleware('auth:api')->group(function () {
        Route::get('infos', 'Auth\AuthController@infos');
        Route::get('user', 'Auth\AuthController@user');
        Route::put('pro', 'Auth\AuthController@pro');
        Route::delete('/{user}', 'Auth\AuthController@destroy')->where('user', '[0-9]+');
        Route::post('forcedelete', 'Auth\AuthController@forcedestroy');
        Route::put('punish/{user}', 'Auth\AuthController@punish')->where('user', '[0-9]+');
    });
});

// PAGES
Route::get('/favorites/{qty}/{posts}/{index?}/{whereIn?}', 'PageController@favorites')->where(['qty' => '[0-9]+', 'posts' => '[0-9]+', 'index' => '[0-9]+', 'whereIn' => '[0-9+,?]+']);
Route::get('/recommended/{qty}/{posts}/{index?}/{whereNotIn?}', 'PageController@recommended')->where(['qty' => '[0-9]+', 'posts' => '[0-9]+', 'index' => '[0-9]+', 'whereNotIn' => '[0-9+,?]+']);

Route::prefix('page')->group(function () {
    // Unlogged
    Route::get('/{page}/{posts?}', 'PageController@show')->where(['page' => '[0-9]+', 'posts' => '[0-9]+']);
    Route::get('/{page}/posts/{qty}/{index?}', 'PostController@index')->where(['page' => '[0-9]+', 'qty' => '[0-9]+', 'index' => '[0-9]+']);

    // Logged
    Route::middleware('auth:api')->group(function () {
        Route::post('/', 'PageController@store');
        Route::put('/{page}', 'PageController@update')->where('page', '[0-9]+');
        Route::delete('/{page}', 'PageController@destroy')->where('page', '[0-9]+');
    });
});

// POSTS
Route::prefix('post')->group(function () {
    // Unlogged
    Route::get('/{post}', 'PostController@show')->where('post', '[0-9]+');
    Route::get('/search/{q}/{qty}/{index?}', 'PostController@search')->where(['qty' => '[0-9]+', 'index' => '[0-9]+']);

    // Logged
    Route::middleware('auth:api')->group(function () {
        Route::post('/page/{page}', 'PostController@store')->where('page', '[0-9]+');
        Route::delete('/{post}', 'PostController@destroy')->where('post', '[0-9]+');
    });
});

// FOLLOWING
Route::prefix('following')->group(function () {
    // Unlogged
    Route::get('/page/{page}', 'FollowingController@index')->where('page', '[0-9]+');
    Route::get('/user/{user}', 'FollowingController@show')->where('user', '[0-9]+');

    // Logged
    Route::middleware('auth:api')->group(function () {
        Route::get('/notifications', 'FollowingController@notifications');
        Route::post('/{page}', 'FollowingController@store')->where('page', '[0-9]+');
        Route::put('/{page}', 'FollowingController@update')->where('page', '[0-9]+');
        Route::delete('/{page}', 'FollowingController@destroy')->where('page', '[0-9]+');
    });
});

// DENOUNCES
Route::prefix('denounce')->group(function () {
    // Unlogged
    Route::post('/page/{page}', 'DenounceController@store')->where('page', '[0-9]+');

    // Logged
    Route::middleware('auth:api')->group(function () {
        Route::get('/{qty}/{index}', 'DenounceController@index')->where(['qty' => '[0-9]+', 'index' => '[0-9]+']);
        Route::get('/{denounce}', 'DenounceController@show')->where('denounce', '[0-9]+');
        Route::delete('/{denounce}', 'DenounceController@destroy')->where('denounce', '[0-9]+');
    });
});

// NOTIFICATIONS
Route::middleware('auth:api')->prefix('notification')->group(function () {
    Route::get('/{qty}/{index}', 'NotificationController@index')->where(['qty' => '[0-9]+', 'index' => '[0-9]+']);
    Route::get('/{notification}', 'NotificationController@show')->where('notification', '[0-9]+');
    Route::get('/me', 'NotificationController@me');
    Route::get('/user/{user}', 'NotificationController@user')->where('user', '[0-9]+');
    Route::post('/user/{user?}', 'NotificationController@store')->where('user', '[0-9]+');
    Route::put('/{notification}/readed', 'NotificationController@readed')->where('notification', '[0-9]+');
    Route::delete('/{notification}', 'NotificationController@destroy')->where('notification', '[0-9]+');
    Route::delete('/force/{notification}', 'NotificationController@forcedestroy')->where('notification', '[0-9]+');
});
