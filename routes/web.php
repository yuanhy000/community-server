<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/admin/login', 'Admin\LoginController@login');


Route::group(['middleware' => ['admin.login'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {

    Route::get('/', 'IndexController@index');
    Route::get('info', 'IndexController@info');
    Route::get('quit', 'LoginController@quit');

    Route::get('question_answer/{id}', 'QuestionController@answerList');
    Route::get('user_article/{id}', 'UserController@articleList');
    Route::get('user_answer/{id}', 'UserController@answerList');
    Route::get('user_comment/{id}', 'UserController@commentList');

    Route::resource('topic', 'TopicController');
    Route::resource('article', 'ArticleController');
    Route::resource('question', 'QuestionController');
    Route::resource('answer', 'AnswerController');
    Route::resource('comment', 'CommentController');
    Route::resource('user', 'UserController');

    Route::post('article/changeOrder', 'ArticleController@changeOrder');

});