<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('resources/views/admin/topic/topic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('resources/views/admin/upload/style.css')}}"/>

</head>
<body>
<div class="container">
    <div class="position-container">
        <img src="{{url('resources/views/admin/images/info.svg')}}"></img>
        <a href="{{url('admin/info')}}">首页</a>&raquo;&nbsp;&nbsp;<a href="{{url('admin/topic')}}">话题管理</a>&raquo; <span>发布话题</span>
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
        <form action="{{url('admin/question')}}" method="post">
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
                        <input type="text" class="item-input" name="title">
                    </div>
                    <div class="form-item">
                        <div class="item-name">
                            话题描述
                        </div>
                        <textarea type="text" class="item-textare" name="content"></textarea>
                    </div>
                    <div class="bottom-container">
                        <input type="submit" class='btn btn-bottom' value="提交">
                        <input type="button" class="btn btn-bottom" onclick="history.go(-1)" value="返回">
                    </div>
                </div>
                <div class="right-container">
                    <div class="info-title">
                        上传附件
                    </div>
                    <div class="form-item">
                        <div class="item-name">上传话题图片：</div>
                        <div id="ossfile">你的浏览器不支持flash,Silverlight或者HTML5！</div>
                        <img src="{{asset('resources/views/admin/topic/image/default.svg')}}" alt="" id="topic-image">
                        <div id="container">
                            <a id="selectfiles" href="javascript:void(0);" class='btn'>选择文件</a>
                            <a id="postfiles" href="javascript:void(0);" class='btn'>开始上传</a>
                        </div>
                        <pre id="console"></pre>
                    </div>
                    <input type="text" id="image-url" name="image_url" style="display: none;">
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