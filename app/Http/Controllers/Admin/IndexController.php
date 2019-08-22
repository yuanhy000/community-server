<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\AnswerModel;
use App\Http\Model\ArticleModel;
use App\Http\Model\CommentModel;
use App\Http\Model\QuestionModel;
use App\Http\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin.index.index');
    }

    public function info()
    {
        $daily = ArticleModel::where('type', '=', 'Daily')->get();
        $dailyCount = $daily->count();
        $dailyPeople = $daily->unique('user_id')->count();

        $photo = ArticleModel::where('type', '=', 'Photo')->get();
        $photoCount = $photo->count();
        $photoPeople = $photo->unique('user_id')->count();

        $video = ArticleModel::where('type', '=', 'Video')->get();
        $videoCount = $video->count();
        $videoPeople = $video->unique('user_id')->count();

        $question = QuestionModel::all();
        $questionCount = $question->count();
        $questionPeople = $question->unique('user_id')->count();

        $comment = CommentModel::all();
        $commentCount = $comment->count();
        $commentPeople = $comment->unique('user_id')->count();

        $user = UserModel::all();
        $userCount = $user->count();

        $answer = AnswerModel::all();
        $answerCount = $answer->count();
        $answerPeople = $answer->unique('user_id')->count();
        $resultPeople = [];
        foreach ($answer as $a) {
            $resultPeople = ($question->push($a));
        }
        $resultPeople = $resultPeople->unique('user_id')->count();
        return view('admin.index.info',
            compact('dailyCount', 'dailyPeople', 'photoCount', 'photoPeople',
                'videoCount', 'videoPeople', 'questionCount', 'answerCount', 'answerPeople',
                'questionPeople', 'commentCount', 'commentPeople', 'userCount'));
    }
}
