<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{--<link rel="stylesheet" href="{{asset('resources/views/admin/topic/topic.css')}}">--}}
    <link rel="stylesheet" href="{{asset('resources/views/admin/article/article.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('resources/views/admin/upload/style.css')}}"/>

</head>
<body>
<div class="container">
    <div class="position-container">
        <img src="{{url('resources/views/admin/images/info.svg')}}"></img>
        <a href="{{url('admin/info')}}">首页</a>&raquo;&nbsp;&nbsp;<a href="{{url('admin/topic')}}">动态管理</a>&raquo; <span>动态详情</span>
    </div>
    <div class="operation-container">
        <form action="{{url('admin/article/'.$article->id)}}" method="post">
            <input type="hidden" name="_method" value="put">
            @csrf
            <div class="content-container">
                <div class="left-container">
                    <div class="info-title">
                        基本信息
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            编号
                        </div>
                        <input class="item-input" readonly value="{{$article->number}}">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            类型
                        </div>
                        <input class="item-input" readonly value="{{$article->type}}">
                    </div>
                    @if($article->title)
                        <div class="item-container">
                            <div class="item-title">
                                标题
                            </div>
                            <input class="item-input" readonly value="{{$article->title}}">
                        </div>
                    @endif
                    <div class="item-container">
                        <div class="item-title">
                            内容
                        </div>
                        <textarea type="text" readonly class="item-textare"
                                  name="description">{{$article->content}}</textarea>
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            显示状态
                        </div>
                        <input name="display" type="hidden" value="FALSE">
                        <label class="switch-btn circle-style">
                            <input class="checked-switch" type="checkbox" name="display" value="TRUE"
                                   @if($article->display=='TRUE') checked @endif/>
                            <span class="text-switch"></span>
                            <span class="toggle-btn"></span>
                        </label>
                    </div>
                </div>
                <div class="right-container">
                    <div class="info-title">
                        图片信息
                    </div>
                    <div class="image-container">
                        @foreach($article->images as $image)
                            <img class="item-image" src="{{$image->url}}" alt="">
                        @endforeach
                    </div>
                    <div class="info-title">
                        视频信息
                    </div>
                    <div class="image-container">
                        @if($article->videos)
                            @foreach($article->videos as $video)
                                <video class="item-video" src="{{$video->video_url}}" controls alt="">
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="bottom-container">
                <input type="submit" class='btn btn-bottom' value="提交">
                <input type="button" class="btn btn-bottom" onclick="history.go(-1)" value="返回">
            </div>
        </form>
    </div>
</div>
</body>
</html>