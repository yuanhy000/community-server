<?php

namespace App\Http\Controllers;

use App\Exceptions\ArticleException;
use App\Exceptions\SuccessMessage;
use App\Exceptions\UserException;
use App\Http\model\AnswerModel;
use App\Http\model\ArticleModel;
use App\Http\model\QuestionModel;
use App\Http\model\TopicModel;
use App\Http\model\UserFollowModel;
use App\Http\Model\UserModel;
use App\Http\Model\ValidateModel;
use App\Http\model\VoteAnswerModel;
use App\Http\model\VoteArticleModel;
use App\Service\Common;
use App\Service\Token;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateInfo(Request $request)
    {
        ValidateModel::checkUserInfo($request);
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }
        $user->nickName = Common::getRequestInfo($request, 'nickName');
        $user->avatarUrl = Common::getRequestInfo($request, 'avatarUrl');
        $user->sex = Common::getRequestInfo($request, 'sex');
        $flag = $user->update();
        if ($flag) {
            throw new SuccessMessage();
        } else {
            throw new UserException([
                'msg' => '用户信息更新失败'
            ]);
        }
    }

    public function getUserArticle(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $articleList = ArticleModel::getUserArticle($request->user_id, $request->type, $request->page, $request->size);

        if (empty($articleList)) {
            return [
                'current_page' => $request->page,
                'data' => []
            ];
        }
        $result = VoteArticleModel::getVoteStatus($articleList);
        $result = ArticleController::getFollowStatus($result);
        return [
            'current_page' => $request->page,
            'data' => $result
        ];
    }

    public function getUserFollowTopic(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->user_id);
        $topic = TopicModel::getFollowedByID($request->user_id);
        if (empty($topic)) {
            return [
                'data' => []
            ];
        }
        return [
            'data' => $topic
        ];
    }

    public function getUserFollowQuestion(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $question = QuestionModel::getQuestionByFollow($request->user_id, $request->page, $request->size);
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


    public function getUserFollowUser(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $user = UserModel::getUserFollowUser($request->user_id, $request->page, $request->size);
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


    public function getUserFans(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $user = UserModel::getUserFans($request->user_id, $request->page, $request->size);
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

    public function getUserQuestion(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->page);
        ValidateModel::valueMustBePositiveInt($request->size);

        $result = AnswerModel::getUserQuestion($request->user_id, $request->page, $request->size);
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

    public function getInfo(Request $request)
    {
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }
        return $user;
    }

    public function getUserByID(Request $request)
    {
        $target_id = ValidateModel::valueMustBePositiveInt($request->user_id);

        $uid = Token::getCurrentUid();
        $user = UserModel::find($target_id);
        if (!$user) {
            throw new UserException();
        }
        return $user;
    }

    public function followUser(Request $request)
    {
        $target_id = $request->user_id;
        $behaviour = $request->behaviour;

        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }

        $record = UserFollowModel::where([['user_id', $target_id], ['follower_id', $uid]])->first();
        $user = UserModel::find($target_id);
        $follower = UserModel::find($uid);
        if ($behaviour == 1) {
            if (is_null($record)) {
                $user->increment('follower');
                $follower->increment('attention');
                $userFollow = new UserFollowModel();
                $userFollow->user_id = $target_id;
                $userFollow->follower_id = $uid;
                $userFollow->save();
                throw new SuccessMessage();
            }
            throw new ArticleException([
                'msg' => '客户端数据不同步，请刷新后再试'
            ]);
        } else {
            $record->delete();
            $user->decrement('follower');
            $follower->decrement('attention');
            throw new SuccessMessage();
        }
    }
}
