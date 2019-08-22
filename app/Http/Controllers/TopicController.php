<?php

namespace App\Http\Controllers;

use App\Exceptions\ArticleException;
use App\Exceptions\ParameterException;
use App\Exceptions\SuccessMessage;
use App\Exceptions\UserException;
use App\Http\model\TopicFollowModel;
use App\Http\model\TopicModel;
use App\Http\Model\UserModel;
use App\Http\Model\ValidateModel;
use App\Http\model\VoteAnswerModel;
use App\Http\model\VoteArticleModel;
use App\Service\Token;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function getTopic(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $topicInfo = TopicModel::getTopicInfo($request->page, $request->size);
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

    public function getOneTopic(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->id);

        $topicInfo = TopicModel::getTopicByID($request->id);
        if (empty($topicInfo)) {
            throw new ParameterException();
        }
        $topicInfo = $this->getFollowStatus($topicInfo);
        return $topicInfo;
    }

    public function getFollowed(Request $request)
    {
        $topicInfo = TopicModel::getFollowed();

        return $topicInfo;
    }

    public function getArticle(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $result = TopicModel::getArticle($request->topicID, $request->page, $request->size);
        if (empty($result)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($result);
        $result = ArticleController::getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getQuestion(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $result = TopicModel::getQuestion($request->topicID, $request->page, $request->size);
        if (empty($result)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteAnswerModel::getVoteStatus($result);
        $result = ArticleController::getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getFollowStatus($topicInfo)
    {
        $topic_id = $topicInfo->id;
        $uid = Token::getCurrentUid();
        $record = TopicFollowModel::where([['topic_id', $topic_id], ['follower_id', $uid]])->get();
        if ($record->isEmpty()) {
            $topicInfo['isFollow'] = false;
        } else {
            $topicInfo['isFollow'] = true;
        }
        return $topicInfo;
    }

    public function follow(Request $request)
    {
        $topic_id = $request->topic_id;
        $behaviour = $request->behaviour;

        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }

        $record = TopicFollowModel::where([['topic_id', $topic_id], ['follower_id', $uid]])->first();
        $topic = TopicModel::find($topic_id);
        if ($behaviour == 1) {
            if (is_null($record)) {
                $topic->increment('followers_count');

                $topicFollow = new TopicFollowModel();
                $topicFollow->topic_id = $topic_id;
                $topicFollow->follower_id = $uid;
                $topicFollow->save();
                throw new SuccessMessage();
            }
            throw new ArticleException([
                'msg' => '客户端数据不同步，请刷新后再试'
            ]);
        } else {
            $record->delete();
            $topic->decrement('followers_count');
            throw new SuccessMessage();
        }
    }
}
