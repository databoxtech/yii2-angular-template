<?php

use yii\db\Migration;

/**
 * Class m200316_065802_refresh_token
 */
class m200316_065802_refresh_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('refresh_tokens', [
            'id' => $this->primaryKey(11),
            'user' => $this->integer(11),
            'token' => $this->char(192),
            'created_at' => $this->dateTime()->defaultExpression('NOW()'),
            'expires_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('ifx-refresh_token-user', 'refresh_tokens', 'user', 'users', 'id');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ifx-refresh_token-user', 'refresh_tokens');
        return $this->dropTable('refresh_tokens');;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200316_065802_refresh_token cannot be reverted.\n";

        return false;
    }
    */
}
