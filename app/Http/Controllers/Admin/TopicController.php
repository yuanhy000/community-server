<?php

namespace App\Http\Controllers\Admin;

use App\Http\Model\ImageModel;
use App\Http\Model\TopicModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $topic = TopicModel::orderBy('id', 'asc')->paginate(10);
        return view('admin.topic.index', compact('topic'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.topic.add');
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
//        dd($input);
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'image_url' => 'required',
        ];

        $message = [
            'name.required' => '主题名称不能为空！',
            'description.required' => '主题描述不能为空！',
            'image_url.required' => '主题缩略图不能为空！',
        ];
        $validator = Validator::make($input, $rules, $message);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $topic = new TopicModel();
                $topic->name = $input['name'];
                $topic->description = $input['description'];

                $image = new ImageModel();
                $image->url = $input['image_url'];
                $image->from = 2;
                $image->save();
                $topic->topic_img_id = $image->id;
                $re = $topic->save();
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                throw $exception;
            }
            if ($re) {
                return redirect('admin/topic');
            } else {
                session(['_error' => '添加主题失败，请稍后重试！']);
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
        $topic = TopicModel::where('id', '=', $id)->with('image')->first();
        return view('admin/topic/edit', compact('topic'));
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
            $topic = TopicModel::where('id', '=', $id)->first();
            $topic->name = $input['name'];
            $topic->description = $input['description'];

            $image = ImageModel::where('id', '=', $topic->topic_img_id)->first();
            if ($input['image_url'] && $image->url != $input['image_url']) {
                $image->url = $input['image_url'];
                $image->from = 2;
                $image->save();
            }
            $topic->topic_img_id = $image->id;
            $re = $topic->save();
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        if ($re) {
            return redirect('admin/topic');
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
        $re = TopicModel::where('id', '=', $id)->delete();
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
}
