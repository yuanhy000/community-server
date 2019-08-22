<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\AnswerModel;
use App\Http\Model\ArticleModel;
use App\Http\Model\ImageModel;
use App\Http\Model\QuestionImageModel;
use App\Http\Model\QuestionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $question = QuestionModel::orderBy('id', 'asc')->paginate(10);
        return view('admin.question.index', compact('question'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.question.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = Input::except('_token');
        $rules = [
            'title' => 'required',
            'content' => 'required',
        ];
        $message = [
            'name.required' => '话题标题不能为空！',
            'description.required' => '话题描述不能为空！',
        ];
        $validator = Validator::make($input, $rules, $message);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $question = new QuestionModel();
                $question->title = $input['title'];
                $question->content = $input['content'];
                $question->number = QuestionModel::makeArticleNumber();
                $question->user_id = 1;
                $re = $question->save();

                if ($input['image_url']) {
                    $image = new ImageModel();
                    $image->url = $input['image_url'];
                    $image->from = 2;
                    $image->save();

                    $questionImage = new QuestionImageModel();
                    $questionImage->question_id = $question->id;
                    $questionImage->img_id = $image->id;
                    $questionImage->save();
                }
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
            if ($re) {
                return redirect('admin/question');
            } else {
                session(['_error' => '发布话题失败，请稍后重试！']);
                return back();
            }
        } else {
            return back()->withErrors($validator);
        }
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
        $question = QuestionModel::where('id', '=', $id)->with(['images', 'users', 'topics'])->first();
        return view('admin/question/edit', compact('question'));
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
        $input = Input::except('_token');
        $rules = [
            'title' => 'required',
            'content' => 'required',
        ];
        $message = [
            'name.required' => '话题标题不能为空！',
            'description.required' => '话题描述不能为空！',
        ];
        $validator = Validator::make($input, $rules, $message);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $question = QuestionModel::where('id', '=', $id)->first();
                $question->title = $input['title'];
                $question->content = $input['content'];
                $re = $question->save();
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
            if ($re) {
                return redirect('admin/question');
            } else {
                session(['_error' => '数据更新失败，请稍后重试！']);
                return back();
            }
        } else {
            return back()->withErrors($validator);
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
        QuestionImageModel::where('question_id', '=', $id)->delete();
        $re = QuestionModel::where('id', '=', $id)->delete();
        if ($re) {
            $data = [
                'status' => 0,
                'msg' => '主题删除成功！'
            ];
        } else {
            $data = [
                'status' => 1,
                'msg' => '主题删除失败，请稍后重试！'
            ];
        }
        return $data;
    }

    public function answerList(Request $request, $id)
    {
        $answer = AnswerModel::where('question_id', '=', $id)->with(['images', 'users', 'questions'])
            ->orderBy('id', 'asc')->paginate(10);
        return view('admin.answer.index', compact('answer'));
    }
}
