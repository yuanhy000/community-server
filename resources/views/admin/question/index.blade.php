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
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>话题管理</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>话题名称</th>
                <th>回答数</th>
                <th>关注数</th>
                <th>创建时间</th>
                <th>最近更新</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($question as $q)
                <tr>
                    <td class="tc">{{$q->id}}</td>
                    <td>{{$q->title}}</td>
                    <td>{{$q->answer_count}}</td>
                    <td>{{$q->likes}}</td>
                    <td>{{$q->created_at}}</td>
                    <td>{{$q->updated_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/question/'.$q->id.'/edit')}}">
                            <button class="on-button">管理</button>
                        </a>
                        <a href="javascript:" onclick="deleteQuestion({{$q->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$question->links()}}
        </div>
    </div>
</div>
<script>
    function deleteQuestion(id) {
        layer.confirm('您确定要删除该话题吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/question/')}}/" + id, {
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