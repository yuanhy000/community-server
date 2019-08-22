<?php

namespace App\Http\model;

use App\Exceptions\ArticleException;
use App\Exceptions\TopicException;
use App\Exceptions\UserException;
use App\Service\Token;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionModel extends Model
{
    protected $table = 'questions';
    protected $fillable = ['number', 'title', 'content', 'user_id', 'temperature'];
    protected $primaryKey = 'id';
    protected $hidden = ['number', 'updated_at', 'deleted_at'];

    public function images()
    {
        return $this->belongsToMany(ImageModel::class, 'question_image', 'question_id', 'img_id');
    }

    public function users()
    {
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }

    public function topics()
    {
        return $this->belongsToMany(TopicModel::class, 'topic_question', 'question_id', 'topic_id');
    }

    public function answers()
    {
        return $this->hasMany(AnswerModel::class, 'question_id', 'id');
    }

    public function followers()
    {
        return $this->belongsToMany(UserModel::class, 'question_follower', 'question_id', 'follower_id');
    }

    public static function getRecommend($page, $size)
    {
        if ($page == 1) {
            $current_time = date_create(Carbon::now()->toDateTimeString());
            $question = self::all();
            for ($i = 0; $i < count($question); $i++) {
                $created_time = date_create($question[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $question[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($question[$i]->attributes['likes'],
                        $question[$i]->attributes['answer_count'], $days_diff);
                $question[$i]->update();
            }
        }

        $result = self::orderBy('temperature', 'desc')
            ->with(['images', 'users', 'topics'])
            ->with(['answers' => function ($query) {
                $query->orderBy('temperature', 'desc');
            }])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getOne($id)
    {
        $result = self::where('id', '=', $id)
            ->with(['images', 'users', 'topics'/*, 'votes'*/])
            ->first();
        return $result;
    }

    public static function getTop()
    {
        $current_time = date_create(Carbon::now()->toDateTimeString());
        $question = self::all();
        for ($i = 0; $i < count($question); $i++) {
            $created_time = date_create($question[$i]->attributes['created_at']);
            $days_diff = date_diff($current_time, $created_time)->days;
            $question[$i]->attributes['temperature'] =
                (new self())->getHackerNewsScore($question[$i]->attributes['likes'],
                    $question[$i]->attributes['answer_count'], $days_diff);
            $question[$i]->update();
        }

        $result = self::orderBy('temperature', 'desc')
            ->with(['images'])
            ->paginate(4)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getQuestionBySearch($name, $page, $size)
    {
        $result = self::where(function ($query) use ($name) {
            $query->where('title', 'like', "%{$name}%")
                ->orWhere('content', 'like', "%{$name}%");
        })
            ->orderBy('created_at', 'desc')
            ->with(['images', 'users', 'topics'])
            ->with(['answers' => function ($query) {
                $query->orderBy('temperature', 'desc');
            }])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])
            ->toArray();
        return $result;
    }


    public static function getQuestionByFollow($user_id, $page, $size)
    {
        $result = self::whereHas('followers', function ($query) use ($user_id) {
            $query->where('follower_id', '=', $user_id);
        })
            ->orderBy('created_at', 'desc')
            ->with(['images', 'users', 'topics'])
            ->with(['answers' => function ($query) {
                $query->orderBy('temperature', 'desc');
            }])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])
            ->toArray();
        return $result;
    }


    public static function CreateQuestion(Request $request)
    {
        DB::beginTransaction();
        try {
            $questionInfo = (new self())->createQuestionRelated($request);
            (new self())->updateTopic($request);
            (new self())->createTopicRelated($request, $questionInfo['questionID']);
            (new self())->createImageRelated($request, $questionInfo['questionID']);

            DB::commit();
            return $questionInfo;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function createQuestionRelated(Request $request)
    {
        $uid = $this->getUser();
        $question = new QuestionModel();
        $question->user_id = $uid;
        $question->number = $this->makeArticleNumber();
        if ($request->info['title']) {
            $question->title = $request->info['title'];
        }
        if ($request->info['content']) {
            $question->content = $request->info['content'];
        }
        $question->save();

        return [
            'questionID' => $question->id,
            'questionNumber' => $question->number,
            'created_at' => $question->created_at
        ];
    }

    private function updateTopic(Request $request)
    {
        $topic = TopicModel::find($request->info['topicInfo']['id']);
        if (!$topic) {
            throw new TopicException();
        }
        $topic->increment('article_count');
        return true;
    }

    private function createTopicRelated(Request $request, $questionID)
    {
        $topicQuestionInfo = [
            'topic_id' => $request->info['topicInfo']['id'],
            'question_id' => $questionID
        ];
        TopicQuestionModel::create($topicQuestionInfo);
        return true;
    }

    private function createImageRelated(Request $request, $questionID)
    {
        foreach ($request->info['imageUrl'] as $url) {
            $image = new ImageModel();
            $image->url = $url;
            $image->from = 2;
            $image->save();

            $questionImageInfo = [
                'question_id' => $questionID,
                'img_id' => $image->id
            ];
            QuestionImageModel::create($questionImageInfo);
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

    public static function makeArticleNumber()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $articleNumber = $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        return $articleNumber;
    }

    private function getHackerNewsScore($likes, $answer, $days_diff)
    {
        $G = 1.8;
        return ($answer + $likes + 1) / pow(($days_diff + 2), $G);
    }
}
