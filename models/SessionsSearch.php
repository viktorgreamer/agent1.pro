<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sessions;

/**
 * SessionsSearch represents the model behind the search form about `app\models\Sessions`.
 */
class SessionsSearch extends Sessions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'datetime_start', 'datetime_check','status'], 'integer'],
            [['id_session', 'current_url'], 'safe'],
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
        $query = Sessions::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'datetime_start' => $this->datetime_start,
            'datetime_check' => $this->datetime_check,
        ]);

        $query->andFilterWhere(['like', 'id_session', $this->id_session])
            ->andFilterWhere(['like', 'current_url', $this->current_url]);

        return $dataProvider;
    }
}
