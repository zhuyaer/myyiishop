<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:31
 */
namespace backend\controllers;

use backend\models\Admin;
use yii\web\Controller;

class AdminController extends Controller{
    //展示admin表
    public function actionIndex(){
        //查询admin表的所以数据
        $admins = Admin::find()->all();
        //分配数据 调用试图
        return $this->render('index',['admins'=>$admins]);
    }

    //添加admin表
    public function actionAdd(){
        $model = new Admin();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            if($model->validate()){
                //密码加密
                $model->
            }

        }
    }
}