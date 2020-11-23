<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%crash}}`.
 */
class m200521_043811_create_crash_table extends Migration
{
    const CRASH = '{{%crash}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::CRASH, [
            '_id' => $this->primaryKey(),
            'report_id' => $this->string(),
            'app_version_code' => $this->string(),
            'app_version_name' => $this->string(),
            'phone_model' => $this->string(),
            'brand' => $this->string(),
            'product' => $this->string(),
            'android_version' => $this->string(),
            'stack_trace' => $this->text(),
            'user_app_start_date' => $this->string(),
            'user_crash_date' => $this->string(),
            'logcat' => $this->text(),
            'userId' => $this->string(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%crash}}');
    }
}
