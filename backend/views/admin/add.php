<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:55
 */
//表单开始
$form = \yii\bootstrap\ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>true])->radioList([0=>'禁用',1=>'启用']);

echo $form->field($model,'roles')->checkboxList(\backend\models\RoleForm::getRoleItems());

echo '<button type="submit" class="btn btn-info">提交</button>';
//表单结束
\yii\bootstrap\ActiveForm::end();