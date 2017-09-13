<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:29
 */
?>
<a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-primary">添加用户</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach ($admins as $admin):?>
    <tr id="<?=$admin->id?>">
        <td><?=$admin->id?></td>
        <td><?=$admin->username?></td>
        <td><?=$admin->email?></td>
        <td><?=$admin->status?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['admin/edit', 'id'=>$admin->id])?>" class="btn btn-default">修改</a>
            <a href="javascript:void(0)" class="btn btn-default del-btn">删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>