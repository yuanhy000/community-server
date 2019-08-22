<?php

namespace App\Http\Controllers;

use App\Http\model\ArticleModel;
use App\Http\model\QuestionModel;
use App\Http\model\TopicModel;
use App\Http\Model\UserModel;
use App\Http\Model\ValidateModel;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchTopic(Request $request)
    {
        ValidateModel::checkSearch($request);
        $topicInfo = TopicModel::getTopicBySearch($request->name, $request->page, $request->size);
        if (empty($topicInfo)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        return [
            'current_page' => $request->page,
            'data' => $topicInfo
        ];
    }

    public function searchArticle(Request $request)
    {
        ValidateModel::checkSearch($request);
        $article = ArticleModel::getArticleBySearch($request->name, $request->page, $request->size);
        if (empty($article)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $article = ArticleController::getFollowStatus($article);
        return [
            'current_page' => $request->page,
            'data' => $article
        ];
    }

    public function searchQuestion(Request $request)
    {
        ValidateModel::checkSearch($request);
        $question = QuestionModel::getQuestionBySearch($request->name, $request->page, $request->size);
        if (empty($question)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
//        $question = QuestionController::getFollowStatus($question);
        return [
            'current_page' => $request->page,
            'data' => $question
        ];
    }

    public function searchUser(Request $request)
    {
        ValidateModel::checkSearch($request);
        $user = UserModel::getUserBySearch($request->name, $request->page, $request->size);
        if (empty($user)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $user = UserModel::isFollowUser($user);
        return [
            'current_page' => $request->page,
            'data' => $user
        ];
    }
}
