<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 11:12
 */
namespace backend\controllers;

use backend\models\GoodsGallery;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use backend\models\GoodsSearch;
use yii\data\Pagination;
use yii\web\Controller;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;
use yii\web\NotFoundHttpException;
use yii\web\Request;
class GoodsController extends Controller{

    //显示goods表
    public function actionIndex(){
      /*  $searchModel = new Goods();
        $query = Goods::find();
        //当前页码（get参数）
        //实例化分页工具类
        $pager = new Pagination([
            //总条数
            'totalCount'=>$query->count(),
            //每页多少条
            'defaultPageSize'=>3
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['goods'=>$models,'pager'=>$pager,'searchModel'=> $searchModel]);*/
        $Goods = Goods::find();
        //表单模型
        $Form = new GoodsSearch();
        //接收表单提交的查询参数
        $Form->search($Goods);
        $pager = new Pagination([
            'totalCount'=>$Goods->count(),
            'defaultPageSize'=>3
        ]);

        $models = $Goods->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager,'search'=>$Form]);
    }

    //添加goods表
    public function actionAdd(){
        $goods = new Goods();
        $goods_intro = new GoodsIntro();
        $brand = Brand::find()->all();
        $request  = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $goods->load($request->post());
            $goods_intro->load($request->post());
            if ($goods->validate() && $goods_intro->validate()){
                $goods_day_count = GoodsDayCount::findOne(['day'=>date("Y-m-d",time())]);
                if ($goods_day_count){
                    $goods_day_count->count += 1;//给数量加一.
                    $goods->sn = date("Ymd",time()).sprintf('%05d',$goods_day_count->count);
                }else{
                    $goods_day_count = new GoodsDayCount();
                    $goods_day_count->day = date("Y-m-d",time());
                    $goods_day_count->count = 1;
                    $goods->sn = date("Ymd",time()).'00001';
                }
                $goods_day_count->save();
                $goods->create_time = time();
                $goods->view_times = 0;
                $goods->save();
                //var_dump($goods->getErrors());exit;
                $goods_intro->goods_id = $goods->id;
                $goods_intro->save();
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['goods/index']);
            } else {
                //验证失败,获取错误信息.
                var_dump($goods->getErrors(),$goods_intro->getErrors());
                exit;
            }
        }
        $goods->status = 1;
        $goods->is_on_sale = 0;
        return $this->render('add', ['goods'=>$goods,'goods_intro'=>$goods_intro,'brand'=>$brand]);
    }

    //修改goods表
    public function actionEdit($id){
        $goods = Goods::findOne(['id'=>$id]);
        $goods_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        $brand = Brand::find()->all();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $goods->load($request->post());
            if($goods->validate()){
                //保存数据
                $goods->save();
                return $this->redirect(['goods/index']);
            }else{
                var_dump($goods->getErrors());
                exit;
            }
        }
        return $this->render('add',['goods'=>$goods,'brand'=>$brand,'goods_intro'=>$goods_intro]);
    }

    //删除goods表
    public function actionDelete(){
        //1.获取请求传递的id
        $id = \Yii::$app->request->post('id');
        //2.删除
        $num = Goods::deleteAll(['id'=>$id]);
        if($num){
            return true;
        }else{
            return false;
        }
    }

    //uploadifive
    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();
                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"

                    //将图片上传到七牛云，并且返回七牛云的图片地址
                    /* $config = [
                         'accessKey'=>'1KBtWljwIfKZnN65GvGX-lH5q6SNgT2ss0BZTaap',
                         'secretKey'=>'zbtXvFnprGvk4m6A_l8ID3FstV-P3v2LOBPNhYh4',
                         'domain'=>'http://ovybghts3.bkt.clouddn.com/',
                         'bucket'=>'0516php',
                         'area'=>Qiniu::AREA_HUADONG     //华东
                     ];*/

                    $qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    //上传文件到七牛云  同时指定一个key(名称，文件名)
                    $file =  $action->getSavePath();
                    $qiniu->uploadFile($file,$key);
                    //获取七牛云上文件的url地址
                    $url = $qiniu->getLink($key);
                    //输出图片的路径
                    $action->output['fileUrl'] = $url;
                },
            ],
        ];
    }




}