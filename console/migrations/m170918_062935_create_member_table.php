<?php

use yii\db\Migration;

/**
 * Handles the creation of table `member`.
 */
class m170918_062935_create_member_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('member', [
            'id' => $this->primaryKey(),
            'username' => $this->string(50)->notNull()->comment('用户名'),
            'auth_key' => $this->string(32)->notNull()->comment('标识'),
            'password_hash' => $this->string(100)->notNull()->comment('密码(密文)'),
            'email' => $this->string(100)->notNull()->comment('邮箱'),
            'tel'=>$this->string(11)->comment('电话'),
            'last_login_time'=>$this->integer()->comment('最后登录时间'),
            'last_login_ip'=>$this->integer()->comment('最后登陆ip'),
            'status'=>$this->smallInteger(1)->comment('登录状态，0删除，1正常'),
            'created_at'=>$this->integer()->comment('添加时间'),
            'updated_at'=>$this->integer()->comment('修改时间')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('member');
    }
}
