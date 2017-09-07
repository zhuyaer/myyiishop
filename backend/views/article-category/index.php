<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 18:44
 */
?>
<a href="<?=\yii\helpers\Url::to(['article-category/add'])?>" class="btn btn-primary">添加文章分类</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($ArticleCategorys as $ArticleCategory):?>
        <tr id="<?=$ArticleCategory->id?>">
            <td><?=$ArticleCategory->id?></td>
            <td><?=$ArticleCategory->name?></td>
            <td><?=$ArticleCategory->intro?></td>
            <td><?=$ArticleCategory->sort?></td>
            <td><?=$ArticleCategory->status?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['article-category/edit', 'id'=>$ArticleCategory->id])?>">修改</a>
                <a href="javascript:void(0)" onclick="del(<?=$ArticleCategory->id?>)">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<script>

    /**
     * 使用jquery库中的ajax删除，使用ajax避免重新刷新列表（查询）
     * @param id
     */
    function del(id) {
        $.ajax({
            url: '/index.php/article-category/delete?id='+id,     // 请求的地址
            type: 'get',                                             // get请求
            success: function (data) {
                //alert(data);                                      //打印从服务端返回的值（/brand/delete返回的值）
                $('#'+id).children('td').eq(4).html('删除');      //设置第五列为删除（将正常变为删除），$('#'+id)为tr的id
            },
            error: function (err) {                             //发生错误
                alert('删除失败');
            }
        })
    }

</script>
