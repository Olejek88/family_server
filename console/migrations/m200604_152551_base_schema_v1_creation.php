<?php

use common\models\ObjectSubType;
use yii\db\Migration;

/**
 * Class m200604_152551_base_schema_v1_creation
 */
class m200604_152551_base_schema_v1_creation extends Migration
{
    const FAMILY = '{{%family}}';
    const FAMILY_USER = '{{%family_user}}';
    const ROUTES = '{{%routes}}';
    const USER = '{{%user}}';
    const SETTINGS = '{{%settings}}';
    const REGISTER = '{{%register}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::FAMILY, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'deleted' => $this->boolean()
        ], $tableOptions);

        $this->createTable(self::FAMILY_USER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'familyUuid' => $this->string(36)->notNull(),
            'userId' => $this->integer(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-familyUuid',
            self::FAMILY_USER,
            'familyUuid'
        );

        $this->addForeignKey(
            'fk-family_user-familyUuid',
            self::FAMILY_USER,
            'familyUuid',
            self::FAMILY,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-userId',
            self::FAMILY_USER,
            'userId'
        );

        $this->addForeignKey(
            'fk-family_user-userId',
            self::FAMILY_USER,
            'userId',
            self::USER,
            'id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::ROUTES, [
            '_id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'longitude' => $this->double()->notNull(),
            'latitude' => $this->double()->notNull(),
            'date' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createIndex(
            'idx-userId',
            self::ROUTES,
            'userId'
        );

        $this->addForeignKey(
            'fk-routes-userId',
            self::ROUTES,
            'userId',
            self::USER,
            'id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::SETTINGS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'parameter' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'userId' => $this->integer(),
            'title' => $this->string(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createIndex(
            'idx-userId',
            self::REGISTER,
            'userId'
        );

        $this->addForeignKey(
            'fk-routes-userId',
            self::REGISTER,
            'userId',
            self::USER,
            'id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::REGISTER);
        $this->dropTable(self::SETTINGS);
        $this->dropTable(self::ROUTES);
        $this->dropTable(self::FAMILY_USER);
        $this->dropTable(self::FAMILY);
        return true;
    }
}
