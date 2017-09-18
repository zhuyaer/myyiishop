<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/17
 * Time: 13:08
 */
namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Menu extends ActiveRecord{

    //定义规则
    public function rules()
    {
        return [
            [['parent_id','sort'], 'integer'],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20],
            [['route'], 'string', 'max' => 50],
        ];
    }

    //定义标签字段名
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => '上级分类',
            'name' => '名称',
            'route' => '路由（权限）',
            'sort' => '排序'
        ];
    }


    /**
     * 获取menu菜单
     */
    public static function getMenus() {
        $menuItems = [];
        //获取所有一级菜单
        $menus = Menu::find()->where(['parent_id'=>0])->all();

        foreach($menus as $menu){
            //获取该一级菜单的所有子菜单
            $children = Menu::find()->where(['parent_id'=>$menu->id])->all();
            // 将$menu以及子菜单添加到 $menuItems 数组
            $menuItems[] = ['menu' => $menu, 'items'=>$children];
        }
        return $menuItems;
    }

}