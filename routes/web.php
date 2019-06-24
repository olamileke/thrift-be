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

$router->group(['middleware'=>'jwt.auth'], function() use ($router) {

	$router->get('/api/dashboard/current-details', 'DashboardController@fetchCurrentDetails');
	$router->post('/api/expense/add', 'ExpensesController@add');
});

$router->post('/api/signup', 'UsersController@register');
$router->post('/api/account/activate/{token}', 'UsersController@activateAccount');
$router->post('/api/login', 'AuthController@authenticate');


