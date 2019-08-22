<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{asset('resources/views/admin/topic/topic.css')}}">
    <script type="text/javascript" src="{{asset('resources/views/admin/js/jquery.js')}}"></script>
    <script type="text/javascript" src="{{asset('resources/views/admin/layer/layer.js')}}"></script>
</head>
<body>
<div class="container">
    <div class="position-container">
        <img src="{{url('resources/views/admin/images/info.svg')}}"></img>
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>用户管理</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>名称</th>
                <th>性别</th>
                <th>关注数</th>
                <th>粉丝数</th>
                <th>注册时间</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($user as $u)
                <tr>
                    <td class="tc">{{$u->id}}</td>
                    <td>{{$u->nickname}}</td>
                    @if($u->sex == 1)
                        <td>男</td>
                    @else
                        <td>女</td>
                    @endif
                    <td>{{$u->attention}}</td>
                    <td>{{$u->follower}}</td>
                    <td>{{$u->created_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/user/'.$u->id.'/edit')}}">
                            <button class="on-button">管理</button>
                        </a>
                        <a href="javascript:" onclick="deleteUser({{$u->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$user->links()}}
        </div>
    </div>
</div>
<script>
    function deleteUser(id) {
        layer.confirm('您确定要删除该用户吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/user/')}}/" + id, {
                '_method': 'delete',
                '_token': "{{csrf_token()}}"
            }, function (data) {
                if (data.status == 0) {
                    location.href = location.href;
                    layer.msg(data.msg, {icon: 6});
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            })
        }, function () {

        })
    }
</script>
</body>
</html>