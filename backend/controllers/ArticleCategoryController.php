<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/7
 * Time: 18:00
 */
namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use yii\web\Controller;

class ArticleCategoryController extends Controller{

    //展示功能
    public function actionIndex(){
        //显示Article_category表的所有数据
        $ArticleCategorys = ArticleCategory::find()->all();
        //分配数据  调用视图
        return $this->render('index', ['ArticleCategorys'=>$ArticleCategorys]);
    }

    //添加功能
    public function actionAdd(){
        $model = new ArticleCategory();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            if($model->validate()){
                //保存数据
                $model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                //保存成功跳转页面
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        $ArticleCategorys = ArticleCategory::find()->all();
        return $this->render('add',['model'=>$model,'ArticleCategorys'=>$ArticleCategorys]);
    }

    //修改功能
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            // 校验数据
            if ($model->validate()) {
                //保存数据
                $model->save(false);
                return $this->redirect(['article-category/index']);
            } else {
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('add',['model'=>$model]);
    }


    //删除功能(逻辑删除)
    public function actionDelete($id) {
        $num = ArticleCategory::updateAll(['status'=>-1], ['id'=>$id]);
        return $num; // 向调用接口（ajax）返回值（false代表错误，整数代表成功）
    }

}