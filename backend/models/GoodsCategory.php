<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/10
 * Time: 11:44
 */
namespace backend\models;

use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\ArrayHelper;


class GoodsCategory extends ActiveRecord{
    //1.定义规则
    public function rules()
    {
        return [
            [['name','parent_id'],'required'],
            [['tree','lft','rgt','depth','parent_id'],'integer'],
            [['intro'],'string'],
            [['name'],'string','max'=>255]
        ];
    }
    //2.定义字段标签名
    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'tree'=>'树id',
            'lft'=>'左值',
            'rgt'=>'右值',
            'depth'=>'层级',
            'name'=>'名称',
            'parent_id'=>'上级分类id',
            'intro'=>'简介',
        ];
    }

    //获取商品分类的ztree数据
    public static function getZNodes(){
        $top = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        $goodsCategories =  GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return ArrayHelper::merge([$top], $goodsCategories);
    }


    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }
}