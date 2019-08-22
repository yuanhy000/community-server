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
        <a href="{{url('admin/info')}}">首页</a>&raquo;&nbsp;&nbsp;<a href="{{url('admin/topic')}}">用户管理</a>&raquo; <span>用户详情</span>
    </div>
    <div class="operation-container">
        <form action="{{url('admin/user/'.$user->id)}}" method="post">
            <input type="hidden" name="_method" value="put">
            @csrf
            <div class="content-container">
                <div class="left-container">
                    <div class="info-title">
                        基本信息
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            用户名称
                        </div>
                        <input class="item-input" readonly value="{{$user->nickname}}">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            身份标识
                        </div>
                        <input class="item-input" readonly value="{{$user->openid}}">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            用户性别
                        </div>
                        <input class="item-input" readonly @if($user->sex==1)value="男" @else value="女" @endif>
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            关注数&nbsp;&nbsp;&nbsp;
                        </div>
                        <input class="item-input" readonly value="{{$user->attention}}" width="30%">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            粉丝数&nbsp;&nbsp;&nbsp;
                        </div>
                        <input class="item-input" readonly value="{{$user->follower}}" width="30%">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            注册时间
                        </div>
                        <input class="item-input" readonly value="{{$user->created_at}}" width="30%">
                    </div>
                    <div class="item-container">
                        <div class="item-title">
                            显示状态
                        </div>
                        <input name="display" type="hidden" value="FALSE">
                        <label class="switch-btn circle-style">
                            <input class="checked-switch" type="checkbox" name="display" value="TRUE"
                                   @if($user->display=='TRUE') checked @endif/>
                            <span class="text-switch"></span>
                            <span class="toggle-btn"></span>
                        </label>
                    </div>
                </div>
                <div class="right-container">
                    <div class="info-title">
                        发布信息
                    </div>
                    <a class="answer-button" href="{{url('admin/user_article/'.$user->id)}}" target="main">
                        查看动态
                    </a>
                    <a class="answer-button" href="{{url('admin/user_answer/'.$user->id)}}" target="main">
                        查看回答
                    </a>
                    <a class="answer-button" href="{{url('admin/user_comment/'.$user->id)}}" target="main">
                        查看评论
                    </a>
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