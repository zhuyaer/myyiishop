<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/10
 * Time: 11:42
 */
namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\Controller;

class GoodsCategoryController extends Controller{


    //展示GoodsCategory表的数据
   public function actionIndex(){
       $query = GoodsCategory::find();
       //当前页码（get参数）
       //实例化分页工具类
       $pager = new Pagination([
           //总条数
           'totalCount'=>$query->count(),
           //每页多少条
           'defaultPageSize'=>6
       ]);
       $models = $query->limit($pager->limit)->offset($pager->offset)->all();
       return $this->render('index',['GoodsCategorys'=>$models,'pager'=>$pager]);
   }

   //添加GoodsCategory表的数据
    public function actionAdd(){
       $model = new GoodsCategory();
       $request = \Yii::$app->request;
       if($request->isPost){
           //模型加载数据
           $model->load($request->post());
           //验证规则
           if($model->validate()){
               //判断添加顶级分类还是非顶级分类（子分类）
               if($model->parent_id){
                    //非顶级分类（子分类）
                   $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                   $model->prependTo($parent);
               }else{
                   //顶级分类
                   $model->makeRoot();
               }
                //保存数据
               //$model->save();
               \Yii::$app->session->setFlash('success','添加成功');
               //保存成功跳转页面
               return $this->redirect(['goods-category/index']);
           }else{
               var_dump($model->getErrors());
               exit;
           }
       }
       return $this->render('add',['model'=>$model]);
    }


    //修改GoodsCategory表的数据
    public function actionEdit($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            //验证规则
            if($model->validate()){
                //判断添加顶级分类还是非顶级分类（子分类）
                if($model->parent_id){
                    //非顶级分类（子分类）
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->prependTo($parent);
                }else{
                    //顶级分类
                    $model->makeRoot();
                }
                //保存数据
                //$model->save();
                \Yii::$app->session->setFlash('success','添加成功');
                //保存成功跳转页面
                return $this->redirect(['goods-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //创建根节点
    public function actionTest(){
        //创建1级分类
        /*$model = new GoodsCategory(['name' => '家用电器']);
        $model->parent_id = 0;
        $model->makeRoot();
        var_dump($model->getErrors());*/
        //创建子分类
        $parent = GoodsCategory::findOne(['id'=>1]);
        $child = new GoodsCategory(['name'=>'大家电']);
        $child->parent_id = 1;
        $child->prependTo($parent);

        echo "操作成功！";
    }

    //测试 ztree
    public function actionZtree(){
        //不加载布局文件
        //$this->layout = false;
        $goodsCategories = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        return $this->renderPartial('ztree',['goodsCategories'=>$goodsCategories]);
    }

    //删除GoodsCategory表的数据
    public function actionDelete(){
        //1.获取删除数据的id
        $id = \Yii::$app->request->post('id');

        //2.统计父节点为id的数据
        $count = GoodsCategory::find()->where(['parent_id'=>$id])->count();
        if ($count > 0) {
           return 'exit';
        }

        //3.调用model删除
        $model = GoodsCategory::deleteAll(['id'=>$id]);

        if ($model) {
            return 'ok';
        } else {
            return 'fail';
        }

    }
}