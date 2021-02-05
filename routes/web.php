<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/login','UserController@login');
$router->post('/register', 'UserController@register');
$router->post('/logout','UserController@logout');
$router->post('/reset', 'UserController@sendResetToken');
$router->put('/reset/{token}', 'UserController@verivyResetPassword');

$router->group(['middleware' => 'auth'], function() use ($router) {
	$router->get('/categories/{id}', 'CategoryController@detail');
	$router->get('/categories', 'CategoryController@index');
	$router->post('/categories', 'CategoryController@store');
	$router->put('/categories/{id}', 'CategoryController@update');
	$router->delete('/categories/{id}', 'CategoryController@destroy');

	$router->get('/fleet/{id}', 'FleetController@detail');
	$router->get('/fleet', 'FleetController@index');
	$router->post('/fleet', 'FleetController@store');
	$router->put('/fleet/{id}', 'FleetController@update');
	$router->delete('/fleet/{id}', 'FleetController@destroy');

	$router->get('/profile', 'UserController@profile');
	$router->get('/users/{id}', 'UserController@view');
	$router->get('/all-users', 'UserController@list');
	$router->get('/users', 'UserController@index');
	$router->post('/users', 'UserController@store');
	$router->put('/users/{id}', 'UserController@update');
	$router->delete('/users/{id}', 'UserController@destroy');

	$router->get('/customers/{id}', 'CustomerController@view');
	$router->get('/customers', 'CustomerController@index');
	$router->post('/customers', 'CustomerController@store');
	$router->put('/customers/{id}', 'CustomerController@update');
	$router->delete('/customers/{id}', 'CustomerController@destroy');
});