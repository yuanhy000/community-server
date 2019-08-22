<?php

namespace App\Http\model;

use App\Exceptions\ArticleException;
use App\Service\Token;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $fillable = ['content', 'user_id'];

    public function users()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function articles()
    {
        return $this->belongsToMany(ArticleModel::class, 'article_comment', 'comment_id', 'article_id');
    }

    public static function getForArticle($article_id)
    {
        $result = ArticleModel::where('id', '=', $article_id)
            ->with([
                'comments' => function ($query) {
                    $query->with('users')->orderBy('likes', 'desc');
                }
            ])->first();
        return $result->comments;
    }

    public static function getForAnswer($answer_id)
    {
        $result = AnswerModel::where('id', '=', $answer_id)
            ->with([
                'comments' => function ($query) {
                    $query->with('users')->orderBy('likes', 'desc');
                }
            ])->first();
        return $result->comments;
    }

    public static function createArticleComment($request)
    {
        DB::beginTransaction();
        try {
            $uid = (new self())->getUser();
            $comment = new CommentModel();
            $comment->content = $request->comment;
            $comment->user_id = $uid;
            $comment->save();

            $articleComment = new ArticleCommentModel();
            $articleComment->article_id = $request->article_id;
            $articleComment->comment_id = $comment->id;
            $articleComment->save();

            $article = ArticleModel::find($request->article_id);
            if (!$article) {
                throw new ArticleException();
            }
            $article->increment('comments_count');

            DB::commit();
            return $comment;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function createAnswerComment($request)
    {
        DB::beginTransaction();
        try {
            $uid = (new self())->getUser();
            $comment = new CommentModel();
            $comment->content = $request->comment;
            $comment->user_id = $uid;
            $comment->save();

            $answerComment = new AnswerCommentModel();
            $answerComment->answer_id = $request->answer_id;
            $answerComment->comment_id = $comment->id;
            $answerComment->save();

            $answer = AnswerModel::find($request->answer_id);
            if (!$answer) {
                throw new ArticleException();
            }
            $answer->increment('comments_count');

            DB::commit();
            return $comment;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function getUser()
    {
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            throw new UserException();
        }
        return $uid;
    }
}
