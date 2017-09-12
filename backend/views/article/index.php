<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 19:46
 */
?>
<a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-primary">添加文章</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($articles as $article):?>
        <tr id="<?=$article->id?>">
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->category->name?></td>
            <td><?=$article->sort?></td>
            <td><?=$article->status?></td>
            <td><?=$article->create_time?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['article/edit', 'id'=>$article->id])?>" class="btn btn-default">修改</a>
                <a href="javascript:void(0)" class="btn btn-default del-btn">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
    //注册js
    /**
     * @var $this \yii\web\View
     */
    $del_url = \yii\helpers\Url::to(['article/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
            $('.del-btn').click(function() {
                var tr = $(this).closest('tr');
                var id = tr.attr('id');
                
                $.post("{$del_url}", {'id': id}, function (data) {
                    if (data == 'success') {
                        tr.remove();    //移除当前tr
                        alert('删除成功');
                    } else {
                        alert('删除失败');
                    }
                });
            });
JS
    ));


