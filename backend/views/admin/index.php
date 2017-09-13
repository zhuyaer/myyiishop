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
    <tr data-id="<?=$admin->id?>">
        <td><?=$admin->id?></td>
        <td><?=$admin->username?></td>
        <td><?=$admin->email?></td>
        <td><?=$admin->status==0 ? '禁用':'启用'?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['admin/edit', 'id'=>$admin->id])?>" class="btn btn-default">修改</a>
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
$del_url = \yii\helpers\Url::to(['admin/delete']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('.del-btn').click(function() {
            var tr = $(this).closest('tr');
            var id = tr.attr('data-id');
            
            $.post("{$del_url}", {'id': id}, function (data){
                if(data == 'success') {
                    tr.remove();
                    alert('删除成功');
                }else{
                    alert('删除失败');
                }
            });
        });
JS

));