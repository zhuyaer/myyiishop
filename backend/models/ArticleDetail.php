<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/9
 * Time: 11:17
 */
namespace backend\models;

use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord{
    //定义规则
    public function rules()
    {
        return [
            [['article_id','content'],'required']
        ];
    }

    //定义字段标签名
    public function attributeLabels()
    {
        return [
            'article_id'=>'文章id',
            'content'=>'简介',
        ];
    }

}
