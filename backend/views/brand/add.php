<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 16:14
 */
use yii\web\JsExpression;
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList([0 =>'隐藏',1=>'正常']);
//输出img标签
echo \yii\bootstrap\Html::img($model->logo,['id'=>'img']);
echo $form->field($model, 'logo')->hiddenInput();
//============uploadifive开始
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
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
        //将上传文件的路径写入logo字段的隐藏域
        $("#brand-logo").val(data.fileUrl);
        //图片回县
        $('#img').attr('src',data.fileUrl);    
            }
}
EOF
        ),
    ]
]);
//============uploadifive结束


echo '<button type="submit" class="btn btn-info">提交</button>';
//表单结束
\yii\bootstrap\ActiveForm::end();