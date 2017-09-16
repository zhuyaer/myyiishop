<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'oldPassword')->textInput();
echo $form->field($model,'newPassword')->textInput();
echo $form->field($model,'rePassword')->textInput();
echo \yii\bootstrap\Html::submitButton('修改密码',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();