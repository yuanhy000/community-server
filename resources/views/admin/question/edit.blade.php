<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('resources/views/admin/topic/topic.css')}}">
    <link rel="stylesheet" href="{{asset('resources/views/admin/article/article.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('resources/views/admin/upload/style.css')}}"/>

</head>
<body>
<div class="container">
    <div class="position-container">
        <img src="{{url('resources/views/admin/images/info.svg')}}"></img>
        <a href="{{url('admin/info')}}">首页</a>&raquo;&nbsp;&nbsp;<a href="{{url('admin/topic')}}">话题管理</a>&raquo; <span>话题详情</span>
    </div>
    <div class="operation-container">
        <div class="result_title">
            @if(count($errors)>0)
                <div class="mark">
                    @foreach($errors->all() as $error)
                        <p>{{$error}}</p>
                    @endforeach
                </div>
            @endif
            @if(session('_error'))
                <div class="mark">
                    <p>{{session('_error')}}</p>
                </div>
                <?php session(['_error' => null]);?>
            @endif
        </div>
        <form action="{{url('admin/question/'.$question->id)}}" method="post">
            <input type="hidden" name="_method" value="put">
            @csrf
            <div class="content-container">
                <div class="left-container">
                    <div class="info-title">
                        话题基本信息
                    </div>
                    <div class="form-item">
                        <div class="item-name">
                            话题标题
                        </div>
                        <input type="text" class="item-input" name="title" value="{{$question->title}}">
                    </div>
                    <div class="form-item">
                        <div class="item-name">
                            话题描述
                        </div>
                        <textarea type="text" class="item-textare" name="content">{{$question->content}}</textarea>
                    </div>

                    <div class="bottom-container">
                        <input type="submit" class='btn btn-bottom' value="提交">
                        <input type="button" class="btn btn-bottom" onclick="history.go(-1)" value="返回">
                    </div>
                </div>
                <div class="right-container">
                    <div class="info-title">
                        图片信息
                    </div>
                    <div class="image-container">
                        @foreach($question->images as $image)
                            <img class="item-image" src="{{$image->url}}" alt="">
                        @endforeach
                    </div>
                    <div class="info-title">
                        更多信息
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            编号&nbsp;&nbsp;&nbsp;
                        </div>
                        <input class="item-input" readonly value="{{$question->number}}">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            关注数
                        </div>
                        <input class="item-input" readonly value="{{$question->answer_count}}">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            回答数
                        </div>
                        <input class="item-input" readonly value="{{$question->likes}}">
                    </div>
                    <a class="answer-button" href="{{url('admin/question_answer/'.$question->id)}}" target="main">
                        查看回答
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript"
        src="{{asset('resources/views/admin/upload/lib/crypto1/crypto/crypto.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/views/admin/upload/lib/crypto1/hmac/hmac.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/views/admin/upload/lib/crypto1/sha1/sha1.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/views/admin/upload/lib/base64.js')}}"></script>
<script type="text/javascript"
        src="{{asset('resources/views/admin/upload/lib/plupload-2.1.2/js/plupload.full.min.js')}}"></script>
<script type="text/javascript" src="{{asset('resources/views/admin/upload/upload.js')}}"></script>
</body>
</html>