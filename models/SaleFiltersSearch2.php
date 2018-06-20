<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SaleFilters;

/**
 * SaleFiltersSearch2 represents the model behind the search form about `app\models\SaleFilters`.
 */
class SaleFiltersSearch2 extends SaleFilters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'period_ads', 'house_type', 'price_down', 'price_up', 'grossarea_down', 'grossarea_up', 'status_blacklist2', 'agents', 'housekeepers', 'date_of_ads', 'floor_down', 'floor_up', 'floorcount_down', 'floorcount_up', 'not_last_floor', 'sort_by', 'mail_inform', 'sms_inform', 'is_super_filter', 'discount', 'date_start', 'date_finish', 'year_up', 'year_down', 'is_client'], 'integer'],
            [['name', 'rooms_count', 'locality', 'district', 'text_like', 'polygon_text', 'black_list_id', 'white_list_id', 'phone', 'id_sources', 'id_address', 'komment', 'tags_id'], 'safe'],
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
        $query = SaleFilters::find();

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
            'user_id' => $this->user_id,
            'house_type' => $this->house_type,
            'price_down' => $this->price_down,
            'price_up' => $this->price_up,
            'grossarea_down' => $this->grossarea_down,
            'grossarea_up' => $this->grossarea_up,
            'status_blacklist2' => $this->status_blacklist2,
            'agents' => $this->agents,
            'housekeepers' => $this->housekeepers,
            'date_of_ads' => $this->date_of_ads,
            'floor_down' => $this->floor_down,
            'floor_up' => $this->floor_up,
            'floorcount_down' => $this->floorcount_down,
            'floorcount_up' => $this->floorcount_up,
            'not_last_floor' => $this->not_last_floor,
            'sort_by' => $this->sort_by,
            'mail_inform' => $this->mail_inform,
            'sms_inform' => $this->sms_inform,
            'is_super_filter' => $this->is_super_filter,
            'discount' => $this->discount,
            'date_start' => $this->date_start,
            'date_finish' => $this->date_finish,
            'year_up' => $this->year_up,
            'year_down' => $this->year_down,
            'is_client' => $this->is_client,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['period_ads' => $this->period_ads])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andFilterWhere(['like', 'district', $this->district])
            ->andFilterWhere(['like', 'text_like', $this->text_like])
            ->andFilterWhere(['like', 'polygon_text', $this->polygon_text])
            ->andFilterWhere(['like', 'black_list_id', $this->black_list_id])
            ->andFilterWhere(['like', 'white_list_id', $this->white_list_id])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'id_sources', $this->id_sources])
            ->andFilterWhere(['like', 'id_address', $this->id_address])
            ->andFilterWhere(['like', 'komment', $this->komment])
            ->andFilterWhere(['like', 'tags_id', $this->tags_id]);

        return $dataProvider;
    }

    public function my_search($params)
    {
        $salefilters = SaleFilters::find()
            ->all();
        $this->load($params);
        $ids = [];
        foreach ($salefilters as $salefilter) {
            // проверка что есть клиент на данный объект
            if (($this->rooms_count) and ($salefilter->rooms_count) and (!in_array($this->rooms_count, explode(",", $salefilter->rooms_count)))) continue;

          //  echo "<br>".mb_strtolower($salefilter->name);
            // проверка на поиск имени в названии фильтра
            if ($this->name != '') {
                if (!strpos(mb_strtolower("-".$salefilter->name), mb_strtolower(trim($this->name)))) continue;
            }

            if (($this->period_ads != 0) and ($this->period_ads != $salefilter->period_ads)) continue;
            if (($this->is_client == 1) and ($salefilter->is_client != 1)) continue;

           // echo " есть  попадание";
            $ids[] = $salefilter->id;

        }


        $query = SaleFilters::find();

// add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

// grid filtering conditions
        $query->Where(['in', 'id', $ids]);


        return $dataProvider;
    }

}
