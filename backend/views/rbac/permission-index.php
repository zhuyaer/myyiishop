<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/15
 * Time: 13:14
 */
?>

<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>权限名称</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($permissions as $permission):?>
        <tr id="<?=$permission->name?>">
            <td><?=$permission->name?></td>
            <td><?=$permission->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['rbac/edit-permission','name'=>$permission->name])?>" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</a>
                <a href="javascript:void(0)" class="btn btn-danger del-btn btn-sm"><span class="glyphicon glyphicon-trash"></span>删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
//注册js
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('http://cdn.datatables.net/1.10.15/css/jquery.dataTables.css');
$this->registerJsFile('http://cdn.datatables.net/1.10.15/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$del_url = \yii\helpers\Url::to(['rbac/delete-permission']);
$this->registerJs(new yii\web\JsExpression(
        <<<JS
             $(document).ready( function () {
                $('#table_id_example').DataTable();
                } );
        
            $('.del-btn').click(function(){
            var tr = $(this).closest('tr');
            var id = tr.attr('id');
            
            $.post('{$del_url}',{'id': id}, function(data) {
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
