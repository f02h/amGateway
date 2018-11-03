<?php

$router->get('/', 'AdminController@index');
$router->get('logout', 'AdminController@logout');

$router->get('gateway/{id}', 'GatewayController@show');
$router->put('gateway/{id}', 'GatewayController@store');
$router->post('gateway', 'GatewayController@create');
$router->delete('gateway/{id}', 'GatewayController@delete');

$router->get('user/{id}', 'UserController@show');
$router->put('user/{id}', 'UserController@store');
$router->post('user', 'UserController@create');
$router->get('user', 'UserController@add');
$router->delete('user/{id}', 'UserController@delete');

