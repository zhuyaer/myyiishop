<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/9
 * Time: 11:07
 */
namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;
use yii\web\Controller;

class ArticleDetailController extends Controller{

    //展示article_detail表的所有数据
    public function actionIndex(){
        //1.查询article_detail表的所有数据
        $ArticleDetails = ArticleDetail::find()->all();
        //2.分配数据  调用视图
        return $this->render('index',['ArticleDetails'=>$ArticleDetails]);
    }

    //添加数据到article_detail表
    public function actionAdd(){
        $model = new ArticleDetail();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            //校验数据
             if($model->validate()){
                //保存数据
                 $model->save();
                 return $this->redirect(['article-detail/index']);
             }else{
                 var_dump($model->getErrors());
                 exit;
             }
        }
        $articles = Article::find()->all();
        return $this->render('add',['model'=>$model, 'articles'=>$articles]);
    }

    //更新数据到article_detail表
    public function actionEdit($id){
        $model = ArticleDetail::findOne([['article_id'=>$id]]);
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            //校验数据
            if($model->validate()){
                //保存数据
                $model->save();
                return $this->redirect(['article-detail/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        $articles = Article::find()->all();
        return $this->render('add',['model'=>$model, 'articles'=>$articles]);
    }

    //删除article_detail表的数据
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = ArticleDetail::findOne(['article_id'=>$id]);
        if($model){
            $model->delete();
            return 'success';
        }
        return 'fail';
    }
}