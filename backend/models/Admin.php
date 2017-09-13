<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:32
 */
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
class Admin extends ActiveRecord implements IdentityInterface {
    //定义规则
    public function rules()
    {
        return [

        ];
    }

    //定义标签字段名
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
        ];
    }

    // IdentityInterface 接口实现
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }
}