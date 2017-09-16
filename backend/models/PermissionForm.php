<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/15
 * Time: 11:54
 */
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{
    //定义变量
    public $name;   //权限名称
    public $description;   //权限的描述
    public $oldName;

    //常量定义场景
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';


    //定义规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
            ['name','validateName','on'=>[self::SCENARIO_ADD]],
            ['name','validateOldName','on'=>[self::SCENARIO_EDIT]]
        ];
    }

    //验证权限名称
    public function validateName(){
        //只管问题
        if(\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }


    public function validateOldName()
    {
        //输入的权限已存在.getPermission查询权限是否存在,返回null或者对象.
        if ($this->oldName != $this->name &&\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }

    //定义标签字段名
    public function attributeLabels()
    {
        return [
            'name'=>'权限名称',
            'description'=>'描述'
        ];
    }
}