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
        <a href="{{url('admin/info')}}">首页</a>&raquo; <span>动态管理</span>
    </div>
    <div class="operation-container">
        <table class="list_tab">
            <tr>
                <th class="tc" width="5%">ID</th>
                <th>编号</th>
                <th>动态类型</th>
                <th>评论数</th>
                <th>点赞数</th>
                <th>发布者</th>
                <th>发布时间</th>
                <th width="20%">操作</th>
            </tr>
            @foreach($article as $a)
                <tr>
                    <td class="tc">{{$a->id}}</td>
                    <td>{{$a->number}}</td>
                    @if($a->type=='Daily')
                        <td>日常</td>
                    @elseif($a->type=='Photo')
                        <td>摄影</td>
                    @else
                        <td>视频</td>
                    @endif
                    <td>{{$a->comments_count}}</td>
                    <td>{{$a->likes}}</td>
                    <td>{{$a->users->nickname}}</td>
                    <td>{{$a->created_at}}</td>
                    <td class="select-container">
                        <a href="{{url('admin/article/'.$a->id.'/edit')}}">
                            <button class="on-button">管理</button>
                        </a>
                        <a href="javascript:" onclick="deleteArticle({{$a->id}})">
                            <button class="on-button">删除</button>
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="page_list">
            <!-- 分页实现 -->
            {{$article->links()}}
        </div>
    </div>
</div>
<script>
    function deleteArticle(id) {
        layer.confirm('您确定要删除该动态吗？', {
            btn: ['确定', '取消']
        }, function () {
            $.post("{{url('admin/article/')}}/" + id, {
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