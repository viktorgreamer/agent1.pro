<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WdCookies;

/**
 * WdCookiesSearch represents the model behind the search form about `app\models\WdCookies`.
 */
class WdCookiesSearch extends WdCookies
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time'], 'integer'],
            [['ip_port', 'id_server', 'body'], 'safe'],
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
        $query = WdCookies::find();

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
            'time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'ip_port', $this->ip_port])
            ->andFilterWhere(['like', 'id_server', $this->id_server])
            ->andFilterWhere(['like', 'body', $this->body]);

        return $dataProvider;
    }
}
