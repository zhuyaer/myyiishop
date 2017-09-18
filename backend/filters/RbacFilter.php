<?php
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action)
    {
        //判断权限
        if(!\Yii::$app->user->can($action->uniqueId)){

            if(\Yii::$app->user->isGuest){
                // 如果用户没有登录,则引导用户跳转到登录页面
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }

            //如果没有权限,则显示提示信息页面
            throw new ForbiddenHttpException('对不起,您没有该操作权限');
        }

        //return true;//放行
        //return false;//禁止
        return parent::beforeAction($action);
    }
}