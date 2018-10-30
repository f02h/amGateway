<?php

$router->get('/', 'AdminController@index');
$router->get('cred/{id}', 'CredController@show');
$router->put('cred/{id}', 'CredController@store');
$router->post('cred', 'CredController@create');
$router->delete('cred/{id}', 'CredController@delete');

$router->get('token/{id}', 'TokenController@show');
$router->put('token/{id}', 'TokenController@store');
$router->post('token', 'TokenController@create');
$router->delete('token/{id}', 'TokenController@delete');

