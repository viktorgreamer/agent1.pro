<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SaleFilters;

/**
 * SaleFiltersSearch2 represents the model behind the search form about `app\models\SaleFilters`.
 */
class SaleFiltersSearch extends SaleFilters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'period_ads', 'house_type', 'price_down', 'price_up', 'grossarea_down', 'grossarea_up', 'status_blacklist2', 'agents', 'housekeepers', 'date_of_ads', 'floor_down', 'floor_up', 'floorcount_down', 'floorcount_up', 'not_last_floor', 'sort_by', 'mail_inform', 'sms_inform', 'is_super_filter', 'discount', 'date_start', 'date_finish', 'year_up', 'year_down', 'type'], 'integer'],
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
            'user_id' => $this->user_id,
            'type' => $this->type,

        ]);
        if ($this->rooms_count)  $query->andFilterWhere(['like', 'rooms_count', $this->rooms_count]);

        $query->andFilterWhere(['like', 'name', $this->name]);


        return $dataProvider;
    }

    public function my_search($params)
    {
        $salefilters = SaleFilters::find()
            ->all();
        $this->load($params);
        $ids = [];
        foreach ($salefilters as $salefilter) {
           //
            if (($this->rooms_count) and ($salefilter->rooms_count) and (!in_array($this->rooms_count, explode(",", $salefilter->rooms_count)))) continue;

            //  echo "<br>".mb_strtolower($salefilter->name);
            // проверка на поиск имени в названии фильтра
            if ($this->name != '') {
                if (!strpos(mb_strtolower("-" . $salefilter->name), mb_strtolower(trim($this->name)))) continue;
            }

            if (($this->period_ads != 0) and ($this->period_ads != $salefilter->period_ads)) continue;
            if (($this->type != $salefilter->type)) continue;

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

    public function getClients($params = [])
    {
        if (in_array('clients', $params)) {
            if (!empty($params['rooms_count'])) {
                $clients = SaleFilters::find()
                    ->where(['in', 'rooms_count', $params['rooms_count']])
                    ->andWhere(['in','type', [SaleFilters::AGENT_TYPE,SaleFilters::CLIENT_TYPE]])
                  //  ->limit(10)
                    ->all();

            } else {
                $clients = SaleFilters::find()
                    ->Where(['in','type', [SaleFilters::AGENT_TYPE,SaleFilters::CLIENT_TYPE]])
                    ->all();
            }

        }
        if ($clients) return $clients;

    }

}
