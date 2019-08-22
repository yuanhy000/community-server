<?php

namespace App\Http\model;

use App\Service\Token;
use Illuminate\Database\Eloquent\Model;

class VoteCommentModel extends Model
{
    protected $table = 'vote_comment';
    protected $fillable = ['user_id', 'comment_id'];

    public static function getVoteStatus($result)
    {
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            for ($i = 0; $i < count($result->comments); $i++) {
                $result->comments[$i]['isVote'] = false;
            }
        }

        if (is_array(json_decode($result->comments,true))) {
            for ($i = 0; $i < count($result->comments); $i++) {
                $comment_id = $result->comments[$i]['id'];
                $info = self::where([['user_id', '=', $uid], ['comment_id', $comment_id]])->first();
                if ($info) {
                    $result->comments[$i]['isVote'] = true;
                } else {
                    $result->comments[$i]['isVote'] = false;
                }
            }
        } else{
            $comment_id = $result->comments['id'];
            $info = self::where([['user_id', '=', $uid], ['article_id', $comment_id]])->first();
            if ($info) {
                $result->comments['isVote'] = true;
            } else {
                $result->comments['isVote'] = false;
            }
        }

        return $result;
    }
}
