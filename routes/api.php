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
    $router->post('password/reset', ['as' => 'password-reset', 'uses' => 'AuthController@resetPassword']);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->post('/logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);
        $router->post('/refresh-token', ['as' => 'token-refresh', 'uses' => 'AuthController@refreshToken']);
        $router->post('/users/{user_id}/password/change', ['as' => 'password-change', 'uses' => 'AuthController@changePassword']);

        // User management
        $router->get('/users/{user_id}', ['as' => 'get-user', 'uses' => 'UserController@getUser']);
        $router->post('/users/{user_id}/update', ['as' => 'update-user', 'uses' => 'UserController@updateUser']);
        $router->post('/users/{user_id}/delete', ['as' => 'delete-user', 'uses' => 'UserController@deleteUser']);
        $router->post('/users/{user_id}/conversations', ['as' => 'get-user-conversations', 'uses' => 'UserController@getUserConversations']);

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
                // $router->post('/users/add', ['as' => 'add-conversation-user', 'uses' => 'ConversationUserController@add']);
                // $router->post('/users/remove', ['as' => 'remove-conversation-user', 'uses' => 'ConversationUserController@remove']);

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

        //Group management
        $router->group(['prefix' => 'groups'], function () use ($router) {
            $router->get('/', ['as' => 'get-group-conversations', 'uses' => 'GroupConversationController@getAll']);
            $router->post('/create', ['as' => 'create-group-conversation', 'uses' => 'GroupConversationController@create']);

            $router->group(['prefix' => '{group_id}', 'middleware' => 'ensureUserIsPartOfGroupConversation'], function () use ($router) {
                $router->get('/', ['as' => 'get-group-conversation', 'uses' => 'GroupConversationController@getGroup']);
                $router->post('/update', ['as' => 'update-group-conversation', 'uses' => 'GroupConversationController@update']);
                $router->post('/delete', ['as' => 'delete-group-conversation', 'uses' => 'GroupConversationController@delete', 'middleware' => 'ensureUserIsOwner']);
                // Group Conversation Participants Routes
                $router->get('/members', ['as' => 'get-group-conversation-members', 'uses' => 'GroupConversationController@getAllParticipants']);
                $router->post('members/add', ['as' => 'add-group-conversation-member', 'uses' => 'GroupConversationController@addMember']);
                $router->post('members/remove/{user_id}', ['as' => 'remove-group-conversation-member', 'uses' => 'GroupConversationController@removeMember', 'middleware' => 'ensureUserIsOwner']);
            });
        });
    });
});
