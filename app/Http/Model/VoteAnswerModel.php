<?php

namespace App\Http\model;

use App\Service\Token;
use Illuminate\Database\Eloquent\Model;

class VoteAnswerModel extends Model
{
    protected $table = 'vote_answer';
    protected $fillable = ['user_id', 'answer_id'];

    public static function getVoteStatus($result)
    {
        $uid = Token::getCurrentUid();
        $user = UserModel::find($uid);
        if (!$user) {
            for ($i = 0; $i < count($result); $i++) {
                $result[$i]['isVote'] = false;
            }
        }
        if (is_array($result)) {
            for ($i = 0; $i < count($result); $i++) {
                $answer_id = $result[$i]['id'];
                $info = self::where([['user_id', '=', $uid], ['answer_id', $answer_id]])->first();
                if ($info) {
                    $result[$i]['isVote'] = true;
                } else {
                    $result[$i]['isVote'] = false;
                }
            }
        } else{
            $answer_id = $result['id'];
            $info = self::where([['user_id', '=', $uid], ['answer_id', $answer_id]])->first();
            if ($info) {
                $result['isVote'] = true;
            } else {
                $result['isVote'] = false;
            }
        }

        return $result;
    }
}
