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

Route::group(['middleware' => ['cors', 'throttle:60,1']], function () use ($router) {
    $router->get('/', 'HomeController@index');
    $router->get('/music/{song_id}', 'HomeController@redirectMusicUrl');
});

Route::group(['middleware' => ['cors']], function () use ($router) {
    $router->get('/count', 'HomeController@getCount');
});
