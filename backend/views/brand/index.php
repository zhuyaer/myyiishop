<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 16:15
 */
?>
<a href="<?=\yii\helpers\Url::to(['brand/add'])?>" class="btn btn-primary">添加brand</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO图片</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($brands as $brand):?>
    <tr id="<?=$brand->id?>">
        <td><?=$brand->id?></td>
        <td><?=$brand->name?></td>
        <td><?=$brand->intro?></td>
        <td><?=$brand->logo?></td>
        <td><?=$brand->sort?></td>
        <td><?=$brand->status?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['brand/edit', 'id'=>$brand->id])?>">修改</a>
            <a href="#" onclick="del(<?=$brand->id?>)">删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>

<script>
    function del(id) {
        $.ajax({
            url: '/index.php/brand/delete?id='+id,     // 请求的地址
            type: 'get',                                // get请求
            success: function (data) {
               //alert(data);                             //打印从服务端返回的值（/brand/delete返回的值）
                $('#'+id).children('td').eq(5).html('删除');  //设置第五列为删除（将正常变为删除），$('#'+id)为tr的id
            },
            error: function (err) {                     //发生错误
                alert('删除失败');
            }
        });
    }
</script>
