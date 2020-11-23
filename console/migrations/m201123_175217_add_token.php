<?php

use yii\db\Migration;

/**
 * Class m201123_175217_add_token
 */
class m201123_175217_add_token extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%token}}', [
            'id' => 'VARCHAR(128) NOT NULL',
            0 => 'PRIMARY KEY (`id`)',
            'access_token' => 'VARCHAR(128) NOT NULL',
            'token_type' => 'VARCHAR(128) NOT NULL',
            'expires_in' => 'INT(10) UNSIGNED NOT NULL',
            'usersName' => 'VARCHAR(128) NOT NULL',
            'issued' => 'VARCHAR(128) NOT NULL',
            'expires' => 'VARCHAR(128) NOT NULL',
        ], null);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201123_175217_add_token cannot be reverted.\n";

        return false;
    }
}
