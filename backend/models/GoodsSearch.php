<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/12
 * Time: 16:11
 */
namespace backend\models;

use yii\base\Model;
use yii\db\ActiveQuery;
class GoodsSearch extends Model{
    public $name;
    public $sn;
    public $minPrice;
    public $maxPrice;

    public function rules()
    {
        return [
            ['name','string','max'=>50],
            ['sn','string'],
            [['minPrice','maxPrice'],'integer']
        ];
    }

    public function search(ActiveQuery $query){
        //加载表单提交的数据
        $this->load(\Yii::$app->request->get());
        if($this->name){
            $query->andWhere(['like','name',$this->name]);
        }
        if($this->sn){
            $query->andWhere(['like','sn',$this->sn]);
        }
        if($this->maxPrice){
            $query->andWhere(['<=','shop_price',$this->maxPrice]);
        }
        if($this->minPrice){
            $query->andWhere(['>=','shop_price',$this->minPrice]);
        }
    }
}