<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 14:42
 */
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsGallery extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['goods_id'],'required'],
            [['path'],'string']
        ];
    }

    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'goods_id'=>'商品id',
            'path'=>'图片地址'
        ];
    }
}