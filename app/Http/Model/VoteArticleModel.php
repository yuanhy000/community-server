<?php

namespace App\Http\model;

use App\Service\Token;
use Illuminate\Database\Eloquent\Model;

class VoteArticleModel extends Model
{
    protected $table = 'vote_article';
    protected $fillable = ['user_id', 'article_id'];

    public static function getVoteStatus($result)
    {
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['isVote'] = false;
            }
        }
//        dd('1');
        if (is_array($result)) {
            for ($i = 0; $i < count($result); $i++) {
                $article_id = $result[$i]['id'];
                $info = self::where([['user_id', '=', $uid], ['article_id', $article_id]])->first();
                if ($info) {
                    $result[$i]['isVote'] = true;
                } else {
                    $result[$i]['isVote'] = false;
                }
            }
        } else{
            $article_id = $result['id'];
            $info = self::where([['user_id', '=', $uid], ['article_id', $article_id]])->first();
            if ($info) {
                $result['isVote'] = true;
            } else {
                $result['isVote'] = false;
            }
        }

        return $result;
    }
}
