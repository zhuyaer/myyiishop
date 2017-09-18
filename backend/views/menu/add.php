<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/17
 * Time: 13:15
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'parent_id')->
dropDownList(\yii\helpers\ArrayHelper::map($menu,'id','name'),['prompt'=>'==请选择上级分类==']);
echo $form->field($model,'route')->
dropDownList(\yii\helpers\ArrayHelper::map($permissions,'name','description'),['prompt'=>'==请选择路由==']);
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();