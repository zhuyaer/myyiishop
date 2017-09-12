<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 14:37
 */
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsDayCount extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['day','count'],'required']
        ];
    }
    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'day'=>'日期',
            'count'=>'商品数'
        ];
    }

}