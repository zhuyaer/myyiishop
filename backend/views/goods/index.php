<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 11:08
 */
?>
<a href="<?=\yii\helpers\Url::to(['goods/add'])?>" class="btn btn-primary">添加商品</a>

<!-- 搜索 -->
<!--<form id="w0" class="form-inline" action="/goods/index" method="get" role="form">
    <div class="form-group field-goodssearchform-name has-success">
        <input type="text" id="goodssearchform-name" class="form-control"
               name="GoodsSearchForm[name]" placeholder="商品名" aria-invalid="false">
    </div>
    <div class="form-group field-goodssearchform-sn has-success">
        <input type="text" id="goodssearchform-sn" class="form-control"
               name="GoodsSearchForm[sn]" placeholder="货号" aria-invalid="false">
    </div>
    <div class="form-group field-goodssearchform-minprice has-success">
        <input type="text" id="goodssearchform-minprice" class="form-control"
               name="GoodsSearchForm[minPrice]" placeholder="￥" aria-invalid="false">
    </div>
    <div class="form-group field-goodssearchform-maxprice">
        <label class="sr-only" for="goodssearchform-maxprice">-</label>
        <input type="text" id="goodssearchform-maxprice" class="form-control"
               name="GoodsSearchForm[maxPrice]" placeholder="￥">
    </div>
    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>搜索</button></form>
-->
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>商品名</th>
        <th>货号</th>
        <th>LOGO图片</th>
        <th>商品分类id</th>
        <th>品牌分类</th>
        <th>市场价格</th>
        <th>商品价格</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>状态</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>浏览</th>
        <th width="20%">操作</th>
    </tr>
    <?php foreach ($goods as $good):?>
        <tr id="<?=$good->id?>">
            <td><?=$good->id?></td>
            <td><?=$good->name?></td>
            <td><?=$good->sn?></td>
            <td><img src="<?=$good->logo?>" width="150px"/></td>
            <td><?=$good->goods_category_id?></td>
            <td><?=$good->brand_id?></td>
            <td><?=$good->market_price?></td>
            <td><?=$good->shop_price?></td>
            <td><?=$good->stock?></td>
            <td><?=$good->is_on_sale?></td>
            <td><?=$good->status?></td>
            <td><?=$good->sort?></td>
            <td><?=$good->create_time?></td>
            <td><?=$good->view_times?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods-gallery/index', 'id'=>$good->id])?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-picture"></span>&nbsp;相册</a>
                <a href="<?=\yii\helpers\Url::to(['goods/edit', 'id'=>$good->id])?>" class="btn btn-warning btn-sm"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</a>
                <a href="javascript:void(0)" class="btn btn-danger del-btn btn-sm"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</a>
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
$del_url = \yii\helpers\Url::to(['goods/delete']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('.del-btn').click(function(){
            var tr = $(this).closest('tr');
            var id = tr.attr('id');
            
            $.post("{$del_url}",{'id':id},function(data){
                if(data == true){
                    tr.remove();    //移除当前tr
                    alert('删除成功');
                } else {
                    alert('删除失败');
                }
            });
        });
JS

));


?>
