<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/19
 * Time: 21:57
 */
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{


    //定义规则
    public function rules()
    {
        return [
            [['username','address', 'tel', 'province', 'city', 'area'],'required'],
            [['default_address'], 'boolean']
        ];
    }

}