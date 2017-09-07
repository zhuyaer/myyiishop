<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 15:42
 */
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    //定义变量
    public $file;
    //定义规则
    public function rules()
    {
        return [
            [['intro'],'string'],
            [['sort','status'],'integer'],
            [['name'],'string','max'=>50],
            [['logo'],'string','max'=>255],
            ['file', 'file', 'skipOnEmpty'=>false, "extensions"=>"jpg,png,gif"],
        ];
    }

    //定义字段标签名称
    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'name'=>'名称',
            'intro'=>'简介',
            'logo'=>'LOGO图片',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }

    /**
     *
     */
    public function afterFind()
    {
        $s = $this->status;
        if ($s == 1) {
            $this->status = '正常';
        } else if ($s == 0) {
            $this->status = '隐藏';
        } else {
            $this->status = '删除';
        }
        return true;
    }

}