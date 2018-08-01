<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ControlParsing;

/**
 * ControlParsingSearch represents the model behind the search form about `app\models\ControlParsing`.
 */
class ControlParsingSearch extends ControlParsing
{
    /**
     * @inheritdoc
     */

    public $server;
    public $sort_by;

    public function rules()
    {
        return [
            [['id', 'date_start', 'date_finish','config_id','sort_by'], 'integer'],
            [['type','status'], 'safe'],
            [['server'], 'string'],
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
        $query = ControlParsing::find();

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
            'date_start' => $this->date_start,
            'date_finish' => $this->date_finish,
            'config_id' => $this->config_id,
        ]);
        if ($this->status)  $query->andFilterWhere(['in', 'status', $this->status]);
        if ($this->server)  $query->andFilterWhere(['server' => $this->server]);
        if ($this->type)  $query->andFilterWhere(['in', 'type', $this->type]);
       if ($this->sort_by == 1) $query->orderBy(['date_check' => SORT_DESC]);
       if ($this->sort_by == 0) $query->orderBy(['date_start' => SORT_DESC]);
        return $dataProvider;
    }
}
