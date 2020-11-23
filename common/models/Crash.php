<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "crash".
 *
 * @property string $_id
 * @property string $report_id
 * @property string $app_version_code
 * @property string $app_version_name
 * @property string $phone_model
 * @property string $brand
 * @property string $product
 * @property string $android_version
 * @property string $stack_trace
 * @property string $user_app_start_date
 * @property string $user_crash_date
 * @property string $logcat
 * @property string $userId
 *
 * @property User $user
 * @property string $createdAt
 * @property string $changedAt
 */
class Crash extends FamilyModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crash';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_id', 'app_version_code', 'app_version_name', 'phone_model', 'brand', 'product', 'android_version',
                'stack_trace', 'user_app_start_date', 'user_crash_date', 'logcat', 'server_url', 'userId'], 'string'],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'report_id' => Yii::t('app', 'Идентификатор'),
            'app_version_code' => Yii::t('app', 'Версия кода'),
            'app_version_name' => Yii::t('app', 'Версия'),
            'phone_model' => Yii::t('app', 'Модель телефона'),
            'brand' => Yii::t('app', 'Брэнд'),
            'product' => Yii::t('app', 'Продукт'),
            'android_version' => Yii::t('app', 'Андроид'),
            'stack_trace' => Yii::t('app', 'Стэк'),
            'user_app_start_date' => Yii::t('app', 'Дата старта'),
            'user_crash_date' => Yii::t('app', 'Дата вылета'),
            'logcat' => Yii::t('app', 'Logcat'),
            'userId' => Yii::t('app', 'Исполнитель'),
            'server_url' => Yii::t('app', 'Сервер'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['report_id', 'app_version_code', 'app_version_name', 'phone_model', 'brand', 'product', 'android_version',
            'stack_trace', 'user_app_start_date', 'user_crash_date', 'logcat', 'server_url',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['uuid' => 'userId']);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['stack'] = 'stack' . $class;
        return $perm;
    }
}