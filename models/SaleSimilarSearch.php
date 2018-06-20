<?php

namespace app\Models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SaleSimilar;

/**
 * SaleSimilarSearch represents the model behind the search form about `app\models\SaleSimilar`.
 */
class SaleSimilarSearch extends SaleSimilar
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'price_up', 'price_down', 'moderated', 'status', 'debug_status'], 'integer'],
            [['similar_ids', 'tags_id', 'similar_ids_all'], 'safe'],
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
        $query = SaleSimilar::find();

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
            'price_up' => $this->price_up,
            'price_down' => $this->price_down,
            'moderated' => $this->moderated,
            'status' => $this->status,
            'debug_status' => $this->debug_status,
        ]);

        $query->andFilterWhere(['like', 'similar_ids', $this->similar_ids])
            ->andFilterWhere(['like', 'tags_id', $this->tags_id])
            ->andFilterWhere(['like', 'similar_ids_all', $this->similar_ids_all]);

        return $dataProvider;
    }
}
