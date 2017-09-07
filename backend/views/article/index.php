<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 19:46
 */
?>
<a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-primary">添加用户</a>
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
        <tr>
            <td><?=$article->id?></td>
            <td><?=$article->name?></td>
            <td><?=$article->intro?></td>
            <td><?=$article->category->name?></td>
            <td><?=$article->sort?></td>
            <td><?=$article->status?></td>
            <td><?=$article->create_time?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['article/edit', 'id'=>$article->id])?>">修改</a>
                <a href="<?=\yii\helpers\Url::to(['article/delete', 'id'=>$article->id])?>">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

