<?php

namespace frontend\models;

use common\models\Route;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class RouteSearch extends Route
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['userId', 'date', 'longitude', 'latitude', 'createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Route::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'date' => $this->date,
            'userId' => $this->userId
        ]);

        return $dataProvider;
    }
}
