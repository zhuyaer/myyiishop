<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170919_081542_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(20)->comment('收货人'),
            'tel'=>$this->integer()->comment('电话'),
            'province'=>$this->string()->comment('省'),
            'city'=>$this->string()->comment('市'),
            'area'=>$this->string()->comment('区'),
            'address'=>$this->string()->comment('详细地址'),
            'default_address'=>$this->string()->comment('默认地址')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
