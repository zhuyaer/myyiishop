<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/17
 * Time: 13:15
 */
?>
<a href="<?=\yii\helpers\Url::to(['menu/add'])?>" class="btn btn-primary">添加菜单</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>路由</th>
        <th>操作</th>
    </tr>
    <?php foreach ($menus as $menu):?>
    <tr id="<?=$menu->id?>">
        <td><?=$menu->id?></td>
        <td><?=$menu->name?></td>
        <td><?=$menu->route?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['menu/edit','id'=>$menu->id])?>" class="btn btn-default">修改</a>
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
$del_url = \yii\helpers\Url::to(['menu/delete']);
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
