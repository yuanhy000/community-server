<?php

namespace App\Http\Controllers;

use App\Exceptions\SuccessMessage;
use App\Exceptions\UserException;
use App\Http\model\AnswerModel;
use App\Http\model\ArticleModel;
use App\Http\model\CommentModel;
use App\Http\Model\UserModel;
use App\Http\model\VoteAnswerModel;
use App\Http\model\VoteArticleModel;
use App\Http\model\VoteCommentModel;
use App\Service\Common;
use App\Service\Token;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function voteForArticle(Request $request)
    {
        $article_id = Common::getRequestInfo($request, 'article_id');
        $index = Common::getRequestInfo($request, 'index');
        $article = ArticleModel::where('id', '=', $article_id)->first();
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException([
                'msg' => '请先登录再进行操作'
            ]);
        }
        if ($index == 1) {
            $vote = new VoteArticleModel();
            $vote->user_id = Token::getCurrentUid();
            $vote->article_id = $article_id;
            $vote->save();
            $article->increment('likes');
            throw new SuccessMessage();
        } else {
            $article->decrement('likes');
            $vote = VoteArticleModel::where([['article_id', $article_id], ['user_id', $uid]])->first();
            $vote->delete();
            throw new SuccessMessage();
        }
    }

    public function voteForAnswer(Request $request)
    {
        $answer_id = Common::getRequestInfo($request, 'answer_id');
        $index = Common::getRequestInfo($request, 'index');
        $answer = AnswerModel::where('id', '=', $answer_id)->first();
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException([
                'msg' => '请先登录再进行操作'
            ]);
        }
        if ($index == 1) {
            $vote = new VoteAnswerModel();
            $vote->user_id = Token::getCurrentUid();
            $vote->answer_id = $answer_id;
            $vote->save();
            $answer->increment('likes');
            throw new SuccessMessage();
        } else {
            $answer->decrement('likes');
            $vote = VoteAnswerModel::where([['answer_id', $answer_id], ['user_id', $uid]])->first();
            $vote->delete();
            throw new SuccessMessage();
        }
    }

    public function voteForComment(Request $request)
    {
        $comment_id = Common::getRequestInfo($request, 'comment_id');
        $index = Common::getRequestInfo($request, 'index');
        $comment = CommentModel::where('id', '=', $comment_id)->first();
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException([
                'msg' => '请先登录再进行操作'
            ]);
        }
        if ($index == 1) {
            $vote = new VoteCommentModel();
            $vote->user_id = Token::getCurrentUid();
            $vote->comment_id = $comment_id;
            $vote->save();
            $comment->increment('likes');
            throw new SuccessMessage();
        } else {
            $comment->decrement('likes');
            $vote = VoteCommentModel::where([['comment_id', $comment_id], ['user_id', $uid]])->first();
            $vote->delete();
            throw new SuccessMessage();
        }
    }
}
