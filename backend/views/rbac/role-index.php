<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/16
 * Time: 19:44
 */
?>
<a href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>" class="btn btn-primary">添加角色</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>操作</th>
    </tr>
    <?php foreach ($roles as $role):?>
    <tr id="<?=$role->name?>">
        <td><?=$role->name?></td>
        <td><?=$role->description?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['rbac/edit-role','name'=>$role->name])?>" class="btn btn-default">修改</a>
            <a href="javascript:void(0)" class="btn btn-default del-btn">删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>

<?php
//注册js
/**
 * @var $this yii\web\View
 */
$del_url = \yii\helpers\Url::to(['rbac/delete-role']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('.del-btn').click(function(){
            var tr = $(this).closest('tr');
            var id = tr.attr('id');
            
            $.post('${del_url}',{'id':id}, function(data) {
              if(data == 'success'){
                  tr.remove();
                  alert('删除成功');
              }else{
                  alert('删除失败');
              }
            });
        });
JS

));