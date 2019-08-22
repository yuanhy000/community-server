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
        <form action="{{url('admin/comment/'.$comment->id)}}" method="post">
            <input type="hidden" name="_method" value="put">
            @csrf
            <div class="content-container">
                <div class="left-container">
                    <div class="info-title">
                        基本信息
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            评论内容
                        </div>
                        <textarea type="text" readonly class="item-textare"
                                  name="description">{{$comment->content}}</textarea>
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            点赞数&nbsp;&nbsp;&nbsp;
                        </div>
                        <input class="item-input" readonly value="{{$comment->likes}}" width="30%">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            显示状态
                        </div>
                        <input name="display" type="hidden" value="FALSE">
                        <label class="switch-btn circle-style">
                            <input class="checked-switch" type="checkbox" name="display" value="TRUE"
                                   @if($comment->display=='TRUE') checked @endif/>
                            <span class="text-switch"></span>
                            <span class="toggle-btn"></span>
                        </label>
                    </div>
                </div>
                <div class="right-container">

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