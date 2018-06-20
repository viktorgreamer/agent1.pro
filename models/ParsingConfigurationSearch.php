<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ParsingConfiguration;

/**
 * ParsingConfigurationSearch represents the model behind the search form about `app\models\ParsingConfiguration`.
 */
class ParsingConfigurationSearch extends ParsingConfiguration
{
    /**
     * @inheritdoc
     */

    public  $ready;
    public function rules()
    {
        return [
            [['id', 'last_timestamp','module_id'], 'integer'],
            [['start_link', 'last_ip', 'id_sources', 'active', 'ready'], 'safe'],
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
        $agentpro = \Yii::$app->params['agent_pro'];
        $period_of_check = $agentpro->period_check;

        $query = ParsingConfiguration::find();

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
            'module_id' => $this->module_id
        ]);
        if ($this->id_sources != '10') $query->andFilterWhere(['id_sources' => $this->id_sources]);
        if ($this->active != '10') $query->andFilterWhere(['active' => $this->active]);
        if ($this->ready) $query->andwhere(['<', 'last_timestamp', time() - $period_of_check * 60 * 60]);
        $query->orderBy(['success_stop'=> SORT_DESC]);





        return $dataProvider;
    }
}
