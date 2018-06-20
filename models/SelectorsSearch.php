<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Selectors;

/**
 * SelectorsSearch represents the model behind the search form about `app\models\Selectors`.
 */
class SelectorsSearch extends Selectors
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_sources', 'type', 'count', 'id_error', 'id_parent'], 'integer'],
            [['pattern'], 'safe'],
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
        $query = Selectors::find();

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


        if ($this->id_parent != 10000) $query->andWhere(['id_parent' => $this->id_parent]);
        if ($this->id_sources) $query->andWhere(['id_sources' => $this->id_sources]);
        if ($this->type) $query->andWhere(['type' => $this->type]);
        if ($this->count) $query->andWhere(['count' => $this->count]);
        if ($this->id_error) $query->andWhere(['id_error' => $this->id_error]);

        $query->andFilterWhere(['like', 'pattern', $this->pattern]);

        return $dataProvider;
    }
}
