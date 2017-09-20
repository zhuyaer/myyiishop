<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/18
 * Time: 14:16
 */
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\SmsDemo;
use yii\filters\AccessControl;
use yii\web\Controller;

class MemberController extends Controller{

    /**
     * 首页
     *
     * @return string
     *
     */
    public function actionIndex(){
        $categories = GoodsCategory::find()->all();
        $result = $this->getTree($categories, 0);
        return $this->renderPartial('index', ['menus'=>$result]);
    }

    /**
     * 商品列表
     * @return string
     */
    public function actionList($id) {
        // 查询出当前分类和子级分类
        $cates = GoodsCategory::find()->where(['id'=>$id])->orWhere(['parent_id'=>$id])->all();
        // 取出当前分类和子级分类的id并放入$ids数组
        $ids = [];
        foreach ($cates as $cate) {
            $ids[] = $cate['id'];
        }
        // 使用mysql in 查询
        // 例如：我们要查询id在1、2、3的范围内
       // $userInfo = User::find()->where(['in' , 'id' , [1,2,3]])->all();
        $goods = Goods::find()->where(['in', 'goods_category_id', $ids])->all();
        return $this->renderPartial('list', ['goods'=>$goods]);
    }

    /**
     * 商品详情
     * @return string
     */
    public function actionGoods($id) {
        $goods = Goods::findOne(['id'=>$id]);
        $goodsIntro = GoodsIntro::findOne(['goods_id'=>$id]);
        $goodsPhotos = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        //没有相册，则把logo添加到相册
        if ($goodsPhotos == null) {
            $g = new GoodsGallery();
            $g->path = $goods->logo;
            $goodsPhotos[] = $g;
        }

        return $this->renderPartial('goods', ['goods'=>$goods, 'goodsIntro'=>$goodsIntro, 'goodsPhotos'=>$goodsPhotos]);
    }



    //注册
    public function actionRegister(){

        $model = new Member();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(), '');
            if($model->validate()){
                $model->save();
                return $this->redirect(['member/login']);
            }
        }

        //显示视图，不加载局部文件
        return $this->renderPartial('register');
    }

    //ajax验证用户唯一性
    public function actionValidateUser($username){
        return 'true';
    }

    //登录功能
    public function actionLogin(){
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post(),'') && $model->login()){
            //var_dump($model);exit;
            //获取用户，用于更新登录用户表
            $user = \Yii::$app->user->identity;
            //获取用户id
            $userId = $user->getId();
            $member = Member::findOne(['id'=>$userId]);
            $member->last_login_time = time();
            $member->last_login_ip =
                \Yii::$app->request->userIP;
            $member->save(false);

            return $this->redirect(['index']);
        }

        return $this->renderPartial('login');
    }

    //添加收货地址
    public function actionAddress(){
        $model = new Address();

        $request = \Yii::$app->request;

        $id = $request->get('id');
        if ($id != null) {
            $model = Address::find()->where(['id'=>$id])->one();
        }

        if($request->isPost){
            // model保存post提交的数据
            $defaultAddress = $request->post('default_address');
            if ($defaultAddress == 1) {
                $model->default_address = 1;
                // 将所有的默认地址重置为非默认
                Address::updateAll(['default_address'=>0], ['default_address'=>1]);
            } else {
                $model->default_address = 0;
            }

            //模型加载数据
            $model->load($request->post(),'');
            if($model->validate()){
                //如果是修改执行此逻辑
                $postId = $request->post('postid');
               if ($postId) {
                   $model->id = $postId;
                   Address::updateAll($model, ['id'=>$postId]);
                   // 获取所有地址列表
                   $model = new Address();
                   $modelArray = Address::find()->all();
                   return $this->renderPartial('address',['model'=>$model, 'modelArray'=>$modelArray]);
               } else {      // 添加执行此逻辑
                   $model->save();
                   $model = new Address();
                   $modelArray = Address::find()->all();
                   return $this->renderPartial('address',['model'=>$model, 'modelArray'=>$modelArray]);
               }
            }else{
                var_dump($model->getErrors());
                exit();
            }
        }

        // 获取所有地址列表
        $modelArray = Address::find()->all();
        return $this->renderPartial('address',['model'=>$model, 'modelArray'=>$modelArray]);
    }

    // 删除收货地址
    public function actionAddressDel() {
        $id = \Yii::$app->request->get('id');
        return Address::deleteAll(['id'=>$id]);
    }

    //测试发送短信
    public function actionSms(){
        header('Content-Type: text/plain; charset=utf-8');
        $phone = \Yii::$app->request->post('phone');
        $code = rand(1000,9999);

        // 参考文章 http://www.yiichina.com/tutorial/550
        // 安装redis扩展: composer require --prefer-dist yiisoft/yii2-redis
        // redis
        $redis = \Yii::$app->redis;
        $redis->set('code_'.$phone, $code);
        $redis->expire('code_'.$phone, 5 * 60); //过期


        //\Yii::$app->session->set('code_'.$phone, $code);
        $demo = new SmsDemo(
            "LTAI7PUWvkjjq1bw",                         //AK    LTAI7PUWvkjjq1bw
            "OoebyJgBQQmoCtnB8M8AwaloX1Tk8t"       //SK    OoebyJgBQQmoCtnB8M8AwaloX1Tk8t
        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "程斌", // 短信签名
            "SMS_97990014", // 短信模板编号
            $phone, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$code,
            )
        );
       if($response->Message == 'OK'){
            echo '发送成功';
        }else {
           echo '发送失败';
       }
        echo $code;
    }

    //前台验证码的验证
    public function actionValidateSms($phone,$sms){
        //$code = \Yii::$app->session->get('code_'.$phone);
        $code = \Yii::$app->redis->get('code_'.$phone);
        if($code==null || $code != $sms){
            return 'false';
        }
        return 'true';
    }

    public function behaviors()
    {
        return [
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


    /**
     * 将菜单生成层级树
     * @param $data
     * @param $pid
     * @return array
     */
    private function getTree($data, $pid) {
        $tree = [];
        foreach ($data as $k =>$v) {
            if ($v['parent_id'] == $pid){
                $v['parent_id'] = $this->getTree($data, $v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}