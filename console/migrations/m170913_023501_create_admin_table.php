<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170913_023501_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->comment('用户名'),
            'auth_key' => $this->string()->notNull()->comment('标识'),
            'password_hash' => $this->string()->notNull()->comment('密码hash'),
            'password_reset_token' => $this->string()->notNull()->comment('重置密码'),
            'email' => $this->string()->notNull()->comment('邮箱'),
            'status'=>$this->smallInteger()->comment('登录状态，0禁用，1启用'),
            'created_at'=>$this->integer()->comment('创建时间'),
            'updated_at'=>$this->integer()->comment('更新时间'),
            'last_login_time'=>$this->integer()->comment('最后登录时间'),
            'last_login_ip'=>$this->integer()->comment('最后登陆ip')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
