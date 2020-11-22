<?php

namespace frontend\models;

use common\models\FamilyUser;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class FamilyUserSearch extends FamilyUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'title', 'familyUuid', 'userId'], 'safe'],
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
        $query = FamilyUser::find();

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
            'userId' => $this->userId,
            'familyUuid' => $this->familyUuid
        ]);

        return $dataProvider;
    }
}
