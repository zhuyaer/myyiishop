<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/16
 * Time: 16:20
 */
$form = \yii\widgets\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'description')->textInput();
echo $form->field($model,'permissions')->checkboxList(\backend\models\RoleForm::getPermissionItems());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\widgets\ActiveForm::end();