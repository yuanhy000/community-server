<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\AnswerImageModel;
use App\Http\Model\AnswerModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $answer = AnswerModel::orderBy('id', 'asc')->with(['images', 'users', 'questions'])
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.answer.index', compact('answer'));
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
        $answer = AnswerModel::where('id', '=', $id)
            ->with(['images', 'users', 'questions', 'topics'])->first();
        return view('admin/answer/edit', compact('answer'));
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
            $answer = AnswerModel::where('id', '=', $id)->first();
            $answer->display = $input['display'];
            $re = $answer->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        if ($re) {
            return redirect('admin/answer');
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
        AnswerImageModel::where('answer_id', '=', $id)->delete();
        $re = AnswerModel::where('id', '=', $id)->delete();
        if ($re) {
            $data = [
                'status' => 0,
                'msg' => '回答删除成功！'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => '回答删除失败，请稍后重试！'
            ];
        }
        return $data;
    }
}
