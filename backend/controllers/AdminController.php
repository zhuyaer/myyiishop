<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:31
 */
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Admin;
use backend\models\Article;
use backend\models\LoginForm;
use backend\models\PasswordForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;
use yii\filters\AccessControl;

class AdminController extends Controller{

    //展示admin表
    public function actionIndex(){
        //$identity = \Yii::$app->user->identity;
       // var_dump($identity);exit;
        //查询admin表的所以数据
        $admins = Admin::find()->all();
        //分配数据 调用试图
        return $this->render('index',['admins'=>$admins]);
    }

    //添加admin表
    public function actionAdd(){
        $model = new Admin(['scenario'=>Admin::SCENARIO_ADD]);

        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            if($model->validate()){
                //密码加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //保存数据
                $model->save();
                return $this->redirect(['admin/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //修改admin表
    public function actionEdit($id){
        $model = Admin::findOne(['id'=>$id]);
        $password_hash = $model->password_hash;
        $request = \Yii::$app->request;
        if($request->isPost){
            //模型加载数据
            $model->load($request->post());
            if($model->validate()){
                //保存数据
                if($model->password_hash==null){
                    $model->password_hash = $password_hash;
                }else{
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                }
                $model->save(false);
                return $this->redirect(['admin/index']);

            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        $model->password_hash='';
        //回显用户角色
        $model->roles = RoleForm::getRoleItemsById($id);
        return $this->render('add',['model'=>$model]);
    }

    //删除admin表的数据
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = Admin::findOne(['id' => $id]);
        if($model){
            $model->delete();
            return 'success';
        }
        return 'fail';
    }

    //登录功能
    public function actionLogin(){
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {

            //------------------------------------------------------------
            // 管理员登陆后需要保存最后登录时间和最后登陆ip
            $user = \Yii::$app->user->identity;         //获取用户，用于更新登录用户表
            $userId = $user->getId();                   //获取用户id
            $admin = Admin::findOne(['id'=>$userId]);   //数据查找登录用户
            $admin->last_login_time = time();          //设置最后登录时间
            $admin->last_login_ip =
                \Yii::$app->request->userIP; //设置最后登录IP
            $admin->save(false);
            // ---------------------------------------------------------

            return $this->redirect(['/admin/index']);
        } else {
            return $this->render('login', ['model'=>$model, 'error'=>'用户名密码错误或者账号被冻结']);
        }

        return $this->render('login', ['model'=>$model, 'error'=>'']);
    }

    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['admin/login']);
    }

    //修改自己密码
    public function actionPassword(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }
        $model = new PasswordForm();
        $request = \Yii::$app->request;

        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $admin = \Yii::$app->user->identity;
                //密码加密
                $admin->password_hash = \Yii::$app->security->generatePasswordHash($model->newPassword);
                //保存数据
                $admin->save();

                return $this->redirect(['admin/logout']);
            }
        }
        return $this->render('password',['model'=>$model]);
    }


    // acf校验
    function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['logout','login', 'add', 'index', 'edit', 'delete', 'password']
            ],
            'acf'=>[
                'class'=>AccessControl::className(),
                //只对以下操作生效
                'only'=>['index', 'edit'],
                'rules'=>[
                    [
                        'allow'=>true,
                        'actions'=>['index'],
                        'roles'=>['@']
                    ],
                    [
                        'allow'=>true,
                        'actions'=>['edit'],
                        'roles'=>['@']
                    ]
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

}