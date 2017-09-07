<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 19:35
 */
namespace backend\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['intro'],'string'],
            [['sort','status','article_category_id','create_time'],'integer'],
            [['name'],'string','max'=>50],
        ];
    }

    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'name'=>'名称',
            'intro'=>'简介',
            'article_category_id'=>'文章分类id',
            'sort'=>'排序',
            'status'=>'状态',
            'create_time'=>'添加时间'
        ];
    }

    public function getCategory() {
        return $this->hasOne(ArticleCategory::className(), ['id'=>'article_category_id']);
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