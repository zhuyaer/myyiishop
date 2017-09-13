<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 16:47
 */
//表单开始
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model, 'remember')->checkbox();
echo  "<div style='color:red'>$error</div><br>";
echo '<button type="submit" class="btn btn-info">登录</button>';
\yii\bootstrap\ActiveForm::end();
//表单结束