<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/9
 * Time: 11:06
 */
?>
<a href="<?=\yii\helpers\Url::to(['article-detail/add'])?>" class="btn btn-primary">添加文章详情</a>
<table class="table table-bordered table-responsive">
    <tr>
        <th>Article_Id</th>
        <th>名称</th>
        <th>操作</th>
    </tr>
    <?php foreach ($ArticleDetails as $ArticleDetail):?>
    <tr id="<?=$ArticleDetail->article_id?>">
        <td><?=$ArticleDetail->article_id?></td>
        <td><?=$ArticleDetail->content?></td>
        <td>
            <a href="<?=\yii\helpers\Url::to(['article-detail/edit', 'id'=>$ArticleDetail->article_id])?>" class="btn btn-default">修改</a>
            <a href="javascript:void(0)" class="btn btn-default del-btn")">删除</a>
        </td>
    </tr>
<?php endforeach;?>
</table>

<?php
    //注册js
    /**
     * @var $this \yii\web\View
     */
    $del_url = \yii\helpers\Url::to(['article-detail/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
            $('.del-btn').click(function() {
                var tr = $(this).closest('tr');
                var id = tr.attr('id');
                
                $.post('{$del_url}',{'id': id}, function(data){
                    if(data == 'success'){
                        tr.remove();
                        alert('删除成功');
                    } else {
                        alert('删除失败');
                    }
                });
            });
JS

    ));