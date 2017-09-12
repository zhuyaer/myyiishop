<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/11
 * Time: 23:13
 */
namespace backend\controllers;

use backend\models\GoodsGallery;
use yii\web\Controller;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class GoodsGalleryController extends Controller{

    //展示goods_gallery页面
    public function actionIndex($id){
        //查询goods_gallery表的数据
        $goodsGallerys = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        $model = new GoodsGallery();
        //分配数据 调用视图
        return $this->render('index',['goods_id'=>$id, 'model'=>$model, 'goodsGallerys'=>$goodsGallerys]);
    }

    /**
     * 保存图片
     */
    public function actionSave() {
        // 获取post参数
        $id = \Yii::$app->request->post('goods_id');
        $path = \Yii::$app->request->post('path');

        $goodsGallery = new GoodsGallery();
        // 设置参数到goodsGallery model
        $goodsGallery->goods_id = $id;
        $goodsGallery->path = $path;
        //保存到数据库
        $goodsGallery->save();

        // 返回图片id，用于删除
        return $goodsGallery->id;
    }

    //删除goods表
    public function actionDelete(){
        //1.获取请求传递的id
        $id = \Yii::$app->request->post('id');
        //2.删除
        $num = GoodsGallery::deleteAll(['id'=>$id]);
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