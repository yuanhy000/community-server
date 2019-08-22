<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('force-json')->group(function () {

    Route::post('token/user', 'TokenController@getToken');
    Route::post('token/app', 'TokenController@getAppToken');
    Route::post('token/check', 'TokenController@checkToken');

    Route::post('user/info/set', 'UserController@updateInfo');
    Route::post('user/info/get', 'UserController@getInfo');
    Route::post('user/getById', 'UserController@getUserByID');
    Route::post('user/follow', 'UserController@followUser');
    Route::post('user/article', 'UserController@getUserArticle');
    Route::post('user/question', 'UserController@getUserQuestion');
    Route::post('user/follow/topic', 'UserController@getUserFollowTopic');
    Route::post('user/follow/question', 'UserController@getUserFollowQuestion');
    Route::post('user/follow/user', 'UserController@getUserFollowUser');
    Route::post('user/follow/fans', 'UserController@getUserFans');

    Route::post('topic/getOne', 'TopicController@getOneTopic');
    Route::post('topic/get', 'TopicController@getTopic');
    Route::post('topic/article', 'TopicController@getArticle');
    Route::post('topic/question', 'TopicController@getQuestion');
//    Route::post('topic/recommend', 'TopicController@getRecommend');
    Route::post('topic/follow', 'TopicController@follow');
    Route::post('topic/getFollowed', 'TopicController@getFollowed');
//    Route::post('topic/article/recommend', 'TopicController@getRecommendArticle');

    Route::post('search/article', 'SearchController@searchArticle');
    Route::post('search/topic', 'SearchController@searchTopic');
    Route::post('search/question', 'SearchController@searchQuestion');
    Route::post('search/user', 'SearchController@searchUser');

    Route::post('addition/daily', 'ArticleController@setDailyArticle');
    Route::post('addition/photo', 'ArticleController@setPhotoArticle');
    Route::post('addition/video', 'ArticleController@setVideoArticle');
    Route::post('addition/question', 'QuestionController@setQuestionArticle');
    Route::post('addition/answer', 'AnswerController@setAnswer');

    Route::get('article/count', 'ArticleController@getArticleCount');
    Route::post('article/recommend', 'ArticleController@getRecommendArticle');
    Route::post('article/update', 'ArticleController@getUpdateArticle');
    Route::post('article/follow', 'ArticleController@getFollowArticle');
    Route::post('article/one', 'ArticleController@getOne');
    Route::post('article/vote', 'VoteController@voteForArticle');
    Route::post('photo/top/days', 'ArticleController@getDaysPhoto');
    Route::post('photo/recommend', 'ArticleController@getRecommendPhoto');
    Route::post('video/recommend', 'ArticleController@getRecommendVideo');

    Route::post('comment/vote', 'VoteController@voteForComment');
    Route::post('article/comment/post', 'CommentController@articleCommentPost');
    Route::post('answer/comment/post', 'CommentController@answerCommentPost');

    Route::get('question/top', 'QuestionController@top');
    Route::post('question/getOne', 'QuestionController@getOne');
    Route::post('question/follow', 'QuestionController@follow');
    Route::post('question/recommend', 'QuestionController@getRecommend');

    Route::post('answer/recommend', 'AnswerController@getRecommendAnswer');
    Route::post('answer/update', 'AnswerController@getUpdateAnswer');
    Route::post('answer/vote', 'VoteController@voteForAnswer');
    Route::post('answer/one', 'AnswerController@getOne');



});
