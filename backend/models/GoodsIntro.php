<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 14:19
 */
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['content'],'string']
        ];
    }

    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'goods_id' => '商品id',
            'content' => '商品描述'
        ];
    }
}