<?php

namespace frontend\models;

use common\models\Crash;
use Exception;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CrashSearch represents the model behind the search form about `common\models\Crash`.
 */
class CrashSearch extends Crash
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['report_id', 'app_version_code', 'app_version_name', 'phone_model', 'brand', 'product', 'android_version',
                'stack_trace', 'user_app_start_date', 'user_crash_date', 'logcat', 'userId'], 'safe'],
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
     * @throws Exception
     */
    public function search($params)
    {
        $query = Crash::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['_id' => SORT_DESC]]
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
        ]);
        return $dataProvider;
    }
}
