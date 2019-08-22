<?php

namespace App\Http\Controllers;

use App\Exceptions\ArticleException;
use App\Http\model\AnswerModel;
use App\Http\model\UserFollowModel;
use App\Http\Model\ValidateModel;
use App\Http\model\VoteAnswerModel;
use App\Http\model\VoteCommentModel;
use App\Service\Token;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function setAnswer(Request $request)
    {
        ValidateModel::checkArticle($request);

        $result = AnswerModel::CreateAnswer($request);
        if (!$result) {
            throw new ArticleException();
        }
        return $result;
    }

    public function getRecommendAnswer(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $answerList = AnswerModel::getRecommend($request->id, $request->page, $request->size);
        if (empty($answerList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteAnswerModel::getVoteStatus($answerList);
        $result = ArticleController::getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }


    public function getUpdateAnswer(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $answerList = AnswerModel::getUpdate($request->id, $request->page, $request->size);
        if (empty($answerList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteAnswerModel::getVoteStatus($answerList);
        $result = ArticleController::getFollowStatus($result);

        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getOne(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->id);
        $answer = AnswerModel::getOne($request->id);
        $answer = $this->getFollowStatus($answer);
        $answer = VoteAnswerModel::getVoteStatus($answer);
        $answer = VoteCommentModel::getVoteStatus($answer);
        return $answer;
    }

    public function getFollowStatus($answer)
    {
        $author_id = $answer->users->id;
        $uid = Token::getCurrentUid();
        $record = UserFollowModel::where([['user_id', $author_id], ['follower_id', $uid]])->get();
        if ($record->isEmpty()) {
            $answer['isFollow'] = false;
        } else {
            $answer['isFollow'] = true;
        }
        return $answer;
    }
}
