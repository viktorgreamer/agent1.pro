<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ParsingControl;

/**
 * ParsingControlSearch represents the model behind the search form about `app\models\ParsingControl`.
 */
class ParsingControlSearch extends ParsingControl
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_ads_api'], 'integer'],
            [['date', 'log', 'name_table'], 'safe'],
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
        $query = ParsingControl::find();

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
            'id_ads_api' => $this->id_ads_api,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'log', $this->log])
            ->andFilterWhere(['like', 'name_table', $this->name_table]);

        return $dataProvider;
    }
}
