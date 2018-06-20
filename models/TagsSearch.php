<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tags;

/**
 * TagsSearch represents the model behind the search form about `app\models\Tags`.
 */
class TagsSearch extends Tags
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parent'], 'integer'],
            [['name', 'locality', 'type','a_type', 'color', 'komment', 'publish'], 'safe'],
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
        $query = Tags::find();

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
            'id' => $this->id
        ]);
        $query->andFilterWhere(['or',
            ['like', 'name', $_GET['text_like']],
            ['like', 'komment', $_GET['text_like']]]);
        if ($this->type != 10) $query->andFilterWhere(['type' => $this->type]);
        if (($this->a_type) and ($this->a_type != 999)) $query->andFilterWhere(['a_type' => $this->a_type]);
        if ($this->a_type == 999) $query->andWhere(['IS', 'a_type', NULL]);
        if ($this->parent != 10) $query->andFilterWhere(['parent' => $this->parent]);
        if ($this->publish != 10) $query->andFilterWhere(['publish' => $this->publish]);
        if ($this->locality != 'any') $query->andFilterWhere(['locality' => $this->locality]);

        return $dataProvider;
    }
}
