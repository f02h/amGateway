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

$router->get('/msg/', function () use ($router) {
    return App\Msg::all();
});

$router->get('msg/{id}/', 'MsgController@show');

$router->post('/reg', 'CredController@store');
$router->get('/reg', 'CredController@index');
$router->get('/reg/{id}', 'CredController@show');


