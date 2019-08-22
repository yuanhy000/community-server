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
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>主题管理</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>主题名称</th>
                <th>文章数</th>
                <th>关注数</th>
                <th>创建时间</th>
                <th>最近更新</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($topic as $t)
                <tr>
                    <td class="tc">{{$t->id}}</td>
                    <td>{{$t->name}}</td>
                    <td>{{$t->article_count}}</td>
                    <td>{{$t->followers_count}}</td>
                    <td>{{$t->created_at}}</td>
                    <td>{{$t->updated_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/topic/'.$t->id.'/edit')}}">
                            <button class="on-button">修改</button>
                        </a>
                        <a href="javascript:" onclick="deleteTopic({{$t->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$topic->links()}}
        </div>
    </div>
</div>
<script>
    function deleteTopic(id) {
        layer.confirm('您确定要删除该主题吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/topic/')}}/" + id, {
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