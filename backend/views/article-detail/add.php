<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/9
 * Time: 11:06
 */
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model,'article_id')->dropDownList(\yii\helpers\ArrayHelper::map($articles,'id','name'));
echo $form->field($model,'content')->textarea();
echo '<button type="submit" class="btn btn-info">提交</button>';
//表单结束
\yii\bootstrap\ActiveForm::end();