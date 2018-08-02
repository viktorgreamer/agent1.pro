<?php

namespace app\models;

use app\models\SaleFilters;
use app\models\Sale;
use app\models\SalefiltersModels\SaleFiltersOnControl;
use yii\db\ActiveQuery;
use yii\db\Query;
use Yii;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[Sale]].
 *
 * @see Sale
 */
class SaleQuery extends ActiveQuery
{
    public $salefilter;

    public function __construct()
    {
        $modelClass = Sale::className();
        parent::__construct($modelClass);
    }
    public function searchSimilar($id_similar)
    {
        $this->from(['s' => Sale::tableName()]);
        // присоединяем связи
        $this->joinWith(['agent AS agent']);
        $this->joinWith(['addresses AS address']);

        $this->where(['s.id_similar' => $id_similar]);

    }


    public function search($salefilter, $type_of_show = SaleFilters::SHOW_MAIN)
    {
        $this->salefilter = $salefilter;
        $this->relations();

        switch ($type_of_show) {
            case SaleFilters::SHOW_WHITE:
                {
                    $this->andFilterWhere(['in', 's.id', $this->salefilter->white]);
                    break;
                }
            case SaleFilters::SHOW_BLACK:
                {
                    $this->andFilterWhere(['in', 's.id', $this->salefilter->black]);
                    break;
                }
            case SaleFilters::SHOW_SIMILAR_WHITE:
                {
                    $this->andFilterWhere(['in', 's.id_similar', $this->salefilter->similar_white]);
                    break;
                }
            case SaleFilters::SHOW_SIMILAR_BLACK:
                {
                    $this->andFilterWhere(['in', 's.id_similar', $this->salefilter->similar_black]);
                    break;
                }
            case SaleFilters::SHOW_PROCESSED:
                {
                    $this->andFilterWhere(['in', 's.id', $this->salefilter->processed]);
                    break;
                }
            case SaleFilters::SHOW_ON_CONTROLS:
                {
                    $this->andWhere(['>=', 's.price', 'controls.price']);
                    break;
                }
            default:
                {
                    $this->main();
                    $this->geo();
                    $this->percent();
                    $this->lists();
                    $this->tags();
                }

        }
        $this->status();
        $this->uniqueness();
        $this->sorting();


    }


    // подключение связей
    public function relations()
    {
        $this->from(['s' => Sale::tableName()]);
        // присоединяем связи
        $this->joinWith(['agent AS agent']);
        $this->joinWith(['addresses AS address']);
        // подлючение связи контроля
        Yii::$app->params['id_salefilter'] = $this->salefilter->id;
        if ($this->salefilter->id) $this->joinWith(['controls as controls']);
//        $param = $this->salefilter->id;;
//        $this->joinWith([
//            'controls' => function ($param) {
//                $this->onCondition(['controls.id_salefilter' => $param]);
//            }
//        ]);
        $this->joinWith(['similar AS sim']);
    }

    protected function main()
    {

        if ($this->salefilter->period_ads) $this->Where(['>=', 's.date_start', (time() - $this->salefilter->period_ads * 86400)]);
        $this->andFilterWhere(['>=', 's.price', $this->salefilter->price_down])
            ->andFilterWhere(['<=', 's.price', $this->salefilter->price_up])
            ->andFilterWhere(['<=', 's.year', $this->salefilter->year_up])
            ->andFilterWhere(['>=', 's.year', $this->salefilter->year_down])
            ->andFilterWhere(['<=', 's.grossarea', $this->salefilter->grossarea_up])
            ->andFilterWhere(['>=', 's.grossarea', $this->salefilter->grossarea_down]);


        $this->andFilterWhere(['in', 's.rooms_count', $this->salefilter->rooms_count]);
        if (!empty($this->salefilter->id_sources)) $this->andFilterWhere(['in', 's.id_sources', $this->salefilter->id_sources]);

        // неактивные ресурсы
      //  info("disactive_id_sources = ".$this->salefilter->disactive_id_sources);
        if (!empty($this->salefilter->disactive_id_sources)) {
               foreach ($this->salefilter->disactive_id_sources as $disactive_id_source) {
                $this->andFilterWhere(['not like', 'sim.id_sources', $disactive_id_source]);
            }

        }

        $this->andFilterWhere(['in', 's.house_type', Methods::convertToArrayWithBorders($this->salefilter->house_type)]);
        $this->andFilterWhere(['in', 's.phone1', Methods::convertToArrayWithBorders($this->salefilter->phone)]);
        if ($this->salefilter->person_type) {
            $this->andWhere(['agent.person_type' => $this->salefilter->person_type]);
        }

        if ($this->salefilter->floor_down) $this->andFilterWhere(['>=', 's.floor', $this->salefilter->floor_down]);
        if ($this->salefilter->floor_up) $this->andFilterWhere(['<=', 's.floor', $this->salefilter->floor_up]);
        if ($this->salefilter->floorcount_down) $this->andFilterWhere(['>=', 's.floorcount', $this->salefilter->floorcount_down]);
        if ($this->salefilter->floorcount_up) $this->andFilterWhere(['<=', 's.floorcount', $this->salefilter->floorcount_up]);



        if ($this->salefilter->text_like != '') $this->andFilterWhere(['or',
            ['like', 's.address', $this->salefilter->text_like],
            ['like', 's.description', $this->salefilter->text_like]]);
        if ($this->salefilter->not_last_floor) {
            $this->andWhere(['<>', 's.floorcount', new Expression('s.floor')]);
        }

    }

    protected function status()
    {

        if ($this->salefilter->sale_disactive != 10) {// and ($this->salefilter->sale_disactive != 0)) $this->andWhere(['s.disactive' => $this->salefilter->sale_disactive]);
            switch ($this->salefilter->sale_disactive) {
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

        if ($this->salefilter->moderated) $this->andfilterWhere(['sim.moderated' => $this->salefilter->moderated]);


    }

    protected function tags()
    {

        // если есть plus_tags_sale
        $tags_sale = $this->salefilter->plus_tags_sale();
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
        $tags_sale = $this->salefilter->minus_tags_sale();
        if ($tags_sale) {
            foreach ($tags_sale as $tag) {
                $this->andWhere(['not like', 'sim.tags_id', "," . $tag . ","]);
            }
        }


    }

    // уникализация
    protected function uniqueness()
    {
    //    info("UNIQUNSS = ".$this->salefilter->uniqueness);
        switch ($this->salefilter->uniqueness) {
            case SaleFilters::UNIQUE_MAIN :
                $this->groupBy('s.id');
                break;

            case SaleFilters::UNIQUE_ROW:

                $this->groupBy('s.id,s.id_similar,s.phone1');
                break;

            case SaleFilters::UNIQUE_OBJECT:

                $this->groupBy('s.id,s.id_similar');
                break;

        }

    }

    protected function sorting()
    {
        switch ($this->salefilter->sort_by) {
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

    protected function percent()
    {
        if ($this->salefilter->discount) {
            $multiplier = (100 - $this->salefilter->discount) / 100;
            $this->andWhere(['<=', 's.price', new Expression("s.average_price_same*" . $multiplier)]);

        }
    }

    protected function lists()
    {

        if ($this->salefilter->id) {
            $this->andFilterWhere(['not in', 's.id', $this->salefilter->white]);
            $this->andFilterWhere(['not in', 's.id', $this->salefilter->black]);
            $this->andFilterWhere(['not in', 's.id_similar', $this->salefilter->similar_white]);
            $this->andFilterWhere(['not in', 's.id_similar', $this->salefilter->similar_black]);
            $this->andFilterWhere(['not in', 's.id', $this->salefilter->processed]);
            // $this->andWhere(['<', 's.price', 'controls.price']);
            $this->andWhere([
                'OR',
                ['<', 's.price', 'controls.price'],
                ['controls.price' => NULL]
            ]);


        }
    }

    //  выбоорка если заданы какие либо географичсеские параметры
    public function geo()
    {
        // если устанвлено поле регион, то игнорируется поле полигон
        if ($this->salefilter->regions) {
            $region = SaleFilters::findOne($this->salefilter->regions);
            $this->salefilter->polygon_text = $region->polygon_text;
        }

        $polygon_id_addresses = [];
        if ($this->salefilter->polygon_text) {
            if (empty(Yii::$app->cache->get('all_addesses'))) {
                Yii::$app->cache->set('all_addesses', Addresses::find()->select('id,coords_x,coords_y')->asArray()->all());

            }
            $all_addesses = Yii::$app->cache->get('all_addesses');
            foreach ($all_addesses as $address) {
                // echo $address['coords_x']." ".$address['coords_y'];
                if (!isPointInsidePolygon(json_decode(substr($this->salefilter->polygon_text, 1, -1)), [$address['coords_x'], $address['coords_y']])) continue;
                array_push($polygon_id_addresses, $address['id']);
            }

        }
        // если есть полигон то объединяем его  с выбранными id_addresses иначе просто id_address
        if (!empty($this->salefilter->polygon_text)) {
            $this->andFilterWhere(['in', 's.id_address', array_merge($polygon_id_addresses, Methods::convertToArrayWithBorders($this->salefilter->id_address))]);
        } else {
            $this->andFilterWhere(['in', 's.id_address', Methods::convertToArrayWithBorders($this->salefilter->id_address)]);

        }

        // вычитаем минус id_address
        $this->andFilterWhere(['not in', 's.id_address', Methods::convertToArrayWithBorders($this->salefilter->minus_id_addresses)]);


        // $this->polygon_text = $this->salefilter->polygon_text;

        // tags
        $this->tags_address();


    }

    protected function tags_address()
    {

        $tags_address = $this->salefilter->minus_tags_address();
        if ($tags_address) {
            foreach ($tags_address as $tag) {
                $this->andWhere(['not like', 'address.tags_id', "," . $tag . ","]);
            }

        }
        $tags_address = $this->salefilter->plus_tags_address();

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





}
