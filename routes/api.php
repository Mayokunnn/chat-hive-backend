<?php

/** @var \Laravel\Lumen\Routing\Router $router */


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




$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
    $router->post('register', ['as' => 'register', 'uses' => 'AuthController@register']);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
        $router->post('/refresh-token', ['as' => 'token-refresh', 'uses' => 'AuthController@refreshToken']);

        $router->group(['prefix' => 'messages'], function () use ($router) {
            $router->post('/send', ['as' => 'get-messages', 'uses' => 'MessageController@send']);
        });

        $router->group(['prefix' => 'conversations'], function () use ($router) {
            $router->get('/', ['as' => 'get-conversations', 'uses' => 'ConversationController@getAll']);
            $router->post('/create', ['as' => 'create-conversation', 'uses' => 'ConversationController@create']);
            $router->post('/update', ['as' => 'update-conversation', 'uses' => 'ConversationController@update']);
            $router->post('/delete', ['as' => 'delete-conversation', 'uses' => 'ConversationController@delete']);
        });
    });
});
