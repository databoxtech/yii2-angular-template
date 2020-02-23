<?php

use app\models\User;
use yii\db\Migration;

/**
 * Class m191021_165358_user
 */
class m191021_165358_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        return $this->createTable('users', [
            'id' => $this->primaryKey(11),
            'password' => $this->char(128),
            'displayName' => $this->char(128),
            'email' => $this->char(128)->notNull()->unique(),
            'phone' => $this->char(15),
            'deleted' => $this->boolean()->defaultValue(false),
            'blocked' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'updated_at' => $this->dateTime()->defaultExpression('NOW()')->append('ON UPDATE NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return $this->dropTable('users');;
    }
}
