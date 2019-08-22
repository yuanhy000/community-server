<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('resources/views/admin/login/login.css')}}">
</head>
<body>

<div class="login_box">
    <div class="login-tag">
        <div class="company-name">
            <img src="/resources/views/admin/images/logo.png">
            <span>后台管理</span>
        </div>
        <div class="system-name">
            <span>Community&nbsp;&nbsp;Content&nbsp;&nbsp;Manage&nbsp;&nbsp;System</span>
        </div>
    </div>
    @if(session('msg'))
        <span class="error-tip">{{session('msg')}}</span>
    @endif
    <div class="login-form">
        <form action="{{url('/admin/login')}}" role="form" method="post">
            {{ csrf_field() }}
            <div>
                <input class="input-box" type="text" name="user_name" placeholder="用户名"/>
                <span><i class="fa fa-user"></i></span>
            </div>
            <div>
                <input class="input-box" type="password" name="user_pass" placeholder="密码"/>
                <span><i class="fa fa-lock"></i></span>
            </div>
            <input type="submit" class="submit-btn" id="login" value="登录">
        </form>
    </div>
</div>
</body>
</html>