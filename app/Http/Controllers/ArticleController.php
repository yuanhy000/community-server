<?php

namespace App\Http\Controllers;

use App\Exceptions\ArticleException;
use App\Exceptions\UserException;
use App\Http\model\AnswerModel;
use App\Http\model\ArticleModel;
use App\Http\model\QuestionModel;
use App\Http\model\UserFollowModel;
use App\Http\Model\UserModel;
use App\Http\Model\ValidateModel;
use App\Http\model\VoteArticleModel;
use App\Http\model\VoteCommentModel;
use App\Service\Token;
use Illuminate\Http\Request;

class ArticleController extends Controller
{

    public function getArticleCount(Request $request)
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

        $answer = AnswerModel::all();
        $answerCount = $answer->count();
        $answerPeople = $answer->unique('user_id')->count();
        $resultPeople = [];
        foreach($answer as $a){
            $resultPeople =($question->push($a));
        }
        $resultPeople =$resultPeople->unique('user_id')->count();
        return [
            'dailyCount' => $dailyCount,
            'dailyPeople' => $dailyPeople,
            'photoCount' => $photoCount,
            'photoPeople' => $photoPeople,
            'videoCount' => $videoCount,
            'videoPeople' => $videoPeople,
            'questionCount' => $questionCount + $answerCount,
            'questionPeople' => $resultPeople,
        ];
    }

    public function setDailyArticle(Request $request)
    {
        ValidateModel::checkArticle($request);

        $result = ArticleModel::CreateDailyArticle($request);
        if (!$result) {
            throw new ArticleException();
        }
        return $result;
    }

    public function setPhotoArticle(Request $request)
    {
        ValidateModel::checkArticle($request);

        $result = ArticleModel::CreatePhotoArticle($request);
        if (!$result) {
            throw new ArticleException();
        }
        return $result;
    }

    public function setVideoArticle(Request $request)
    {
        ValidateModel::checkArticle($request);

        $result = ArticleModel::CreateVideoArticle($request);

        if (!$result) {
            throw new ArticleException();
        }
        return $result;
    }

    public function getRecommendArticle(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $Article = new ArticleModel();
        $articleList = $Article->getRecommend($request->page, $request->size);

        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        $result = $this->getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getUpdateArticle(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $Article = new ArticleModel();
        $articleList = $Article->getUpdate($request->page, $request->size);

        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        $result = $this->getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getFollowArticle(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $Article = new ArticleModel();
        $articleList = $Article->getFollow($request->page, $request->size);

        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        $result = $this->getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getOne(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->id);
        $article = ArticleModel::getOne($request->id);
        $article = $this->getFollowStatus($article);
        $articleInfo = VoteArticleModel::getVoteStatus($article);
        $result = VoteCommentModel::getVoteStatus($articleInfo);
        return $result;
    }

    public static function getFollowStatus($article)
    {

        if (is_array($article)) {
            for ($i = 0; $i < count($article); $i++) {
//                dd($article[$i]);
                $author_id = $article[$i]['users']['id'];
                $uid = Token::getCurrentUid();
                $record = UserFollowModel::where([['user_id', $author_id], ['follower_id', $uid]])->get();
                if ($record->isEmpty()) {
                    $article[$i]['isFollow'] = false;
                } else {
                    $article[$i]['isFollow'] = true;
                }
            }
        } else {
            $author_id = $article->users->id;
            $uid = Token::getCurrentUid();
            $record = UserFollowModel::where([['user_id', $author_id], ['follower_id', $uid]])->get();
            if ($record->isEmpty()) {
                $article['isFollow'] = false;
            } else {
                $article['isFollow'] = true;
            }
        }
        return $article;
    }

    public function getDaysPhoto(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->days);
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $articleList = ArticleModel::getDaysPhoto($request->days, $request->page, $request->size);
        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getRecommendPhoto(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $articleList = ArticleModel::getRecommendPhoto($request->page, $request->size);
        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getRecommendVideo(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $articleList = ArticleModel::getRecommendVideo($request->page, $request->size);
        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        $result = $this->getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }
}
