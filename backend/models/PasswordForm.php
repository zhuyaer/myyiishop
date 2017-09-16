<?php
namespace backend\models;

use yii\base\Model;

class PasswordForm extends  Model
{
    public $oldPassword;
    public $newPassword;
    public $rePassword;

    public function rules()
    {
        return [
            [['oldPassword', 'newPassword', 'rePassword'], 'required'],
            ['rePassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => '两次密码必须一致'],
            //自定义验证方法
            ['oldPassword', 'validatePassword'],
        ];
    }

    //自定义验证方法  只考虑验证不通过的情况
    public function validatePassword(){
        //验证密码
        if(!\Yii::$app->security->validatePassword($this->oldPassword,\Yii::$app->user->identity->password_hash)){
            $this->addError('oldPassword','旧密码不正确');
        }
    }


    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'rePassword'=>'重复密码',
        ];
    }
}