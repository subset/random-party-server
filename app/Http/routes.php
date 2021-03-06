<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', ['as' => 'hello', 'uses' => 'App\Http\Controllers\IndexController@index']);

$app->group(['prefix' => 'occasions'], function($app) {
	$app->get('/', ['as' => 'occasions.index', 'uses' => 'App\Http\Controllers\OccasionsController@index']);
});
