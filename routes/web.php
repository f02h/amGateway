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

//$router->get('msg/{id}/', 'MsgController@show');
$router->get('/msgs/read/{action}', 'ApiMsgController@getMessages');
$router->get('/msgs/confirm/', 'ApiMsgController@confirmMessages');
$router->get('/msgs/release/', 'ApiMsgController@releaseMessages');

$router->get('/reg/{id}', 'CredController@show');



$router->get('users', function() {
    $users = \App\User::all();
    return response()->json($users);
});

$router->get('/key', function() {
    return response()->json(array("key" => str_random(32)));
});


