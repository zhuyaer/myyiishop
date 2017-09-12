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
use yii\data\Pagination;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;
class BrandController extends Controller{

    //展示功能
    public function actionIndex(){
        $query = Brand::find();
        //当前页码（get参数）
        //实例化分页工具类
        $pager = new Pagination([
            //总条数
            'totalCount'=>$query->count(),
            //每页多少条
            'defaultPageSize'=>3
        ]);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['brands'=>$models,'pager'=>$pager]);
    }

    //添加功能表
    public function actionAdd(){
        $model = new Brand();
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            /*// 上传文件开始
            //获取文件（file在Brank Model中定义）
            $model->file = UploadedFile::getInstance($model,'file');*/
            if($model->validate()){
                /*//设置文件路径
                $path = '/upload/'.uniqid().'.'.$model->file->getExtension();//文件名(包含路径)
                //保存文件(文件另存为)
                $model->file->saveAs(\Yii::getAlias('@webroot').$path,false);
                //上传文件的地址赋值给作者的head字段
                $model->logo = $path;
                // 上传文件结束*/
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
            /*// 上传文件开始
            //获取文件（file在Brank Model中定义）
            $model->file = UploadedFile::getInstance($model,'file');*/
            if($model->validate()){
                /*//设置文件路径
                $path = '/upload/'.uniqid().'.'.$model->file->getExtension();//文件名(包含路径)
                //保存文件(文件另存为)
                $model->file->saveAs(\Yii::getAlias('@webroot').$path,false);
                //上传文件的地址赋值给作者的head字段
                $model->logo = $path;
                // 上传文件结束*/
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

    //测试七牛云  对象存储
    public function actionQiniu(){
        $config = [
            'accessKey'=>'1KBtWljwIfKZnN65GvGX-lH5q6SNgT2ss0BZTaap',
            'secretKey'=>'zbtXvFnprGvk4m6A_l8ID3FstV-P3v2LOBPNhYh4',
            'domain'=>'http://ovybghts3.bkt.clouddn.com/',
            'bucket'=>'0516php',
            'area'=>Qiniu::AREA_HUADONG     //华东
        ];



        $qiniu = new Qiniu($config);
        $key = '1.jpg';
        //上传文件到七牛云  同时指定一个key(名称，文件名)
        $file = \Yii::getAlias('@webroot/upload/1.jpg');
        $qiniu->uploadFile($file,$key);
        //获取七牛云上文件的url地址
        $url = $qiniu->getLink($key);
        var_dump($url);
    }
}