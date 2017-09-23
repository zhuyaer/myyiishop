<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/23
 * Time: 20:48
 */
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use yii\web\Controller;
use Yii;

class OrderController extends Controller{
    //订单
    public function actionIndex(){

        //判断必须是登录状态
        if(!\Yii::$app->user->isGuest){
            $member_id = Yii::$app->user->getId();
            //获取收货人信息
            $modelArray = Address::find()->where(['member_id'=>$member_id])->all();
            //获取送货方式数据
            $deliveries = Order::$deliveries;
            //获取支付方式数据
            $payment = Order::$payment;
            //展示购物车数据
            $carts = Cart::find()->where(['member_id'=>$member_id])->all();

            return $this->renderPartial('index',['deliveries'=>$deliveries,'payment'=>$payment,'modelArray'=>$modelArray,'carts'=>$carts]);
        }

    }
}