<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 11:16
 */
namespace backend\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['name','logo'],'required'],

            [['goods_category_id','brand_id','market_price',
                'shop_price','stock','is_on_sale','status',
                'sort'],'integer'],

            [['name','sn'],'string'],

            [['logo'],'string','max'=>255]
        ];
    }

    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'name'=>'商品名称',
//            'sn'=>'货号',
            'logo'=>'LOGO图片',
            'goods_category_id'=>'商品分类',
            'brand_id'=>'品牌分类',
            'market_price'=>'市场价格',
            'shop_price'=>'商品价格',
            'stock'=>'库存',
            'is_on_sale'=>'是否在售',
            'status'=>'状态',
            'sort'=>'sort',
//            'create_time'=>'添加时间',
//            'view_times'=>'浏览次数'
        ];
    }
}