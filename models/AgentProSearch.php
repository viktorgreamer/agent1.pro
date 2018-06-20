<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AgentPro;

/**
 * AgentProSearch represents the model behind the search form about `app\models\AgentPro`.
 */
class AgentProSearch extends AgentPro
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'id_error', 'time', 'status_parsingsync', 'status_detailed_parsing', 'status_processing', 'status_parsing_avito_phones', 'status_analizing', 'status_parsing_new', 'status_sync', 'status_geocogetion'], 'integer'],
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
        $query = AgentPro::find();

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
            'status' => $this->status,
            'id_error' => $this->id_error,
            'time' => $this->time,
            'status_parsingsync' => $this->status_parsingsync,
            'status_detailed_parsing' => $this->status_detailed_parsing,
            'status_processing' => $this->status_processing,
            'status_parsing_avito_phones' => $this->status_parsing_avito_phones,
            'status_analizing' => $this->status_analizing,
            'status_parsing_new' => $this->status_parsing_new,
            'status_sync' => $this->status_sync,
            'status_geocogetion' => $this->status_geocogetion,
        ]);

        return $dataProvider;
    }
}
