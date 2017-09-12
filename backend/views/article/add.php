<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 22:32
 */
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
//显示所有分类下拉列表
echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($categories,'id','name'));
echo $form->field($model,'sort')->textInput();
//echo $form->field($model, 'create_time')->input('date');
echo $form->field($model,'status',['inline'=>true])->radioList(['-1'=>'删除','0'=>'隐藏','1'=>'正常']);
echo '<button type="submit" class="btn btn-info">提交</button>';
//表单结束
\yii\bootstrap\ActiveForm::end();