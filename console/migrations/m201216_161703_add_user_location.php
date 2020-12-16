<?php

use yii\db\Migration;

/**
 * Class m201216_161703_add_user_location
 */
class m201216_161703_add_user_location extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'location', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201216_161703_add_user_location cannot be reverted.\n";

        return false;
    }
}
