<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "route".
 *
 * @property integer $_id
 * @property integer $userId
 * @property double $longitude
 * @property double $latitude
 * @property string $date
 *
 * @property User $user
 */
class Route extends FamilyModel
{
    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'routes';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'userId',
                    'latitude',
                    'longitude',
                    'date'
                ],
                'required'
            ],
            [['userId'], 'number'],
            [['longitude', 'latitude',], 'double'],
            [['date'], 'string', 'max' => 50],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'userId' => Yii::t('app', 'Пользователь'),
            'date' => Yii::t('app', 'Дата')
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields1()
    {
        return [
            '_id',
            'userId',
            'user' => function ($model) {
                return $model->user;
            },
            'latitude',
            'longitude',
            'date'
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

}
