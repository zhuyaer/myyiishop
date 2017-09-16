<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/16
 * Time: 16:10
 */
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    //定义变量,角色名称，角色描述，角色允许的权限
    public $name;
    public $description;
    public $permissions;
    public $oldName;

    //常量定义场景
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';


    //定义规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
            //没有规则
            ['permissions','safe'],
            ['name','validateName','on'=>[self::SCENARIO_ADD]],
            ['name','validateOldName','on'=>[self::SCENARIO_EDIT]]
        ];
    }

//    //验证权限名称
    public function validateName(){
        //只管问题
        if(\Yii::$app->authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
        }
    }


    public function validateOldName()
    {
        //输入的角色已存在.getPermission查询角色是否存在,返回null或者对象.
        if ($this->oldName != $this->name &&\Yii::$app->authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }



    //遍历权限
    public static function getPermissionItems(){
        $permissions = \Yii::$app->authManager->getPermissions();
        $items = [];
        foreach ($permissions as $permission){
            $items[$permission->name] = $permission->description;
        }
        return $items;
    }

    /**
     * 获取指定$name用户的角色
     * @param $name
     * @return array
     */
    public static function getPermissionsByName($name){
        $permissions = \Yii::$app->authManager->getPermissionsByRole($name);
        $items = [];
        foreach ($permissions as $permission){
            $items[$permission->name] = $permission->name;
        }
        return $items;
    }



    //遍历角色
    public static function getRoleItems() {
        $roles = \Yii::$app->authManager->getRoles();
        $items = [];
        foreach ($roles as $key =>$role){
            $items[$key] = $role->name;
        }
        return $items;
    }

    /**
     * 获取用户为userid的角色列表
     * @param $id
     * @return array
     */
    public static function getRoleItemsById($id) {
        $roles = \Yii::$app->authManager->getRolesByUser($id);
        $items = [];
        foreach ($roles as $role){
            $items[$role->name] = $role->name;
        }
        return $items;
    }


}