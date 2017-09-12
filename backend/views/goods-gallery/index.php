<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 23:21
 */
use yii\web\JsExpression;
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);

//输出img标签
echo \yii\bootstrap\Html::img($model->path,['id'=>'img']);
echo $form->field($model, 'path')->hiddenInput();
//============uploadifive开始
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);

$add_url = \yii\helpers\Url::to(['goods-gallery/save']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
        function(file, errorCode, errorMsg, errorString) {
            console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
        }
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
        function(file, data, response) {
            data = JSON.parse(data);
            if (data.error) {
                console.log(data.msg);
            } else {
                console.log(data.fileUrl);
                // 上传图片成功
                // 使用ajax将图片保存到数据库
                $.post('{$add_url}', {goods_id: '{$goods_id}', path: data.fileUrl}, function(id) {
                    // 将图片添加到table显示出来
                    var td1 = $("<td><img src='"+ data.fileUrl +"' style='max-width: 500px'></td>");
                    var td2 = $("<td><a href='javascript:void(0)' class='btn btn-danger del-btn'>删除</a></td>");
                    var tr = $("<tr id="+id+"></tr>");
                    tr.append(td1);
                    tr.append(td2);
                    
                    // tr添加到table
                    $('.table').append(tr);
                })
            }
        }
EOF
        ),
    ]
]);
//============uploadifive结束
\yii\bootstrap\ActiveForm::end();
?>

<table class="table">
<?php foreach ($goodsGallerys as $goodsGallery):?>
    <tr id="<?=$goodsGallery->id?>">
        <td><img src="<?=$goodsGallery->path?>" style="max-width:500px"/></td>
        <td><a href="javascript:void(0)" class="btn btn-danger del-btn">删除</a></td>
    </tr>
<?php endforeach;?>
</table>

<?php
$del_url = \yii\helpers\Url::to(['goods-gallery/delete']);
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
        //动态绑定click事件
        $('table').on('click', '.del-btn', function(){
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
