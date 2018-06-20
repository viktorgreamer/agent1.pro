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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'house_type', 'floorcount', 'year', 'tagged', 'floorcount_up', 'floorcount_down', 'year_up', 'year_down'], 'integer'],
            [['address', 'polygon_text'], 'string'],
            [['coords_x', 'coords_y', 'street', 'house', 'hull', 'locality', 'district', 'address_string_variants', 'precision_yandex', 'status', 'address', 'tagged', 'polygon_text', 'id'], 'safe'],
        ];
    }

    public function formName()
    {
        return '';
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
        $query->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'hull', $this->hull])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andWhere(['<>', 'precision_yandex', 'street'])
            ->andfilterWhere(['id' => $this->id]);

        if (!empty($this->house_type)) $query->andFilterWhere(['in', 'house_type', explode(",", $this->house_type)]);

        if (($this->floorcount_down != 0)) $query->andFilterWhere(['>=', 'floorcount', $this->floorcount_down]);
        if (($this->floorcount_up != 0)) $query->andFilterWhere(['<=', 'floorcount', $this->floorcount_up]);
        if (($this->year_up != 0)) $query->andFilterWhere(['<=', 'year', $this->year_up]);
        if (($this->year_down != 0)) $query->andFilterWhere(['>=', 'year', $this->year_down]);
        if ($this->status != 10) $query->andWhere(['status' => $this->status]);
        if ($this->tagged == 1) $query->andWhere(['IS', 'tags_id', NULL]);
        if ($this->tagged == 2) $query->andWhere(['<>', 'tags_id', '']);
        $session = Yii::$app->session;

        if (($this->polygon_text != '') or (!empty($session->get('tags_id_to_search')))) {
            $addresses = $query->all();
            $ids = [];
            foreach ($addresses as $address) {

                if ($this->polygon_text != '') {
                    if (!isPointInsidePolygon(json_decode(substr($this->polygon_text, 1, -1)), [$address->coords_x, $address->coords_y])) continue;

                }

                // searching via tags
               if (!empty($session->get('tags_id_to_search'))) {
                   if ($address->tags_id) {
                       $continue = false;

                       if (!empty($session->get('tags_id_to_search'))) {
                           $tags_plus = explode(',', $session->get('tags_id_to_search'));
                           foreach ($tags_plus as $tag_plus) {
                              // info("searching " . $tag_plus . "in " . $address->tags_id, 'default');
                               if (in_array($tag_plus, explode(',', $address->tags_id))) 1; // info($tag_plus . " in " . $address->tags_id,'success');
                               else {

                                  // info($tag_plus . " not in " . $address->tags_id, 'alert');
                                   $continue = true;
                                   break;
                               }

                           }
                       }
                   } else continue; // если tags нет то сразу пропускаем поисе по tags
               }

                if ($continue) continue;

                $ids[] = $address->id;
            }
            $query = Addresses::find()
                ->where(['in', 'id', $ids]);


        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        return $dataProvider;
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
