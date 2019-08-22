<?php

namespace App\Http\Controllers;

use App\Exceptions\ArticleException;
use App\Exceptions\SuccessMessage;
use App\Exceptions\UserException;
use App\Http\model\QuestionFollowModel;
use App\Http\model\QuestionModel;
use App\Http\Model\UserModel;
use App\Http\Model\ValidateModel;
use App\Service\Token;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function setQuestionArticle(Request $request)
    {
        ValidateModel::checkArticle($request);

        $result = QuestionModel::CreateQuestion($request);
        if (!$result) {
            throw new ArticleException([
                'msg' => '很遗憾，发布话题失败，请稍后再试'
            ]);
        }
        return $result;
    }

    public function top(Request $request)
    {
        $result = QuestionModel::getTop();

        if (empty($result)) {
            return [
                'data' => []
            ];
        }
//        $result = VoteArticleModel::getVoteStatus($articleList);
        return [
            'data' => $result
        ];
    }

    public function getRecommend(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $questionList = QuestionModel::getRecommend($request->page, $request->size);
        if (empty($questionList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        return [
            'current_page' => $request->page,
            'data' => $questionList
        ];
    }

    public function getOne(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->id);

        $result = QuestionModel::getOne($request->id);
        $result = $this->getFollowStatus($result);
        return $result;
    }

    public function follow(Request $request)
    {
        $question_id = $request->question_id;
        $behaviour = $request->behaviour;

        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }

        $record = QuestionFollowModel::where([['question_id', $question_id], ['follower_id', $uid]])->first();
        $question = QuestionModel::find($question_id);
        if ($behaviour == 1) {
            if (is_null($record)) {
                $question->increment('likes');

                $questionFollow = new QuestionFollowModel();
                $questionFollow->question_id = $question_id;
                $questionFollow->follower_id = $uid;
                $questionFollow->save();
                throw new SuccessMessage();
            }
            throw new ArticleException([
                'msg' => '客户端数据不同步，请刷新后再试'
            ]);
        } else {
            $record->delete();
            $question->decrement('likes');
            throw new SuccessMessage();
        }
    }

    public static function getFollowStatus($question)
    {
//        dd(count($question));
        if (is_array($question)) {
            for ($i = 0; $i < count($question); $i++) {
                $question_id = $question[$i]['id'];
                $uid = Token::getCurrentUid();
                $record = QuestionFollowModel::where([['question_id', $question_id], ['follower_id', $uid]])->get();
                if ($record->isEmpty()) {
                    $question[$i]['isFollow'] = false;
                } else {
                    $question[$i]['isFollow'] = true;
                }
            }
        } else {
            $question_id = $question->id;
            $uid = Token::getCurrentUid();
            $record = QuestionFollowModel::where([['question_id', $question_id], ['follower_id', $uid]])->get();
            if ($record->isEmpty()) {
                $question['isFollow'] = false;
            } else {
                $question['isFollow'] = true;
            }
        }
        return $question;
    }
}
