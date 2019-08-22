<?php

namespace App\Http\model;

use App\Exceptions\ArticleException;
use App\Exceptions\QuestionException;
use App\Exceptions\UserException;
use App\Service\Token;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnswerModel extends Model
{
    protected $table = 'answers';
    protected $fillable = ['user_id', 'question_id', 'content'];
    protected $hidden = ['updated_at', 'deleted_at'];

    public function users()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }

    public function images()
    {
        return $this->belongsToMany(ImageModel::class, 'answer_image', 'answer_id', 'img_id');
    }

    public function votes()
    {
        return $this->belongsToMany(UserModel::class, 'vote_answer', 'answer_id', 'user_id');
    }

    public function comments()
    {
        return $this->belongsToMany(CommentModel::class, 'answer_comment', 'answer_id', 'comment_id');
    }

    public function questions()
    {
        return $this->belongsTo(QuestionModel::class, 'question_id', 'id');
    }

    public function topics()
    {
        return $this->belongsTo(TopicModel::class, 'topic_id', 'id');
    }

    public static function getUserQuestion($topic_id, $page, $size)
    {
        $answer = self::where('user_id', '=', $topic_id)
            ->orderBy('created_at', 'desc')
            ->with(['images', 'users', 'questions'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])->toArray();
        return $answer;
    }

    public static function getRecommend($questionID, $page, $size)
    {
        if ($page == 1) {
            $current_time = date_create(Carbon::now()->toDateTimeString());
            $answer = self::where('question_id', '=', $questionID)->get();
            for ($i = 0; $i < count($answer); $i++) {
                $created_time = date_create($answer[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $answer[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($answer[$i]->attributes['likes'], $answer[$i]->attributes['comments_count'], $days_diff);
                $answer[$i]->update();
            }
        }

        $result = self::where('question_id', '=', $questionID)
            ->orderBy('temperature', 'desc')
            ->with(['images', 'users'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])->toArray();
        return $result;
    }

    public static function getUpdate($questionID, $page, $size)
    {
        $result = self::where('question_id', '=', $questionID)
            ->orderBy('created_at', 'desc')
            ->with(['images', 'users'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'updated_at'])->toArray();
        return $result;
    }

    public static function getOne($id)
    {
        $result = self::where('id', '=', $id)
            ->with([
                'comments' => function ($query) {
                    $query->with('users')->orderBy('likes', 'desc');
                }
            ])
            ->with(['images', 'users', 'questions', 'votes'])
            ->first();
        return $result;
    }

    private function getHackerNewsScore($likes, $comments, $days_diff)
    {
        $G = 1.8;
        return ((1 + $comments / 100) * ($likes + 1)) / pow(($days_diff + 2), $G);
    }

    public static function CreateAnswer(Request $request)
    {
        //采用事物，避免数据不一致
        DB::beginTransaction();
        try {
            $answerInfo = (new self())->createAnswerRelated($request);
            (new self())->updateQuestion($request);
            (new self())->createImageRelated($request, $answerInfo['answerID']);

            DB::commit();
            return $answerInfo;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function createAnswerRelated(Request $request)
    {
        $uid = $this->getUser();
        $answer = new AnswerModel();
        $answer->user_id = $uid;
        $answer->question_id = $request->info['questionID'];
        $answer->topic_id = $request->info['topicID'];
        if ($request->info['content']) {
            $answer->content = $request->info['content'];
        }
        $answer->save();

        return [
            'answerID' => $answer->id,
            'created_at' => $answer->created_at
        ];

    }

    private function updateTopic(Request $request)
    {
        $topic = TopicModel::find($request->info['topicID']);
        if (!$topic) {
            throw new ArticleException();
        }
        $topic->increment('article_count');
        return true;
    }

    private function updateQuestion(Request $request)
    {
        $question = QuestionModel::find($request->info['questionID']);
        if (!$question) {
            throw new QuestionException();
        }
        $question->increment('answer_count');

        return true;
    }

    private function createImageRelated(Request $request, $answerID)
    {
        foreach ($request->info['imageUrl'] as $url) {
            $image = new ImageModel();
            $image->url = $url;
            $image->from = 2;
            $image->save();

            $answerImageInfo = [
                'answer_id' => $answerID,
                'img_id' => $image->id
            ];
            AnswerImageModel::create($answerImageInfo);
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
