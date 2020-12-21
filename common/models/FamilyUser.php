<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "family_user".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $familyUuid
 * @property string $userId
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property User $user
 */
class FamilyUser extends FamilyModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'family_user';
    }

    /**
     * Behaviors.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'familyUuid', 'userId'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'familyUuid'], 'string', 'max' => 50],
            [['userId'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'familyUuid' => Yii::t('app', 'Семейство'),
            'userId' => Yii::t('app', 'Пользователь'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFamily()
    {
        return $this->hasOne(Family::class, ['uuid' => 'familyUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];
        $perm = parent::getPermissions();
        return $perm;
    }
}
