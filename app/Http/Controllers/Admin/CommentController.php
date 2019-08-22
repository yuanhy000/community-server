<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\AnswerCommentModel;
use App\Http\Model\ArticleCommentModel;
use App\Http\Model\CommentModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comment = CommentModel::with('users')->orderBy('id', 'asc')->paginate(10);
        return view('admin.comment.index', compact('comment'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $comment = CommentModel::where('id', '=', $id)->with('users')->first();
        return view('admin/comment/edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = Input::except('_token', '_method');
        DB::beginTransaction();
        try {
            $comment = CommentModel::where('id', '=', $id)->first();
            $comment->display = $input['display'];
            $re = $comment->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        if ($re) {
            return redirect('admin/comment');
        } else {
            session(['_error' => '数据更新失败，请稍后重试！']);
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AnswerCommentModel::where('comment_id', '=', $id)->delete();
        ArticleCommentModel::where('comment_id', '=', $id)->delete();
        $re = CommentModel::where('id', '=', $id)->delete();
        if ($re) {
            $data = [
                'status' => 0,
                'msg' => '评论删除成功！'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => '评论删除失败，请稍后重试！'
            ];
        }
        return $data;
    }
}
