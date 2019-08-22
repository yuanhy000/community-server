<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\ArticleImageModel;
use App\Http\Model\ArticleModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = ArticleModel::orderBy('id', 'asc')->with('users')->paginate(10);
        return view('admin.article.index', compact('article'));
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
        $article = ArticleModel::where('id', '=', $id)->with(['images', 'videos', 'users', 'topics'])->first();
        return view('admin/article/edit', compact('article'));
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
            $article = ArticleModel::where('id', '=', $id)->first();
            $article->display = $input['display'];
            $re = $article->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        if ($re) {
            return redirect('admin/article');
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
        ArticleImageModel::where('article_id', '=', $id)->delete();
        $re = ArticleModel::where('id', '=', $id)->delete();
        if ($re) {
            $data = [
                'status' => 0,
                'msg' => '动态删除成功！'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => '动态删除失败，请稍后重试！'
            ];
        }
        return $data;
    }
}
