<?php

namespace App\Http\Controllers;

use App\Exceptions\CommentException;
use App\Exceptions\SuccessMessage;
use App\Http\model\CommentModel;
use App\Http\Model\ValidateModel;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function articleCommentPost(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->article_id);
        ValidateModel::checkComment($request);

        $result = CommentModel::createArticleComment($request);
        if (!$result) {
            throw new CommentException();
        }
        $comment = CommentModel::getForArticle($request->article_id);
        return $comment;
    }

    public function answerCommentPost(Request $request)
    {
        ValidateModel::valueMustBePositiveInt($request->answer_id);
        ValidateModel::checkComment($request);

        $result = CommentModel::createAnswerComment($request);
        if (!$result) {
            throw new CommentException();
        }
        $comment = CommentModel::getForAnswer($request->answer_id);
        return $comment;
    }

}
