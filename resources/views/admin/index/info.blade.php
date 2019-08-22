<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('resources/views/admin/index/info.css')}}">
</head>
<body>
<div class="container">
    <div class="position-container">
        <img src="{{url('resources/views/admin/images/info.svg')}}"></img>
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>基本信息</span>
    </div>
    <div class="body-container">
        <div class="operation-container">
            <div class="title-container">
                快捷选项
            </div>
            <div class="operation-content">
                <div class="operation-item">
                    <a href="{{url('admin/topic')}}">主题管理</a>
                </div>
                <div class="operation-item">
                    <a href="{{url('admin/article')}}">动态管理</a>
                </div>
                <div class="operation-item">
                    <a href="{{url('admin/question')}}">话题管理</a>
                </div>
                <div class="operation-item">
                    <a href="{{url('admin/user')}}">用户管理</a>
                </div>
            </div>
        </div>
        <div class="content-container">
            <div class="left-container">
                <div class="title-container">
                    系统基本信息
                </div>
                <div class="base-info">
                    <div class="info-item">
                        <label>操作系统</label><span>{{PHP_OS}}</span>
                    </div>
                    <div class="info-item">
                        <label>运行环境</label><span>{{$_SERVER['SERVER_SOFTWARE']}}</span>
                    </div>
                    <div class="info-item">
                        <label>系统时间</label><span><?php echo date('Y年m月d日 H时i分s秒')?></span>
                    </div>
                    <div class="info-item">
                        <label>版本</label><span>v-1.0</span>
                    </div>
                    <div class="info-item">
                        <label>上传限制</label><span><?php echo get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : "不允许上传附件"; ?> </span>
                    </div>
                    <div class="info-item">
                        <label>服务器域名/IP</label><span>{{$_SERVER['SERVER_NAME']}} [ {{$_SERVER['SERVER_ADDR']}} ]</span>
                    </div>
                    <div class="info-item">
                        <label>Host</label><span>{{$_SERVER['SERVER_ADDR']}}</span>
                    </div>
                    <div class="info-item">
                        <label>开发者</label><span>yuanhy</span>
                    </div>
                </div>
            </div>
            <div class="right-container">
                <div class="title-container">
                    社区基本信息
                </div>
                <div class="base-info">
                    <div class="info-item">
                        <label>日常动态</label><span>{{$dailyCount}} 条</span>
                        <label>参与人数</label><span>{{$dailyPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>摄影动态</label><span>{{$photoCount}} 条</span>
                        <label>参与人数</label><span>{{$photoPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>视频动态</label><span>{{$videoCount}} 条</span>
                        <label>参与人数</label><span>{{$videoPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>话题数目</label><span>{{$questionCount}} 条</span>
                        <label>参与人数</label><span>{{$questionPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>回答数目</label><span>{{$answerCount}} 条</span>
                        <label>参与人数</label><span>{{$answerPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>评论数目</label><span>{{$commentCount}} 条</span>
                        <label>参与人数</label><span>{{$commentPeople}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>活跃人数</label><span>{{$userCount}} 人</span>
                    </div>
                    <div class="info-item">
                        <label>开发者</label><span>yuanhy</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>