<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Events\MessageSent;

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



$router->post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
$router->post('register', ['as' => 'register', 'uses' => 'AuthController@register']);

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
    $router->group(['prefix' => 'api/v1'], function () use ($router) {
        $router->post('/messages', 'MessageController@sendMessage');
        $router->get('/messages', 'MessageController@getMessages');
        
    });
});
