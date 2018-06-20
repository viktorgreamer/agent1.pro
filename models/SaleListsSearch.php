<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SaleLists;
use app\models\Sale;
use yii\data\Pagination;

/**
 * SaleListsSearch represents the model behind the search form about `app\models\SaleLists`.
 */
class SaleListsSearch extends SaleLists
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type', 'sale_disactive'], 'integer'],
            [['name', 'list_of_ids', 'komment', 'tags_id', 'type','regions'], 'safe'],
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
        $query = SaleLists::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            echo "Даннйе не провалидировались";
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sale_disactive' => $this->sale_disactive,
        ]);
        if ($this->type != 0) {
            $query->andWhere(['type' => $this->type]);
        }
        if ($this->user_id != 0) {
            $query->andWhere(['user_id' => $this->user_id]);
        }
        if ($this->regions != 10) {
            $query->andWhere(['regions' => $this->regions]);
        }

        /*$query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'list_of_ids', $this->list_of_ids])
            ->andFilterWhere(['like', 'komment', $this->komment])
            ->andFilterWhere(['like', 'tags_id', $this->tags_id]);*/

        return $dataProvider;
    }
}
