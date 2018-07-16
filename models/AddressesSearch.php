<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Addresses;

/**
 * AddressesSearch represents the model behind the search form about `app\models\Addresses`.
 */
class AddressesSearch extends Addresses
{
    public $tagged;
    public $polygon_text;
    public $floorcount_down;
    public $floorcount_up;
    public $year_up;
    public $year_down;
    public $plus_tags;
    public $minus_tags;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'house_type', 'floorcount', 'year', 'tagged', 'floorcount_up', 'floorcount_down', 'year_up', 'year_down'], 'integer'],
            [['address', 'polygon_text','plus_tags','minus_tags'], 'string'],
            [['coords_x', 'coords_y', 'street', 'house', 'hull', 'locality', 'district', 'address_string_variants', 'precision_yandex', 'status', 'address', 'tagged', 'polygon_text', 'id'], 'safe'],
        ];
    }

    public function formName()
    {
        return '';
    }

    public function attributeLabels()
    {
        return [
            'year_up' => 'До года',
            'year_down' => 'От года',
            'floorcount_down' => 'От этажа',
            'floorcount_up' => 'До этажа',
            'tagged' => 'Tags',
            'house_type' => 'тип дома',
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
        $query = Addresses::find();
        $this->load($params);
        // add conditions that should always apply here
      if ($this->year_up)  $query->andFilterWhere(['<=', 'year', $this->year_up]);
      if ($this->year_down)   $query ->andFilterWhere(['>=', 'year', $this->year_down]);



        if ($this->house_type) $query->andFilterWhere(['house_type' => $this->house_type]);

        if ($this->floorcount_down) $query->andFilterWhere(['>=', 'floorcount', $this->floorcount_down]);
        if ($this->floorcount_up) $query->andFilterWhere(['<=', 'floorcount', $this->floorcount_up]);


        $query->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'hull', $this->hull])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andWhere(['<>', 'precision_yandex', 'street'])
            ->andfilterWhere(['id' => $this->id]);

         if ($this->status != 10) $query->andWhere(['status' => $this->status]);
        if ($this->tagged == 1) $query->andWhere(['IS', 'tags_id', NULL]);
        if ($this->tagged == 2) $query->andWhere(['<>', 'tags_id', '']);
        $session = Yii::$app->session;

        $polygon_id_addresses = [];
        if ($this->polygon_text) {
          //  info(" POLYGON");
            if (empty(Yii::$app->cache->get('all_addesses'))) {
                Yii::$app->cache->set('all_addesses', Addresses::find()->select('id,coords_x,coords_y')->asArray()->all());

            }
            $all_addesses = Yii::$app->cache->get('all_addesses');
            foreach ($all_addesses as $address) {
                // echo $address['coords_x']." ".$address['coords_y'];
                if (!isPointInsidePolygon(json_decode(substr($this->polygon_text, 1, -1)), [$address['coords_x'], $address['coords_y']])) continue;
                array_push($polygon_id_addresses, $address['id']);
            }
          if ($polygon_id_addresses)  $query->andWhere(['in','id',$polygon_id_addresses]);

        }
        if ($minus_tags_address = Tags::convertToArray($this->minus_tags)) {
            foreach ($minus_tags_address as $tag) {
                $this->andWhere(['not like', 'tags_id', "," . $tag . ","]);
            }

        }


        // группироем по условиям OR и AND
        if ($tags_address = Tags::convertToArray($this->plus_tags)) {
            $grouped_tags_addresses = $this->groupTags($tags_address);
            foreach ($grouped_tags_addresses as $grouped_tags_address) {
                $queryOR = ['or'];
                foreach ($grouped_tags_address as $item) {
                    array_push($queryOR, ['like', 'tags_id', "," . $item['id'] . ","]);
                }
                $this->andWhere($queryOR);

            }

        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        return $dataProvider;
    }

    protected function groupTags($tags)
    {
        if (empty(Yii::$app->cache->get('tags'))) {
            Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());

        }
        $all_tags = Yii::$app->cache->get('tags');


        $new_tags = [];
        foreach ($tags as $tag) {
            array_push($new_tags, $all_tags[$tag]);
        }


        return array_group_by($new_tags, 'type');

    }

    public
    static function QuickSearch($string)
    {
        // берем чисто текст из строки
        preg_match_all("/\w{4,}/iu", $string, $output_array);
        // echo " слова в поисковом запросе = " . $string;
        $street = $output_array[0][0];
        //  echo "<br> цифры в поисковом запросе";
        preg_match_all("/\d+/", $string, $output_array);
        // my_var_dump($output_array[0]);
        $numbers = $output_array[0];
        // add conditions that should always apply here
//        $QueryAddresses = Addresses::find();
//        foreach ($like_array as $like) {
//            $QueryAddresses->Where(['ilike','street', $like]);
//        }
//        foreach ($numbers as $number) {
//            $QueryAddresses->orwhere(['house' => $number]);
//            $QueryAddresses->orWhere(['hull' => $number]);
//        }
        if (strlen($street) > 3) {

            $addresses = Addresses::find()
                ->select('id,street,address')
                ->where(['like', 'street', $street])
                ->andfilterwhere(['house' => $numbers[0]])
                ->andfilterwhere(['hull' => $numbers[1]])
                ->limit(10)
                ->all();

        } else return false;
//        foreach ($addresses as $address) {
//            echo " <br>".$address->address ." id".$address->id;
//        }
        return $addresses;

    }
}
