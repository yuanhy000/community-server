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
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>评论管理</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>评论内容</th>
                <th>点赞数</th>
                <th>评论者</th>
                <th>评论时间</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($comment as $c)
                <tr>
                    <td class="tc">{{$c->id}}</td>
                    <td>{{$c->content}}</td>
                    <td>{{$c->likes}}</td>
                    <td>{{$c->users->nickname}}</td>
                    <td>{{$c->created_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/comment/'.$c->id.'/edit')}}">
                            <button class="on-button">管理</button>
                        </a>
                        <a href="javascript:" onclick="deleteComment({{$c->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$comment->links()}}
        </div>
    </div>
</div>
<script>
    function deleteComment(id) {
        layer.confirm('您确定要删除该评论吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/comment/')}}/" + id, {
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