<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <script type="text/javascript" src="{{asset('resources/views/admin/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/views/admin/index/index.js')}}"></script>
    <link rel="stylesheet" href="{{asset('resources/views/admin/index/index.css')}}">
</head>
<body>
<div class="container" id="container">
    <div class="head-container">
        <div class="head-title">
            <a href="/admin/" class="title-a"><span class="title-important">社区后台</span>管理系统</a>
        </div>
        <div class="head-quit">
            <button class="quit-button">退出</button>
        </div>
    </div>
    <div class="body-container">
        <div class="menu-container">
            <ul>
                <li>
                    <div class="menu-item">主题管理</div>
                    <ul class="sub_menu">
                        <li>
                            <a href="{{url('admin/topic/create')}}" class="sub_item_a" target="main">发布主题</a>
                        </li>
                        <li>
                            <a href="{{url('admin/topic')}}" class="sub_item_a" target="main">主题列表</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="menu-item">动态管理</div>
                    <ul class="sub_menu">
                        {{--<li>--}}
                        {{--<a href="{{url('admin/article/create')}}" class="sub_item_a" target="main">添加主题</a>--}}
                        {{--</li>--}}
                        <li>
                            <a href="{{url('admin/article')}}" class="sub_item_a" target="main">动态列表</a>
                        </li>
                        <li>
                            <a href="{{url('admin/comment')}}" class="sub_item_a" target="main">评论列表</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="menu-item">话题管理</div>
                    <ul class="sub_menu">
                        <li>
                            <a href="{{url('admin/question/create')}}" class="sub_item_a" target="main">发布话题</a>
                        </li>
                        <li>
                            <a href="{{url('admin/question')}}" class="sub_item_a" target="main">话题列表</a>
                        </li>
                        <li>
                            <a href="{{url('admin/answer')}}" class="sub_item_a" target="main">回答列表</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <div class="menu-item">用户管理</div>
                    <ul class="sub_menu">
                        <li>
                            <a href="{{url('admin/user')}}" class="sub_item_a" target="main">用户列表</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="content-container">
            <iframe src="{{url('admin/info')}}" frameborder="0" width="100%" height="100%" name="main">
            </iframe>
        </div>
    </div>
</div>

</body>
</html>