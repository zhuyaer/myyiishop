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
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\web\Controller;
use Yii;

class OrderController extends Controller{

    //订单
    public function actionIndex(){
        //判断必须是登录状态
        if(!\Yii::$app->user->isGuest){
            $member_id = Yii::$app->user->getId();

            // 如果购物车为空，提示并退出
            $count = Cart::find(['member_id' => $member_id])->count();
            if ($count == 0) {
                echo "购物车为空,请先到<a href='/index.php/member/index.html'>首页</a>购买商品";
                exit();
            }

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

    // 提交订单
    public function actionSave() {


        if(!\Yii::$app->user->isGuest) {

            // 获取登录用户id
            $member_id = Yii::$app->user->getId();

            // 如果购物车为空，提示并退出
           $count = Cart::find(['member_id' => $member_id])->count();
           if ($count == 0) {
               echo "购物车为空,请先到<a href='/index.php/member/index.html'>首页</a>购买商品";
               exit();
           }

           // 开始从表单获取值
            $order = new Order();

            //赋值订单号 和 下单时间 （默认订单号 == 下单时间）
            $order->trade_no = $order->create_time = time();
            $order->member_id = $member_id;

            /************* 收货地址获取*************/
            //地址id
            $address_id = Yii::$app->request->post('address_id');
            $address = Address::findOne(['id' => $address_id, 'member_id' => $member_id]);
            $order->name = $address->username;
            $order->tel = $address->tel;
            $order->province = $address->province;
            $order->city = $address->city;
            $order->area = $address->area;
            $order->address = $address->address;

            /************* 配送方式赋值 *************/
            $order->delivery_id = Yii::$app->request->post('delivery_id');
            $order->delivery_name = Order::$deliveries[$order->delivery_id][0];
            $order->delivery_price = Order::$deliveries[$order->delivery_id][1];


            /************* 支付方式赋值*************/
            $order->payment_id = Yii::$app->request->post('payment_id');
            $order->payment_name = Order::$payment[$order->payment_id][0];

            /************* 总额计算 *************/
            //遍历购物车表里面的商品,累加计算,加上运费
            $order->total = 0;
            $transaction = Yii::$app->db->beginTransaction();//开始事务
            try {
                $order->save();

                //订单商品详情表
                $carts = Cart::find()->where(['member_id' => $member_id])->all();
                foreach ($carts as $cart) {
                    //检查库存
                    if ($cart->amount > $cart->goods->stock) {
                        //库存不足,不能下单(抛出异常)
                        throw new Exception($cart->goods->name . '商品库存不足,不能下单');
                    }
                    $order_goods = new OrderGoods();
                    $order_goods->order_id = $order->id;
                    $order_goods->goods_id = $cart->goods_id;
                    $order_goods->goods_name = $cart->goods->name;
                    $order_goods->logo = $cart->goods->logo;
                    $order_goods->price = $cart->goods->shop_price;
                    $order_goods->amount = $cart->amount;
                    $order_goods->total = $order_goods->price * $order_goods->amount;
                    $order_goods->save();
                    $order->total += $order_goods->total;
                }

                //加运费
                $order->total += $order->delivery_price; //邮费

                // 订单状态（0已取消 1待付款 2待发货 3待收货 4完成）
                $order->status = 2;

                $order->save();

                /////////////////////// 删除购物车数据  ///////////////////////
                Cart::deleteAll(['member_id' => $member_id]);

                //提交事务
                $transaction->commit();
                //跳转到下单成功提示页
            } catch (Exception $e) {
                //不能下单,需要回滚
                $transaction->rollBack();
            }

            return $this->redirect(['/order/payok']);
        }
    }

    // 支付完成
    public function actionPayok() {
        return $this->renderPartial('payok');
    }

    // 查看我的订单
    public function actionMyorder() {
        // 获取登录用户id
        $member_id = Yii::$app->user->getId();
        $orders = Order::find()->where(['member_id'=>$member_id])->all();
        return $this->renderPartial('myorder', ['orders'=>$orders]);
    }

    //删除订单
    public function actionDelete(){
        $id = Yii::$app->request->post('id');
        return Order::deleteAll(['id'=>$id]);
    }
}