<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 15:38
 */
namespace backend\controllers;

use backend\models\Brand;
use yii\web\Controller;
use yii\web\UploadedFile;

class BrandController extends Controller{

    //展示功能
    public function actionIndex(){
        //显示brand表的所有数据
        $brands = Brand::find()->all();
        //分配数据  调用视图
        return $this->render('index',['brands'=>$brands]);
    }

    //添加功能表
    public function actionAdd(){
        $model = new Brand();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            // 上传文件开始
            //获取文件（file在Brank Model中定义）
            $model->file = UploadedFile::getInstance($model,'file');
            if($model->validate()){
                //设置文件路径
                $path = '/upload/'.uniqid().'.'.$model->file->getExtension();//文件名(包含路径)
                //保存文件(文件另存为)
                $model->file->saveAs(\Yii::getAlias('@webroot').$path,false);
                //上传文件的地址赋值给作者的head字段
                $model->logo = $path;
                // 上传文件结束
                //保存数据
                $model->save(false);
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }


    //修改功能表
    public function actionEdit($id){
        $model = Brand::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            // 上传文件开始
            //获取文件（file在Brank Model中定义）
            $model->file = UploadedFile::getInstance($model,'file');
            if($model->validate()){
                //设置文件路径
                $path = '/upload/'.uniqid().'.'.$model->file->getExtension();//文件名(包含路径)
                //保存文件(文件另存为)
                $model->file->saveAs(\Yii::getAlias('@webroot').$path,false);
                //上传文件的地址赋值给作者的head字段
                $model->logo = $path;
                // 上传文件结束
                //保存数据
                $model->save(false);
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }


    //删除功能表（逻辑删除）
    public function actionDelete($id) {
       $num = Brand::updateAll(['status'=>-1], ['id'=>$id]);
       return $num; // 向调用接口（ajax）返回值（false代表错误，整数代表成功）
    }
}