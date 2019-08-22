<?php

namespace App\Http\model;

use App\Exceptions\ArticleException;
use App\Exceptions\UserException;
use App\Service\Token;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ArticleModel extends Model
{
    protected $table = 'articles';
    protected $fillable = ['type', 'title', 'content', 'user_id', 'temperature'];
    protected $primaryKey = 'id';
    protected $hidden = ['updated_at', 'deleted_at'];

    public function images()
    {
        return $this->belongsToMany(ImageModel::class, 'article_image', 'article_id', 'img_id');
    }

    public function videos()
    {
        return $this->belongsToMany(VideoModel::class, 'article_video', 'article_id', 'video_id');
    }

    public function users()
    {
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }

    public function topics()
    {
        return $this->belongsToMany(TopicModel::class, 'topic_article', 'article_id', 'topic_id');
    }

    public function votes()
    {
        return $this->belongsToMany(UserModel::class, 'vote_article', 'article_id', 'user_id');
    }

    public function comments()
    {
        return $this->belongsToMany(CommentModel::class, 'article_comment', 'article_id', 'comment_id');
    }

    public function getRecommend($page, $size)
    {
        if ($page == 1) {
            $current_time = date_create(Carbon::now()->toDateTimeString());
            $article = self::all();
            for ($i = 0; $i < count($article); $i++) {
                $created_time = date_create($article[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $article[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($article[$i]->attributes['likes'], $days_diff);
                $article[$i]->update();
            }
        }

        $result = self::where(function ($query) {
            $query->where('type', '=', "Daily")
                ->orWhere('type', '=', "Photo")
                ->orWhere('type', '=', "Video");
        })
            ->orderBy('temperature', 'desc')
            ->with(['images', 'videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getUserArticle($user_id, $type, $page, $size)
    {
        $result = self::where('user_id', '=', $user_id)
            ->where('type', '=', $type)
            ->orderBy('created_at', 'desc')
            ->with(['images', 'videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getDaysPhoto($days, $page, $size)
    {
        $time = date("Y-m-d", strtotime("-" . $days . "days", strtotime(Carbon::now()->toDateTimeString())));
        $result = self::where('type', '=', 'Photo')
            ->where('created_at', '>', $time)
            ->orderBy('likes', 'desc')
            ->orderBy('comments_count', 'desc')
            ->with(['images', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getRecommendPhoto($page, $size)
    {
        $article = self::where('type', '=', 'Photo');
        if ($page == 1) {
            $current_time = date_create(Carbon::now()->toDateTimeString());
            $articleInfo = $article->get();
            for ($i = 0; $i < count($articleInfo); $i++) {
                $created_time = date_create($articleInfo[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $articleInfo[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($articleInfo[$i]->attributes['likes'], $days_diff);
                $articleInfo[$i]->update();
            }
        }
        $result = $article
            ->orderBy('temperature', 'desc')
            ->with(['images', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }


    public static function getRecommendVideo($page, $size)
    {
        $article = self::where('type', '=', 'Video');
        if ($page == 1) {
            $current_time = date_create(Carbon::now()->toDateTimeString());
            $articleInfo = $article->get();
            for ($i = 0; $i < count($articleInfo); $i++) {
                $created_time = date_create($articleInfo[$i]->attributes['created_at']);
                $days_diff = date_diff($current_time, $created_time)->days;
                $articleInfo[$i]->attributes['temperature'] =
                    (new self())->getHackerNewsScore($articleInfo[$i]->attributes['likes'], $days_diff);
                $articleInfo[$i]->update();
            }
        }
        $result = $article
            ->orderBy('temperature', 'desc')
            ->with(['videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public static function getArticleBySearch($name, $page, $size)
    {
        $result = self::where(function ($query) use ($name) {
            $query->where('title', 'like', "%{$name}%")
                ->orWhere('content', 'like', "%{$name}%");
        })
            ->orderBy('created_at', 'desc')
            ->with(['images', 'videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])
            ->toArray();
        return $result;
    }


    public function getUpdate($page, $size)
    {
        $result = self::where(function ($query) {
            $query->where('type', '=', "Daily")
                ->orWhere('type', '=', "Photo")
                ->orWhere('type', '=', "Video");
        })
            ->orderBy('created_at', 'desc')
            ->with(['images', 'videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
        return $result;
    }

    public function getFollow($page, $size)
    {
        $uid = $this->getUser();
        $followUser = UserFollowModel::where('follower_id', '=', $uid)->get('user_id');
        $followUserID = [];
        for ($i = 0; $i < count($followUser); $i++) {
            $followUserID[$i] = $followUser[$i]->attributes['user_id'];
        }
        $result = self::whereIn('user_id', $followUserID)
            ->orderBy('created_at', 'desc')
            ->with(['images', 'videos', 'users', 'topics'])
            ->paginate($size)
            ->makeHidden(['deleted_at', 'number', 'updated_at'])->toArray();
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
            ->with(['images', 'videos', 'users', 'topics', 'votes'])
            ->first();
        return $result;
    }

    public static function CreateDailyArticle(Request $request)
    {
        //采用事物，避免数据不一致
        DB::beginTransaction();
        try {
            $articleInfo = (new self())->createArticleRelated($request);
            (new self())->updateTopic($request);
            (new self())->createTopicRelated($request, $articleInfo['articleID']);
            (new self())->createImageRelated($request, $articleInfo['articleID']);

            DB::commit();
            return $articleInfo;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function CreatePhotoArticle(Request $request)
    {
        DB::beginTransaction();
        try {
            $articleInfo = (new self())->createArticleRelated($request);
            (new self())->updateTopic($request);
            (new self())->createTopicRelated($request, $articleInfo['articleID']);
            (new self())->createImageRelated($request, $articleInfo['articleID']);

            DB::commit();
            return $articleInfo;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function CreateQuestionArticle(Request $request)
    {
        DB::beginTransaction();
        try {
            $articleInfo = (new self())->createArticleRelated($request);
            (new self())->updateTopic($request);
            (new self())->createTopicRelated($request, $articleInfo['articleID']);
            (new self())->createImageRelated($request, $articleInfo['articleID']);

            DB::commit();
            return $articleInfo;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function CreateVideoArticle(Request $request)
    {
        DB::beginTransaction();
        try {
            $articleInfo = (new self())->createArticleRelated($request);
            (new self())->updateTopic($request);
            (new self())->createTopicRelated($request, $articleInfo['articleID']);
            (new self())->createVideoRelated($request, $articleInfo['articleID']);
            DB::commit();
//            dd($articleInfo);
            return $articleInfo;
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

    private function updateTopic(Request $request)
    {
        $topic = TopicModel::find($request->info['topicInfo']['id']);
        if (!$topic) {
            throw new ArticleException();
        }
        $topic->increment('article_count');
        return true;
    }

    private function createArticleRelated(Request $request)
    {
        $uid = $this->getUser();
        $article = new ArticleModel();
        $article->user_id = $uid;
        $article->number = $this->makeArticleNumber();
        $article->type = $request->info['type'];
        if ($request->info['title']) {
            $article->title = $request->info['title'];
        }
        if ($request->info['content']) {
            $article->content = $request->info['content'];
        }
        $article->save();

        return [
            'articleID' => $article->id,
            'articleNumber' => $article->number,
            'created_at' => $article->created_at,
            'type' => $request->info['type']
        ];

    }

    private function createTopicRelated(Request $request, $articleID)
    {
        $topicArticleInfo = [
            'topic_id' => $request->info['topicInfo']['id'],
            'article_id' => $articleID
        ];
        TopicArticleModel::create($topicArticleInfo);
        return true;
    }

    private function createImageRelated(Request $request, $articleID)
    {
        foreach ($request->info['imageUrl'] as $url) {
            $image = new ImageModel();
            $image->url = $url;
            $image->from = 2;
            $image->save();

            $articleImageInfo = [
                'article_id' => $articleID,
                'img_id' => $image->id
            ];
            ArticleImageModel::create($articleImageInfo);
        }
    }

    private function createVideoRelated(Request $request, $articleID)
    {
        $video = new VideoModel();
        $video->video_url = $request->info['videoInfo']['videoUrl'];
        $video->video_cover = $request->info['videoInfo']['videoCoverUrl'];
        $video->from = 2;
        $video->save();

        $articleVideoInfo = [
            'article_id' => $articleID,
            'video_id' => $video->id
        ];
        ArticleVideoModel::create($articleVideoInfo);

    }


    public function makeArticleNumber()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $articleNumber = $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        return $articleNumber;
    }

    private function getHackerNewsScore($likes, $days_diff)
    {
        $G = 1.8;
        return ($likes + 1) / pow(($days_diff + 2), $G);
    }

    public function updateBatch($multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }
            $tableName = DB::getTablePrefix() . $this->getTable(); // 表名
            $firstRow = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets = [];
            $bindings = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings = array_merge($bindings, $whereIn);
            $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            // 传入预处理sql语句和对应绑定数据
            return DB::update($updateSql, $bindings);
        } catch (\Exception $e) {
//            return false;
            throw $e;
        }
    }

}

