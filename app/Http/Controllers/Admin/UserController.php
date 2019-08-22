<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\AnswerModel;
use App\Http\Model\ArticleModel;
use App\Http\Model\CommentModel;
use App\Http\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = UserModel::orderBy('id', 'asc')->paginate(10);
        return view('admin.user.index', compact('user'));
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
        $user = UserModel::where('id', '=', $id)->first();
        return view('admin/user/edit', compact('user'));
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
            $user = UserModel::where('id', '=', $id)->first();
            $user->display = $input['display'];
            $re = $user->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        if ($re) {
            return redirect('admin/user');
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
        $re = UserModel::where('id', '=', $id)->delete();
        if ($re) {
            $data = [
                'status' => 0,
                'msg' => '用户删除成功！'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => '用户删除失败，请稍后重试！'
            ];
        }
        return $data;
    }


    public function articleList(Request $request, $id)
    {
        $article = ArticleModel::where('user_id', '=', $id)->with(['users',])
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.article.index', compact('article'));
    }

    public function answerList(Request $request, $id)
    {
        $answer = AnswerModel::where('user_id', '=', $id)->with(['images', 'users', 'questions'])
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.answer.index', compact('answer'));
    }

    public function commentList(Request $request, $id)
    {
        $comment = CommentModel::where('user_id', '=', $id)
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.comment.index', compact('comment'));
    }

}
