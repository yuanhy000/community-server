<?php

namespace App\Http\model;

use App\Service\Token;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TopicModel extends Model
{
    protected $table = 'topics';
    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    public function image()
    {
        return $this->belongsTo(ImageModel::class, 'topic_img_id', 'id');
    }

    public function articles()
    {
        return $this->belongsToMany(ArticleModel::class, 'topic_article', 'topic_id', 'article_id');
    }

    public function questions()
    {
        return $this->belongsToMany(QuestionModel::class, 'topic_question', 'topic_id', 'question_id');
    }

    public function followers()
    {
        return $this->belongsToMany(UserModel::class, 'topic_follower', 'topic_id', 'follower_id');
    }

    public static function getArticle($topic_id, $page, $size)
    {
        $article = ArticleModel::whereHas('topics', function ($query) use ($topic_id) {
            $query->where('topic_id', '=', $topic_id);
        });
        if ($page == 1) {
            $articleInfo = $article->get();
            $current_time = date_create(Carbon::now()->toDateTimeString());
            for ($i = 0; $i < count($articleInfo); $i++) {
                $created_time = date_create($articleInfo[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $articleInfo[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($articleInfo[$i]->attributes['likes'],
                        $articleInfo[$i]->attributes['comments_count'], $days_diff);
                $articleInfo[$i]->update();
            }
        }

        $result = $article
            ->orderBy('temperature', 'desc')
            ->with(['images', 'videos', 'users'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();

        return $result;
    }

    public static function getQuestion($topic_id, $page, $size)
    {
        $answer = AnswerModel::where('topic_id', '=', $topic_id);
        if ($page == 1) {
            $answerInfo = $answer->get();
            $current_time = date_create(Carbon::now()->toDateTimeString());
            for ($i = 0; $i < count($answerInfo); $i++) {
                $created_time = date_create($answerInfo[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $answerInfo[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($answerInfo[$i]->attributes['likes'],
                        $answerInfo[$i]->attributes['comments_count'], $days_diff);
                $answerInfo[$i]->update();
            }
        }

        $result = $answer
            ->orderBy('temperature', 'desc')
            ->with(['images', 'users', 'questions'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])->toArray();
        return $result;
    }

    public static function getTopicInfo($page, $size)
    {
        $result = self::with('image')
            ->orderBy('article_count', 'desc')
            ->paginate($size)
            ->makeHidden('category_id')->toArray();
        return $result;
    }

    public static function getFollowed()
    {
        $uid = Token::getCurrentUid();
//        dd($uid);
        $topic = self::whereHas('followers', function ($query) use ($uid) {
            $query->where('follower_id', '=', $uid);
        })->with('image')->get();
        return $topic;
    }

    public static function getFollowedByID($user_id)
    {
        $topic = self::whereHas('followers', function ($query) use ($user_id) {
            $query->where('follower_id', '=', $user_id);
        })->with('image')->get();
        return $topic;
    }

    public static function getTopicBySearch($name, $page, $size)
    {
        $result = self::where('name', 'like', "%{$name}%")
            ->orderBy('article_count', 'desc')
            ->with('image')
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])
            ->toArray();
        return $result;
    }

    public static function getTopicByID($id)
    {
        $result = self::where('id', '=', $id)
            ->with('image')->first();
        return $result;
    }

    private function getHackerNewsScore($likes, $comments, $days_diff)
    {
        $G = 1.8;
        return ((1 + $comments / 100) * ($likes + 1)) / pow(($days_diff + 2), $G);
    }

}
