<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/15
 * Time: 11:33
 */
namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\rbac\Role;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

class RbacController extends Controller{

    //添加权限
    public function actionAddPermission(){
        $model = new PermissionForm();
        $model->scenario = PermissionForm::SCENARIO_ADD;

        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $auth = \Yii::$app->authManager;
                //添加权限
                //1.创建权限
                $permission = $auth->createPermission($model->name);
                $permission->description = $model->description;
                //2.保存到数据表
                $auth->add($permission);

                return $this->redirect(['permission-index']);
            }
        }
        return $this->render('permission',['model'=>$model]);
    }

    //修改权限
    public function actionEditPermission($name){

        // 根据name获取权限信息
        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);

        // 将上一步获取的权限信息填充到$model对象，用于页面回显
        $model = new PermissionForm();
        $model->oldName = $permission->name;   //oldName用于为数据库查询的旧值，做修改时使用
        $model->scenario = PermissionForm::SCENARIO_EDIT;
        $model->name = $permission->name;
        $model->description = $permission->description;


        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //1.创建权限
                $permission->name = $model->name;
                $permission->description = $model->description;
                //2.保存到数据表
                $auth->update($model->oldName, $permission);

                return $this->redirect(['rbac/permission-index']);
            }
        }

        return $this->render('permission',['model'=>$model]);
    }

    //权限列表
    public function actionPermissionIndex(){
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();

        return $this->render('permission-index',['permissions'=>$permissions]);
    }

    //删除权限
    public function actionDeletePermission(){
        $request = \Yii::$app->request;
        $name = $request->post('id');

        $auth = \Yii::$app->authManager;
        $permission = $auth->getPermission($name);
        $flag = $auth->remove($permission);

        if ($flag) {
            return "success";
        } else {
            return "fail";
        }
    }

    //添加角色
    public function actionAddRole(){
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //保存角色
                $auth = \Yii::$app->authManager;
                //1.添加角色
                //1.1创建新角色
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                //1.2保存到数据表
                $auth->add($role);
                //给角色分配权限
                if($model->permissions){
                    foreach ($model->permissions as $permissionName){
                        $permission = $auth->getPermission($permissionName);
                        $auth->addChild($role,$permission);
                    }
                }
                return $this->redirect(['role-index']);
            }
        }
        return $this->render('role',['model'=>$model]);
    }

    //角色列表
    public function actionRoleIndex(){
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRoles();
        return $this->render('role-index',['roles'=>$roles]);
    }

    //修改角色列表
    public function actionEditRole($name){
        // 根据name获取role信息
        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);


        // 将上一步获取的权限信息填充到$model对象，用于页面回显
        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_EDIT;
        $model->oldName = $role->name;   //oldName用于为数据库查询的旧值，做修改时使用
        $model->name = $role->name;
        $model->description=$role->description;
        $model->permissions = RoleForm::getPermissionsByName($name);


        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //1.创建权限
                $role->name = $model->name;
                $role->description = $model->description;
                $auth->update($name, $role);


                //修改角色权限
                $auth->removeChildren($role);
                if(is_array($model->permissions)){
                    //遍历得到对象里的每一个权限名称
                    foreach ($model->permissions as $permissionName){
                        $permission=$auth->getPermission($permissionName);
                        if($permission){
                            $auth->addChild($role,$permission);
                        }
                    }

                }

                //2.保存到数据表
                $auth->update($model->oldName, $role);

                return $this->redirect(['rbac/role-index']);
            }
        }

        return $this->render('role',['model'=>$model]);
    }

    //删除角色列表
    public function actionDeleteRole(){
        $request = \Yii::$app->request;
        $name = $request->post('id');

        $auth = \Yii::$app->authManager;
        $role = $auth->getRole($name);
        $flag = $auth->remove($role);
        if($flag){
            return "success";
        }else{
            return "fail";
        }
    }

}