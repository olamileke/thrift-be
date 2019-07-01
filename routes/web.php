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
	$router->post('/api/expenses/add', 'ExpensesController@add');
	$router->get('/api/expenses/daily/{day}','ExpensesController@fetchDaily');
	$router->get('/api/expenses/monthly/{month}/{year}', 'ExpensesController@fetchMonthly');
	$router->get('/api/singlePeriod/{from}/{to}', 'ExpensesController@singlePeriodAnalysis');
	$router->get('/api/comparison/{period1Start}/{period1End}/{period2Start}/{period2End}', 'ExpensesController@comparison');
	$router->get('/api/expenses/search/{searchTerm}', 'ExpensesController@search');
	$router->get('/api/dashboard/fetch-report-purchases/{from}/{to}', 'DashboardController@fetchReports');
});

$router->post('/api/signup', 'UsersController@register');
$router->post('/api/account/activate/{token}', 'UsersController@activateAccount');
$router->post('/api/login', 'AuthController@authenticate');
$router->post('/api/sendpasswordresetmail', 'AuthController@sendResetPasswordMail');


