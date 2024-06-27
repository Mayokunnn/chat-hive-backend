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

        // User management
        $router->get('/user', ['as' => 'get-user', 'uses' => 'UserController@getUser']);
        $router->post('/user/update', ['as' => 'update-user', 'uses' => 'UserController@updateUser']);

        //Conversation management
        $router->group(['prefix' => 'conversations'], function () use ($router) {
            $router->get('/', ['as' => 'get-conversations', 'uses' => 'ConversationController@getAll']);
            $router->post('/create', ['as' => 'create-conversation', 'uses' => 'ConversationController@create']);

            $router->group(['prefix' => '{conversation_id}', 'middleware' => 'ensureUserIsPartOfConversation'], function () use ($router) {
                $router->get('/', ['as' => 'get-conversation', 'uses' => 'ConversationController@getSingleConversation']);
                $router->post('/update', ['as' => 'update-conversation', 'uses' => 'ConversationController@update']);
                $router->post('/delete', ['            as' => 'delete-conversation', 'uses' => 'ConversationController@delete']);

                // Conversation user management
                $router->get('/users', ['as' => 'get-conversation-users', 'uses' => 'ConversationUserController@getAll']);
                $router->post('/users/add', ['as' => 'add-conversation-user', 'uses' => 'ConversationUserController@add']);
                $router->post('/users/remove', ['as' => 'remove-conversation-user', 'uses' => 'ConversationUserController@remove']);

                $router->group(['prefix' => 'messages'], function () use ($router) {
                    $router->get('/', ['as' => 'get-conversation-messages',  'uses' => 'MessageController@getAll']);
                    $router->post('/send', ['as' => 'add-conversation-message',  'uses' => 'MessageController@send']);
                    $router->group(['prefix' => '{message_id}'], function () use ($router) {
                        $router->post('/edit', ['as' => 'update-conversation-message',  'uses' => 'MessageController@edit']);
                        $router->post('/delete', ['as' => 'delete-conversation-message',  'uses' => 'MessageController@delete']);
                    });
                });
            });
        });
    });
});
