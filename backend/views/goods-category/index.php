<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/10
 * Time: 11:41
 */
?>
<a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>" class="btn btn-primary">添加商品分类</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>树id</th>
        <th>左值</th>
        <th>右值</th>
        <th>层级</th>
        <th>名称</th>
        <th>上级分类id</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach ($GoodsCategorys as $GoodsCategory):?>
    <tr id="<?=$GoodsCategory->id?>">
        <td><?=$GoodsCategory->id?></td>
        <td><?=$GoodsCategory->tree?></td>
        <td><?=$GoodsCategory->lft?></td>
        <td><?=$GoodsCategory->rgt?></td>
        <td><?=$GoodsCategory->depth?></td>
        <td><?=$GoodsCategory->name?></td>
        <td><?=$GoodsCategory->parent_id?></td>
        <td><?=$GoodsCategory->intro?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['goods-category/edit', 'id'=>$GoodsCategory->id])?>" class="btn btn-default">修改</a>
            <a href="javascript:void(0)" class="btn btn-default del-btn">删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>

<?php

//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$pager
]);

//注册js
/**
* @var $this \yii\web\View
*/
$del_url = \yii\helpers\Url::to(['goods-category/delete']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('.del-btn').click(function(){
            var tr = $(this).closest('tr');
            var id = tr.attr('id');
            
            $.post("{$del_url}",{'id':id},function(data){
                if(data == 'ok'){
                     tr.remove();    //移除当前tr
                     alert('删除成功');
                } else if (data == 'exit') {
                    alert('该节点存在子元素');
                }else{
                    alert('删除失败');
                }
            });
        });

JS

));


