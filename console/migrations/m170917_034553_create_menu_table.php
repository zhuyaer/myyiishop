<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m170917_034553_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string()->notNull()->comment('名称'),
            'parent_id'=>$this->string()->comment('上级菜单的ID'),
            'route'=>$this->string()->comment('地址/路由'),
            'sort'=>$this->string()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
