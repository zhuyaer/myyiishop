<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/17
 * Time: 13:15
 */
?>
<h4>菜单tree</h4>
<table class="table table-bordered table-responsive">
    <tr>
        <th>名称</th>
        <th>路由</th>
        <th>排序</th>
    </tr>
    <?php foreach ($menus as $menu):?>
    <!--  打印父菜单-->
    <tr id="<?=$menu['menu']->id?>">
        <td><?=$menu['menu']->name?></td>
        <td><?=$menu['menu']->route?></td>
        <td><?=$menu['menu']->sort?></td>
    </tr>

    <!--        打印子菜单-->
     <?php foreach ($menu['items'] as $item):?>
            <tr id="<?=$item->id?>">
                <td>——<?=$item->name?></td>
                <td><?=$item->route?></td>
                <td><?=$item->sort?></td>
            </tr>
     <?php endforeach;?>

<?php endforeach;?>
</table>

<?php
////注册js
///**
// * @var $this yii\web\View
// */
//$del_url = \yii\helpers\Url::to(['menu/delete']);
//$this->registerJs(new \yii\web\JsExpression(
//        <<<JS
//        $('.del-btn').click(function(){
//            var tr = $(this).closest('tr');
//            var id = tr.attr('id');
//
//            $.post('${del_url}',{'id':id}, function(data) {
//              if(data == 'success'){
//                  tr.remove();
//                  alert('删除成功');
//              }else{
//                  alert('删除失败');
//              }
//            });
//        });
//JS
//
//));
