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
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\SmsDemo;
use function Sodium\add;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class MemberController extends Controller{

    public function init(){
        $this->enableCsrfValidation = false;
    }

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


    //商品添加到购物车(完成添加到购物车的操作)
    public function actionAddtocart($goods_id,$amount){

        if(\Yii::$app->user->isGuest){
            //未登录 购物车数据存cookie
            //写入cookie
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }

            //检查购物车中是否存在当前需要添加的商品
            if(array_key_exists($goods_id,$carts)){
                $carts[$goods_id] += $amount;
            }else{
                $carts[$goods_id] = $amount;
            }

            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;       //过期时间戳
            $cookies->add($cookie);
        }else{
            //已登录 购物车数据存数据库

            //1 检查购物车有没有该商品(根据goods_id member_id查询)
            $memberId = \Yii::$app->user->id;
            $value = Cart::findOne(['goods_id'=> $goods_id, 'member_id'=>$memberId]);

            //2. 修改数据库数量
            if($value){   // 如果购物车中存在此商品，总数=原有数量+amount
                $value->amount = $value->amount + $amount;
            }else{        // 如果购物车中不存在此商品，总数=amount
                $value = new Cart();
                $value->goods_id = $goods_id;
                $value->member_id = $memberId;
                $value->amount = $amount;
            }
            // 3.保存修改过后的数据
             $value->save();
        }

        //直接跳转到购物车
        return $this->redirect(['cart']);
    }

    //购物车页面
    public function actionCart(){
        //获取购物车数据
        if(\Yii::$app->user->isGuest){
            //从cookie取值
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }
            $models = Goods::find()->where(['in','id',array_keys($carts)])->all();
        }else{
            $memberId = \Yii::$app->user->id;
            $value = Cart::find()->where(['member_id'=>$memberId])->asArray()->all();
            if ($value) {
                $carts = ArrayHelper::map($value, 'goods_id', 'amount');
            } else {
                $carts = [];
            }
            $models = Goods::find()->where(['in','id', array_keys($carts)])->all();
        }
        return $this->renderPartial('cart',['models'=>$models,'carts'=>$carts]);
    }

    //AJAX修改购物车商品的数量
    public function actionAjax(){
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');


        if(\Yii::$app->user->isGuest){ //没登录
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }

            //检查购物车中是否存在当前需要添加的商品
            if(array_key_exists($goods_id,$carts)){
                $carts[$goods_id] = $amount;
            }

            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;//过期时间戳
            $cookies->add($cookie);
        }else{  //已经登录
            $memberId = \Yii::$app->user->id;
            $model = Cart::findOne(['goods_id'=> $goods_id, 'member_id'=>$memberId]);
            $model->amount = $amount;
            $success = $model->save();
            if ($success == true) {
                return "success";
            } else {
                return "fail";
            }
        }
    }

    //

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

    //删除cart表
    public function actionDelete(){
        $goods_id = \Yii::$app->request->post('goods_id');
        $member_id = \Yii::$app->user->id;


        if (\Yii::$app->user->isGuest) {  //未登录
            // 服务端cookie执行删除
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('carts');
            $carts = unserialize($value);
            unset($carts[$goods_id]);

            // 写回客户端
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'carts';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;       //过期时间戳
            $cookies->add($cookie);

            return true;
        } else {
            $num = Cart::deleteAll(['goods_id' => $goods_id, 'member_id'=>$member_id]);
            if ($num) {
                return true;
            } else {
                return false;
            }
        }
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


            // 用户登录后将未登录之前的购物车信息保存到数据库，并清除之前的cookie
            //配置购物车cookie操作组件（config/main.php中配置，CookieComponents为自定义控件）
//            'cartCookie'=>[
//                'class'=>\frontend\components\CookieComponents::className(),
//            ]
            \Yii::$app->cartCookie->saveDB()->clearCookie()->save();

            return $this->redirect(['index']);
        }

        return $this->renderPartial('login');
    }

    //注销功能
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['index']);
    }

    //添加收货地址
    public function actionAddress(){
        $model = new Address();
        $request = \Yii::$app->request;
        $member_id = \Yii::$app->user->getId();

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
            $model->member_id=$member_id;
//            var_dump( $model->member_id);exit;
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
        $modelArray = Address::find()->where(['member_id'=>$member_id])->all();
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