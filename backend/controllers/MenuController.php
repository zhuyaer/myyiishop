<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/17
 * Time: 13:07
 */
namespace backend\controllers;

use backend\models\Menu;
use yii\web\Controller;

class MenuController extends Controller{

    //展示menu表的数据
    public function actionIndex(){
        //查询menu表的所有数据
        $menus = Menu::find()->all();
        //调用视图  分配数据
        return $this->render('index',['menus'=>$menus]);
    }

    //使用tree方式展示menu表的数据
    public function actionTree(){
        //查询menu表的所有数据(用tree的方法查询)
        $menus = Menu::getMenus();
        //调用视图  分配数据
        return $this->render('tree',['menus'=>$menus]);
    }

    //添加menu表的数据
    public function actionAdd(){
        $model = new Menu();
        $menu = Menu::find()->where(['=','parent_id',0])->all();
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','菜单添加成功');
                return $this->redirect('index');
            }
        }
        return $this->render('add',['model'=>$model,'menu'=>$menu,'permissions'=>$permissions]);
    }

    //修改menu表的数据
    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        $menu = Menu::find()->where(['=','parent_id',0])->all();
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','菜单修改成功');
                return $this->redirect(['menu/index']);
            }
        }
        return $this->render('add',['model'=>$model,'menu'=>$menu,'permissions'=>$permissions]);
    }

    //删除menu表的数据
    public function actionDelete(){
        //1.获取请求传递的id
        $id = \Yii::$app->request->post('id');
        //2.删除
        $result = Menu::deleteAll(['id'=>$id]);
        if($result){
            return 'success';
        }else{
            return 'fail';
        }
    }

}