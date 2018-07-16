<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Addresses]].
 *
 * @see Addresses
 */
class AddressesQuery extends ActiveQuery
{
    public $addressfilter;

    public function __construct()
    {
        $modelClass = Addresses::className();
        parent::__construct($modelClass);
    }


    public function search()
    {
        $this->main();
        $this->geo();
        $this->percent();
        $this->lists();
        $this->tags();
        $this->status();
        $this->uniqueness();
        $this->sorting();

    }



    protected
    function main()
    {

        $this->andFilterWhere(['<=', 'year', $this->year_up])
            ->andFilterWhere(['>=', 'year', $this->year_down]);



        $this->andFilterWhere(['house_type' => $this->house_type]);

        if ($this->floorcount_down) $this->andFilterWhere(['>=', 'floorcount', $this->floorcount_down]);
        if ($this->floorcount_up) $this->andFilterWhere(['<=', 'floorcount', $this->floorcount_up]);


        $this->andFilterWhere(['like', 'street', $this->street])
            ->andFilterWhere(['like', 'hull', $this->hull])
            ->andFilterWhere(['like', 'house', $this->house])
            ->andFilterWhere(['like', 'locality', $this->locality])
            ->andWhere(['<>', 'precision_yandex', 'street'])
            ->andfilterWhere(['id' => $this->id]);

    }

    protected
    function status()
    {

        if ($this->sale_disactive != 10) {// and ($this->sale_disactive != 0)) $this->andWhere(['s.disactive' => $this->sale_disactive]);
            switch ($this->sale_disactive) {
                case 0:
                    $this->andWhere(['<>', 's.disactive', [1, 2]]);
                    //  $this->andWhere(['<>', 'sim.status', SaleSimilar::SOLD]);
                    break;
                case 4:
                    $this->andWhere(['<>', 's.disactive', [1, 2]]);
                    $this->andWhere(['<>', 'sim.status', SaleSimilar::SOLD]);
                    break;
            }
        }

        if ($this->moderated) $this->andfilterWhere(['sim.moderated' => $this->moderated]);


    }

    protected
    function tags()
    {

        // если есть plus_tags_sale
        $tags_sale = $this->plus_tags_sale();
        if ($tags_sale) {
            $grouped_tags_addresses = $this->groupTags($tags_sale);
            foreach ($grouped_tags_addresses as $grouped_tags_address) {
                $queryOR = ['or'];
                foreach ($grouped_tags_address as $item) {
                    array_push($queryOR, ['like', 'sim.tags_id', "," . $item['id'] . ","]);
                }
                $this->andWhere($queryOR);

            }

        }
        $tags_sale = $this->minus_tags_sale();
        if ($tags_sale) {
            foreach ($tags_sale as $tag) {
                $this->andWhere(['not like', 'sim.tags_id', "," . $tag . ","]);
            }
        }


    }

// уникализация
    protected
    function uniqueness()
    {
        //    info("UNIQUNSS = ".$this->uniqueness);
        switch ($this->uniqueness) {
            case SaleFilters::UNIQUE_MAIN :
                break;

            case SaleFilters::UNIQUE_ROW:

                $this->groupBy('s.id_similar,s.phone1');
                break;

            case SaleFilters::UNIQUE_OBJECT:

                $this->groupBy('s.id_similar');
                break;

        }

    }

    protected
    function sorting()
    {
        switch ($this->sort_by) {
            case SaleFilters::SORTING_ID:
                $this->orderBy(['s.id' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_PRICE_ASC:
                $this->orderBy(['s.price' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_PRICE_DESC:
                $this->orderBy(['s.price' => SORT_DESC]);
                break;
            case SaleFilters::SORTING_DATE_START_ASC:
                $this->orderBy(['s.date_start' => SORT_DESC]);
                break;

            case SaleFilters::SORTING_DATE_START_DESC:
                $this->orderBy(['s.date_start' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_ID_ADDRESS_ASC:
                $this->orderBy(['s.id_address' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_ID_ADDRESS_DESC:
                $this->orderBy(['s.id_address' => SORT_DESC]);
                break;


        }

    }

    protected
    function percent()
    {
        if ($this->discount) {
            $multiplier = (100 - $this->discount) / 100;
            $this->andWhere(['<=', 's.price', new Expression("s.average_price_same*" . $multiplier)]);

        }
    }

    protected
    function lists()
    {

        if ($this->id) {
            $this->andFilterWhere(['not in', 's.id', $this->white]);
            $this->andFilterWhere(['not in', 's.id', $this->black]);
            $this->andFilterWhere(['not in', 's.id_similar', $this->similar_white]);
            $this->andFilterWhere(['not in', 's.id_similar', $this->similar_black]);
            $this->andFilterWhere(['not in', 's.id', $this->processed]);
            // $this->andWhere(['<', 's.price', 'controls.price']);
            $this->andWhere([
                'OR',
                ['<', 's.price', 'controls.price'],
                ['controls.price' => NULL]
            ]);


        }
    }

//  выбоорка если заданы какие либо географичсеские параметры
    public
    function geo()
    {
        // если устанвлено поле регион, то игнорируется поле полигон
        if ($this->regions) {
            $region = SaleFilters::findOne($this->regions);
            $this->polygon_text = $region->polygon_text;
        }

        $polygon_id_addresses = [];
        if ($this->polygon_text) {
            if (empty(Yii::$app->cache->get('all_addesses'))) {
                Yii::$app->cache->set('all_addesses', Addresses::find()->select('id,coords_x,coords_y')->asArray()->all());

            }
            $all_addesses = Yii::$app->cache->get('all_addesses');
            foreach ($all_addesses as $address) {
                // echo $address['coords_x']." ".$address['coords_y'];
                if (!isPointInsidePolygon(json_decode(substr($this->polygon_text, 1, -1)), [$address['coords_x'], $address['coords_y']])) continue;
                array_push($polygon_id_addresses, $address['id']);
            }

        }
        // если есть полигон то объединяем его  с выбранными id_addresses иначе просто id_address
        if (!empty($this->polygon_text)) {
            $this->andFilterWhere(['in', 's.id_address', array_merge($polygon_id_addresses, Methods::convertToArrayWithBorders($this->id_address))]);
        } else {
            $this->andFilterWhere(['in', 's.id_address', Methods::convertToArrayWithBorders($this->id_address)]);

        }

        // вычитаем минус id_address
        $this->andFilterWhere(['not in', 's.id_address', Methods::convertToArrayWithBorders($this->minus_id_addresses)]);


        // $this->polygon_text = $this->polygon_text;

        // tags
        $this->tags_address();


    }

    protected
    function tags_address()
    {

        $tags_address = $this->minus_tags_address();
        if ($tags_address) {
            foreach ($tags_address as $tag) {
                $this->andWhere(['not like', 'address.tags_id', "," . $tag . ","]);
            }

        }
        $tags_address = $this->plus_tags_address();

        // группироем по условиям OR и AND
        if ($tags_address) {
            $grouped_tags_addresses = $this->groupTags($tags_address);
            foreach ($grouped_tags_addresses as $grouped_tags_address) {
                $queryOR = ['or'];
                foreach ($grouped_tags_address as $item) {
                    array_push($queryOR, ['like', 'address.tags_id', "," . $item['id'] . ","]);
                }
                $this->andWhere($queryOR);

            }

        }
    }


    protected
    function groupTags($tags)
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


}
