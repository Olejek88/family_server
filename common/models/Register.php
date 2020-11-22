<?php

namespace common\models;

use common\components\MainFunctions;
use Exception;
use Yii;

/**
 * This is the model class for table "register".
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $title
 * @property integer $userId
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class Register extends FamilyModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'register';
    }

    /**
     * Check Register model.
     * @param $title
     * @param $userId
     * @return mixed
     * @throws Exception
     */

    public static function addRegister($title, $userId)
    {
        $model = new Register();
        $model->title = $title;
        $model->userId = $userId;
        $model->uuid = MainFunctions::GUID();
        $model->save();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['uuid'], 'string', 'max' => 50],
            [['uuid'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
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
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Событие'),
            'userId' => Yii::t('app', 'Пользователь'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'userId', 'createdAt', 'changedAt'];
    }
}