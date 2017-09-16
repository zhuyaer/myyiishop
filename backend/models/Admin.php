<?php
/**
 * Created by PhpStorm.
 * User: zhu-y
 * Date: 2017/9/13
 * Time: 11:32
 */
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
class Admin extends ActiveRecord implements IdentityInterface{
    //常量定义场景
    const SCENARIO_ADD = 'add';

    // 用户角色数组
    public $roles = [];

    //定义规则
    public function rules()
    {
        return [
            [['username','email','status'],'required'],
            [['password_hash','password_reset_token'],'string'],
            //on指定场景  该规则只在该场景下生效
            ['password_hash','required','on'=>[self::SCENARIO_ADD]],

            // 非常重要
            // 使用safe可以声明该attribute是安全的，任意值都可以通过验证
            [['roles'], 'safe'],
        ];
    }


    /**
     * 添加管理员后，给该管理员添加角色
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        //给用户关联权限
        if($this->roles){
            $authManager = \Yii::$app->authManager;
            $authManager->revokeAll($this->id); //先清空该用户的角色

            // 该选择的角色赋给该用户
            foreach ($this->roles as $roleName){
                $role = $authManager->getRole($roleName);
                if($role) $authManager->assign($role,$this->id);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    //定义标签字段名
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'email'=>'邮箱',
            'status'=>'状态'
        ];
    }

    /**
     * 通过username查询用户
     *
     * @param $username
     * @return static
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => 1]);
    }

    // IdentityInterface 接口实现
  /*  public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }*/
    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */

    //  IdentityInterface 接口实现
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}