<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 19:29
 */
namespace backend\controllers;

use backend\models\Article;
use yii\web\Controller;

class ArticleController extends Controller{
    //展示功能
    public function actionIndex(){
        //展示 article表的所有数据
        $articles = Article::find()->all();
        //分配数据 调用视图
        return $this->render('index',['articles'=>$articles]);
    }

    //添加功能
    public function actionAdd(){
        $model = new Article();
        $request = \Yii::$app->request;
        if($request->isPost) {
            //模型加载数据
            $model->load($request->post());
            if($model->validate()){
                $model->create_time = time();
                $model->status = 1;
                $model->save();
                return $this->redirect(['article/index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add', ['model'=>$model]);
    }



}