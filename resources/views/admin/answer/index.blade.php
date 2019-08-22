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
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>回答列表</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>所属话题</th>
                <th>评论数</th>
                <th>点赞数</th>
                <th>发布者</th>
                <th>发布时间</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($answer as $a)
                <tr>
                    <td class="tc">{{$a->id}}</td>
                    <td>{{$a->questions->title}}</td>
                    <td>{{$a->comments_count}}</td>
                    <td>{{$a->likes}}</td>
                    <td>{{$a->users->nickname}}</td>
                    <td>{{$a->created_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/answer/'.$a->id.'/edit')}}">
                            <button class="on-button">管理</button>
                        </a>
                        <a href="javascript:" onclick="deleteAnswer({{$a->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$answer->links()}}
        </div>
    </div>
</div>
<script>
    function deleteAnswer(id) {
        layer.confirm('您确定要删除该回答吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/answer/')}}/" + id, {
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