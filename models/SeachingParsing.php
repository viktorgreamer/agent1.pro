<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Parsing;

/**
 * SeachingParsing represents the model behind the search form about `app\models\Parsing`.
 */
class SeachingParsing extends Parsing
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'original_date', 'count_of_views', 'date_start', 'rooms_count', 'price', 'house_type', 'id_address', 'year', 'floor', 'floorcount', 'id_sources', 'status_unique_phone', 'load_analized', 'status_unique_date', 'status_blacklist2', 'geocodated', 'processed', 'broken', 'average_price', 'average_price_count', 'average_price_address', 'average_price_address_count', 'average_price_same', 'average_price_same_count', 'radius', 'date_of_check', 'disactive', 'is_balcon'], 'integer'],
            [['title', 'phone1', 'city', 'address', 'locality', 'description', 'images', 'url', 'person', 'id_irr_duplicate', 'id_in_source', 'tags', 'street'], 'safe'],
            [['coords_x', 'coords_y', 'grossarea', 'kitchen_area', 'living_area', 'living_area_if_rooms'], 'number'],
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
        $query = Parsing::find();

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
            'original_date' => $this->original_date,
            'count_of_views' => $this->count_of_views,
            'date_start' => $this->date_start,
            'rooms_count' => $this->rooms_count,
            'price' => $this->price,
            'house_type' => $this->house_type,
            'coords_x' => $this->coords_x,
            'coords_y' => $this->coords_y,
            'id_address' => $this->id_address,
            'year' => $this->year,
            'floor' => $this->floor,
            'floorcount' => $this->floorcount,
            'id_sources' => $this->id_sources,
            'grossarea' => $this->grossarea,
            'kitchen_area' => $this->kitchen_area,
            'living_area' => $this->living_area,
            'status_unique_phone' => $this->status_unique_phone,
            'load_analized' => $this->load_analized,
            'status_unique_date' => $this->status_unique_date,
            'status_blacklist2' => $this->status_blacklist2,
            'geocodated' => $this->geocodated,
            'processed' => $this->processed,
            'broken' => $this->broken,
            'average_price' => $this->average_price,
            'average_price_count' => $this->average_price_count,
            'average_price_address' => $this->average_price_address,
            'average_price_address_count' => $this->average_price_address_count,
            'average_price_same' => $this->average_price_same,
            'average_price_same_count' => $this->average_price_same_count,
            'radius' => $this->radius,
            'date_of_check' => $this->date_of_check,
            'disactive' => $this->disactive,
            'living_area_if_rooms' => $this->living_area_if_rooms,
            'is_balcon' => $this->is_balcon,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'phone1', $this->phone1])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'images', $this->images])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'person', $this->person])
            ->andFilterWhere(['like', 'id_irr_duplicate', $this->id_irr_duplicate])
            ->andFilterWhere(['like', 'id_in_source', $this->id_in_source])
            ->andFilterWhere(['like', 'tags', $this->tags])
            ->andFilterWhere(['like', 'street', $this->street]);

        return $dataProvider;
    }
}
