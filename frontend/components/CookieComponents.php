<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/22
 * Time: 0:48
 */
namespace frontend\components;

/*
*操作cookie里面的购物车数据
*/
use frontend\models\Cart;
use yii\base\ErrorException;
use yii\web\Cookie;
class CookieComponents extends \yii\base\Component
{
    private $_cart=[];
    public function __construct(array $config=[])
    {
        $cookies = \Yii::$app->request->cookies;
        $cookie = $cookies->get('carts');
        if($cookie == null){//购物车cookie不存在
            $cart = [];
        }else{//购物车cookie存在
            $cart = unserialize($cookie->value);
        }
        $this->_cart = $cart;
        parent::__construct($config);
    }

    public function save(){
        //将购车数据保存回cookie
        $cookies = \Yii::$app->response->cookies;
        $cookie = new Cookie([
            'name'=>'carts',
            'value'=>serialize($this->_cart)
        ]);
        $cookies->add($cookie);
    }


    //清空cookie购物车
    public function clearCookie()
    {
        $this->_cart = [];
        return $this;
    }


    //同步到购物车数据库
    public function saveDB()
    {
        if(\Yii::$app->user->isGuest){
            throw new ErrorException('必须登录后才能保存到数据库');
        }
        $member_id = \Yii::$app->user->id;
        foreach($this->_cart as $goods_id=>$num){
            $cart = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
            if($cart == null){
                $cart = new Cart();
                $cart->member_id = $member_id;
                $cart->goods_id = $goods_id;
            }
            $cart->amount = $num;
            $cart->save();
        }
        return $this;
    }
}