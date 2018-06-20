<?php

namespace app\models;

use app\components\TagsWidgets;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sale;
use app\models\SaleFilters;

use yii\data\Pagination;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\Query;

/* @var $salefilter SaleFilters */

/**
 * SaleSearch represents the model behind the search form about `app\models\Sale`.
 */
class SaleSearch extends SaleFilters
{

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

    public function search($salefilter)
    {
        $sales = Sale::find()
            ->Where(['>=', 'date_start', (time() - $salefilter['period_ads'] * 86400)])
            ->andFilterWhere(['>=', 'price', $salefilter->price_down])
            ->andFilterWhere(['<=', 'price', $salefilter->price_up])
            ->andFilterWhere(['>=', 'floor', $salefilter->floor_down])
            ->andFilterWhere(['<=', 'floor', $salefilter->floor_up])
            ->andFilterWhere(['<=', 'year', $salefilter->year_up])
            ->andFilterWhere(['>=', 'year', $salefilter->year_down])
            ->andFilterWhere(['>=', 'floorcount', $salefilter->floorcount_down])
            ->andFilterWhere(['<=', 'floorcount', $salefilter->floorcount_up])
            //  ->andFilterWhere(['status_blacklist2' => $salefilter->status_blacklist2])

            ->andFilterWhere(['>=', 'grossarea', $salefilter->grossarea_down])
            ->andFilterWhere(['<=', 'grossarea', $salefilter->grossarea_up])
            ->andWhere(['disactive' => 0])
            ->all();

        // если выбраны  те и те то принимаем $all = true;
        if (($salefilter->agents) and ($salefilter->housekeepers)) $all = true;
        // если не выбраны те и те то тоже
        if ((!$salefilter->agents) and (!$salefilter->housekeepers)) $all = true;
        // теперь прогоняем дополнительные условия
        $ids = [];
        foreach ($sales as $sale) {
            if (!$all) {
                if (($salefilter->agents) and ($sale->status_blacklist2 == 0)) continue;
                if (($salefilter->housekeepers) and ($sale->status_blacklist2 == 1)) continue;
            }
            if ((!empty($salefilter->rooms_count)) and (!in_array($sale->rooms_count, explode(",", $salefilter->rooms_count)))) continue;
            if ((!empty($salefilter->id_sources)) and (!in_array($sale->id_sources, explode(",", $salefilter->id_sources)))) continue;
            if ((!empty($salefilter->id_address)) and (!in_array($sale->id_address, explode(",", $salefilter->id_address)))) continue;
            if ((!empty($salefilter->phone)) and (!in_array($sale->phone1, explode(",", trim($salefilter->phone))))) continue;
            if (($salefilter->house_type != 0) and ($sale->house_type != $salefilter->house_type)) continue;


            if ($salefilter->text_like != '') {
                if ((!strpos(mb_strtolower($sale->address), mb_strtolower($salefilter->text_like))) and
                    (!strpos(mb_strtolower($sale->description), mb_strtolower($salefilter->text_like)))
                ) continue;
            }
            // если ранее в бэклисте
            if (($salefilter->black_list_id != '') and (in_array($sale->id, explode(",", $salefilter->black_list_id)))) continue;
            // если поледний этаж
            if (($salefilter->not_last_floor) and ($sale->floorcount == $sale->floor)) continue;
            // если попадает в полигон
            // echo $salefilter->polygon_text;
            if (($salefilter->polygon_text != '') and (!isPointInsidePolygon(json_decode(substr($salefilter->polygon_text, 1, -1)), [$sale->coords_x, $sale->coords_y]))) continue;
            // сели супер фильтрр
            if ($salefilter->discount != 0) {
                //  echo " идет проверка суперфилтра";
                if ($sale->average_price_same == 0) continue;
                elseif (($sale->price > (100 - $salefilter->discount) / 100 * $sale->average_price_same) or ($sale->price < (100 - $salefilter->discount) / 100 * $sale->average_price_address)) continue;
            }
            $ids[] = $sale->id;

        }
        // выполняем новый поиск исходя из этих условий

        $query = Sale::find()
            ->where(['in', 'id', $ids]);
        if ($salefilter->sort_by == 1) $query->orderBy(['price' => SORT_ASC]);
        if ($salefilter->sort_by == 2) $query->orderBy(['date_start' => SORT_DESC]);

        $query->all();


        // данное условие всплывает только если установлено что супер фильтр

        $all_query = $query->all();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);


        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages,
            'counts' => $countQuery->count(),
            'all_sales' => $all_query
        ];

        return $data;
    }

    /**
     * главный метод поиска исходя из фильтра
     */
    public function finalsearch($salefilter)
    {
        $QuerySale = $this->getQuery($salefilter);
        // это для отображения на карте
        // $all_query = $QuerySale->limit(80)->all();
        // if ($salefilter->moderated == 0) $QuerySale->limit(20)->all();
        $all_query = $QuerySale->all();

        $countQuery = clone $QuerySale;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);


        $rows = $QuerySale->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages,
            'counts' => $countQuery->count(),
            'all_sales' => $all_query
        ];

        return $data;

    }

    public function search2($salefilter)
    {
        $QuerySale = Sale::find()
            ->Where(['>=', 'date_start', (time() - $salefilter['period_ads'] * 86400)])
            ->andFilterWhere(['>=', 'price', $salefilter->price_down])
            ->andFilterWhere(['<=', 'price', $salefilter->price_up])
            ->andFilterWhere(['>=', 'floor', $salefilter->floor_down])
            ->andFilterWhere(['<=', 'floor', $salefilter->floor_up])
            ->andFilterWhere(['<=', 'year', $salefilter->year_up])
            ->andFilterWhere(['>=', 'year', $salefilter->year_down])
            ->andFilterWhere(['>=', 'floorcount', $salefilter->floorcount_down])
            ->andFilterWhere(['<=', 'floorcount', $salefilter->floorcount_up])
            ->andFilterWhere(['>=', 'grossarea', $salefilter->grossarea_down])
            ->andFilterWhere(['<=', 'grossarea', $salefilter->grossarea_up])
            ->andWhere(['not in', 'disactive', [1, 2]]);
        if (!empty($salefilter->rooms_count)) $QuerySale->andFilterWhere(['in', 'rooms_count', explode(",", $salefilter->rooms_count)]);
        if (!empty($salefilter->id_sources)) $QuerySale->andFilterWhere(['in', 'id_sources', explode(",", $salefilter->id_sources)]);
        if (!empty($salefilter->id_address)) $QuerySale->andFilterWhere(['in', 'id_address', explode(",", $salefilter->id_address)]);
        if (!empty($salefilter->house_type)) $QuerySale->andFilterWhere(['in', 'house_type', explode(",", $salefilter->house_type)]);
        if (!empty($salefilter->phone)) $QuerySale->andFilterWhere(['in', 'phone1', explode(",", $salefilter->phone)]);
        if (!empty($salefilter->black_list_id)) $QuerySale->andFilterWhere(['not in', 'phone1', explode(",", $salefilter->black_list_id)]);

        if ((!$salefilter->agents) and ($salefilter->housekeepers)) {
            $QuerySale->andFilterWhere(['status_blacklist2' => 1]);
        }
        if (($salefilter->agents) and (!$salefilter->housekeepers)) {
            $QuerySale->andFilterWhere(['status_blacklist2' => 0]);
        }

        $QuerySale->andFilterWhere(['or',
            ['address' => $salefilter->text_like],
            ['description' => $salefilter->text_like]]);
        if ($salefilter->not_last_floor) {
            $QuerySale->andWhere(['<>', 'floorcount', new Expression('floor')]);
        }
        if ($salefilter->discount != 0) {
            // если дискаунт установлен то делаем проверку на дискаунт
            $QuerySale->andWhere(['<', 'price', (100 - $salefilter->discount) / (100 * new Expression('average_price_same'))]);
        }
        if ($salefilter->sort_by == 1) $QuerySale->orderBy(['price' => SORT_ASC]);
        if ($salefilter->sort_by == 2) $QuerySale->orderBy(['date_start' => SORT_DESC]);

        // если пришел полигон то начинаем прочерку на нахаждение внутри полигона циклом php
        if ($salefilter->polygon_text != '') {
            $sales = $QuerySale->all();
            $ids = [];
            foreach ($sales as $sale) {
                if (!isPointInsidePolygon(json_decode(substr($salefilter->polygon_text, 1, -1)), [$sale->coords_x, $sale->coords_y])) continue;
                $ids[] = $sale->id;
            }
            $QuerySale = Sale::find()
                ->where(['in', 'id', $ids]);
        }

        // это для отображения на карте
        $all_query = $QuerySale->limit(50)->all();
        $countQuery = clone $QuerySale;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);


        $rows = $QuerySale->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages,
            'counts' => $countQuery->count(),
            'all_sales' => $all_query
        ];

        return $data;
    }

    protected function groupTags($tags)
    {
        if (empty(Yii::$app->cache->get('tags'))) {
            Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());

        }
        $all_tags = Yii::$app->cache->get('tags');


        $new_tags = [];
        // echo implode(",", $tags);
        foreach ($tags as $tag) {
            array_push($new_tags, $all_tags[$tag]);
        }
        //  my_var_dump($all_tags[20]);
        //  my_var_dump(\yii\helpers\ArrayHelper::getColumn($new_tags, 'id'));


        return array_group_by($new_tags, 'type');

    }

    // подключение связей
    protected function relations($QuerySale)
    {
        $QuerySale->from(['s' => Sale::tableName()]);
        // присоединяем связи
        $QuerySale->joinWith(['agent AS agent']);
        $QuerySale->joinWith(['addresses AS address']);
        $QuerySale->joinWith(['similarNew AS sim']);
    }


    /**
     * формирование запроса из фильтра
     */


    protected function getQuery($salefilter, $local = '', $exceptions = [])
    {
        if ($salefilter->regions) {
            $region = SaleFilters::findOne($salefilter->regions);
            $salefilter->polygon_text = $region->polygon_text;
        }
        $polygon_id_addresses = [];
        if ($salefilter->polygon_text) {
            if (empty(Yii::$app->cache->get('all_addesses'))) {
                Yii::$app->cache->set('all_addesses', Addresses::find()->select('id,coords_x,coords_y')->asArray()->all());

            }
            $all_addesses = Yii::$app->cache->get('all_addesses');
            foreach ($all_addesses as $address) {
                // echo $address['coords_x']." ".$address['coords_y'];
                if (!isPointInsidePolygon(json_decode(substr($salefilter->polygon_text, 1, -1)), [$address['coords_x'], $address['coords_y']])) continue;
                array_push($polygon_id_addresses, $address['id']);
            }

        }
        $this->polygon_text = $salefilter->polygon_text;
        if ($local == 'local') $QuerySale = Synchronization::find(); else $QuerySale = Sale::find();
        $QuerySale->from(['s' => Sale::tableName()]);
        // присоединяем связи
        $QuerySale->joinWith(['agent AS agent']);
        $QuerySale->joinWith(['addresses AS address']);
        $QuerySale->joinWith(['similarNew AS sim']);

        if ($salefilter['period_ads']) $QuerySale->Where(['>=', 's.date_start', (time() - $salefilter['period_ads'] * 86400)]);
        $QuerySale->andFilterWhere(['>=', 's.price', $salefilter->price_down])
            ->andFilterWhere(['<=', 's.price', $salefilter->price_up])
            ->andFilterWhere(['<=', 's.year', $salefilter->year_up])
            ->andFilterWhere(['>=', 's.year', $salefilter->year_down]);

        if ($salefilter->sale_disactive != 10) {// and ($salefilter->sale_disactive != 0)) $QuerySale->andWhere(['s.disactive' => $salefilter->sale_disactive]);
            switch ($salefilter->sale_disactive) {
                case 0:
                    $QuerySale->andWhere(['<>', 's.disactive', [1, 2]]);
                    //  $QuerySale->andWhere(['<>', 'sim.status', SaleSimilar::SOLD]);
                    break;
                case 4:
                    $QuerySale->andWhere(['<>', 's.disactive', [1, 2]]);
                    $QuerySale->andWhere(['<>', 'sim.status', SaleSimilar::SOLD]);
                    break;
            }
        }


        if ($salefilter->moderated) {
            //  info("MODERATED");
            $QuerySale->andfilterWhere(['sim.moderated' => $salefilter->moderated]);
        }
        if (!empty($salefilter->rooms_count) and (!in_array('s.rooms_count', $exceptions))) $QuerySale->andFilterWhere(['in', 's.rooms_count', $salefilter->rooms_count]);
        if (!empty($salefilter->id_sources)) $QuerySale->andFilterWhere(['in', 's.id_sources', $salefilter->id_sources]);

        // неактивные ресурсы
        if (!empty($salefilter->disactive_id_sources)) {
            info('SEARCHING DISACTIVE SOURCES');
            foreach ($salefilter->disactive_id_sources as $disactive_id_source) {
                $QuerySale->andFilterWhere(['not like', 'sim.id_sources', $disactive_id_source]);
            }

        }

        if (!empty($salefilter->polygon_text)) {
            $QuerySale->andFilterWhere(['in', 's.id_address', $polygon_id_addresses]);
        }
        if (($salefilter->minus_id_addresses)) $QuerySale->andFilterWhere(['not in', 's.id_address', explode(",", $salefilter->minus_id_addresses)]);
        if (!empty($salefilter->id_address)) $QuerySale->andFilterWhere(['in', 's.id_address', $salefilter->id_address]);
        if (!empty($salefilter->house_type)) $QuerySale->andFilterWhere(['in', 's.house_type', explode(",", $salefilter->house_type)]);
        if (!empty($salefilter->phone)) $QuerySale->andFilterWhere(['in', 's.phone1', explode(",", $salefilter->phone)]);

        if (($salefilter->floor_down != 0)) $QuerySale->andFilterWhere(['>=', 's.floor', $salefilter->floor_down]);
        if (($salefilter->floor_up != 0)) $QuerySale->andFilterWhere(['<=', 's.floor', $salefilter->floor_up]);
        if (($salefilter->floorcount_down != 0)) $QuerySale->andFilterWhere(['>=', 's.floorcount', $salefilter->floorcount_down]);
        if (($salefilter->floorcount_up != 0)) $QuerySale->andFilterWhere(['<=', 's.floorcount', $salefilter->floorcount_up]);
//        if (($salefilter->plus_tags)) {
//            $QuerySale->joinWith(['tags']);

        // если есть plus_tags_sale
        $tags_sale = $salefilter->plus_tags_sale();
        if ($tags_sale) {
            //  info('+TAGS_SALE');
            $grouped_tags_addresses = $this->groupTags($tags_sale);
            foreach ($grouped_tags_addresses as $grouped_tags_address) {
                $queryOR = ['or'];
                foreach ($grouped_tags_address as $item) {
                    array_push($queryOR, ['like', 'sim.tags_id', "," . $item['id'] . ","]);
                }
                $QuerySale->andWhere($queryOR);

            }

//            foreach ($tags_sale as $tag) {
//                $QuerySale->andWhere(['like', 's.tags_id', "," . $tag . ","]);
//            }
            // $QuerySale->andWhere(['in', 'tags.tag_id', $tags_sale]);
            // $QuerySale->having(new Expression("count(*)=" . count($tags_sale)));
        }
        // если есть plus_tags_address
        $tags_address = $salefilter->plus_tags_address();
// группироем по условиям OR и AND
        if ($tags_address) {
            $grouped_tags_addresses = $this->groupTags($tags_address);
            foreach ($grouped_tags_addresses as $grouped_tags_address) {
                $queryOR = ['or'];
                foreach ($grouped_tags_address as $item) {
                    array_push($queryOR, ['like', 'address.tags_id', "," . $item['id'] . ","]);
                }
                $QuerySale->andWhere($queryOR);

            }
            // my_var_dump($queryOR);
//            foreach ($tags_address as $tag) {
//
//                $QuerySale->andWhere(['like', 'address.tags_id', "," . $tag . ","]);
//            }


//            $query->andFilterWhere([
//                'or',
//                ['like', 'profiles.first_name', $this->userFullName],
//                ['like', 'profiles.last_name', $this->userFullName],
//            ]);
            // $QuerySale->andWhere(['in', 'tags.tag_id', $tags_sale]);
            // $QuerySale->having(new Expression("count(*)=" . count($tags_sale)));
        }

        $tags_sale = $salefilter->minus_tags_sale();
        if ($tags_sale) {
            foreach ($tags_sale as $tag) {
                $QuerySale->andWhere(['not like', 'sim.tags_id', "," . $tag . ","]);
            }
            // $QuerySale->andWhere(['in', 'tags.tag_id', $tags_sale]);
            // $QuerySale->having(new Expression("count(*)=" . count($tags_sale)));
        }
        // если есть plus_tags_address
        $tags_address = $salefilter->minus_tags_address();
        if ($tags_address) {
            foreach ($tags_address as $tag) {
                $QuerySale->andWhere(['not like', 'address.tags_id', "," . $tag . ","]);
            }
            // $QuerySale->andWhere(['in', 'tags.tag_id', $tags_sale]);
            // $QuerySale->having(new Expression("count(*)=" . count($tags_sale)));
        }
        // $QuerySale->groupBy("s.id");
//        }

        if ((!$salefilter->agents) and ($salefilter->housekeepers)) {
            $QuerySale->andFilterWhere(['agent.person_type' => 0]);
        }
        if (($salefilter->agents) and (!$salefilter->housekeepers)) {
            $QuerySale->andFilterWhere(['agent.person_type' => 1]);
        }

        if ($salefilter->text_like != '') $QuerySale->andFilterWhere(['or',
            ['like', 's.address', $salefilter->text_like],
            ['like', 's.description', $salefilter->text_like]]);
        if ($salefilter->not_last_floor) {
            $QuerySale->andWhere(['<>', 's.floorcount', new Expression('s.floor')]);
        }

        if ($salefilter->id) {
            $onControlsTemplate = $salefilter->getOnControls();
            //  $onBlacksTemplate = $salefilter->getOnBlacks();
            //
            $QuerySale->andFilterWhere(['not in', 's.id', $salefilter->white]);
            $QuerySale->andFilterWhere(['not in', 's.id', $salefilter->black]);
            $QuerySale->andFilterWhere(['not in', 's.id', $salefilter->similar_white]);
            $QuerySale->andFilterWhere(['not in', 's.id', $salefilter->similar_black]);
            //  $QuerySale->andFilterWhere(['not in', 's.id', $salefilter->check]);
            //   if (!empty($salefilter->check_list_id)) $QuerySale->andWhere(['not in', 's.id', explode(",", $salefilter->check_list_id)]);


        }

        if (($salefilter->discount != 0) or ($onControlsTemplate)) {

            $conditions = true;
        }

        // если пришел полигон то начинаем прочерку на нахаждение внутри полигона циклом php и дискаунта
        if ($conditions) {
            $QuerySale->groupBy("s.id");
            $sales = $QuerySale->all();
            $ids = [];
            foreach ($sales as $sale) {
                $continue = false;
                if ($salefilter->discount != 0) {
                    //  echo " идет проверка суперфилтра";
                    if ($sale->average_price == 0) continue;
                    elseif (($sale->price > (100 - $salefilter->discount) / 100 * $sale->average_price)) continue;
                }
                if ($onControlsTemplate) {
                    foreach ($onControlsTemplate as $template) {
                        if ($sale->IsInTemplate($template)) {
                            //  info("сработал смарт-фильтр ".$salefilter->renderOnControlTemplate($template). " ".$sale->renderLong_title());
                            $continue = true;
                            continue;
                        }
                    }
                }


                if ($continue) continue;
                //
                $ids[] = $sale->id;
            }
            $QuerySale = Sale::find();
            $QuerySale = $this->relations($QuerySale);
            if (count($ids) > 0) $QuerySale->where(['in', 's.id', $ids]);
        }

        if ($_GET['unique'] == 1) $QuerySale->groupBy('s.id_similar,s.phone1');
        elseif ($_GET['unique'] == 2) $QuerySale->groupBy('s.id_similar');
        else   $QuerySale->groupBy("s.id");

        $QuerySale = $this->sorting($QuerySale);

        return $QuerySale;
    }


    public function getQueryListsId($list)
    {
        $QuerySale = Sale::find();
        $QuerySale = $this->relations($QuerySale);
        if ($list) $QuerySale->where(['in', 's.id', explode(',', $list)]);
        return $QuerySale;
    }

// уникализация
    public function uniquing($QuerySale)
    {
        switch ($_GET['unique']) {
            case SaleFilters::UNIQUE_MAIN :
                break;

            case SaleFilters::UNIQUE_ROW:
                $QuerySale->groupBy('s.id_similar,s.phone1');
                break;

            case SaleFilters::UNIQUE_OBJECT:
                $QuerySale->groupBy('s.id_similar');
                break;

        }
        return $QuerySale;
    }

    public function sorting($QuerySale)
    {
        switch ($_GET['sort_by']) {
            case SaleFilters::SORTING_ID:
                $QuerySale->orderBy(['s.id' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_PRICE_ASC:
                $QuerySale->orderBy(['s.price' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_PRICE_DESC:
                $QuerySale->orderBy(['s.price' => SORT_DESC]);
                break;
            case SaleFilters::SORTING_DATE_START_ASC:
                $QuerySale->orderBy(['s.date_start' => SORT_DESC]);
                break;

            case SaleFilters::SORTING_DATE_START_DESC:
                $QuerySale->orderBy(['s.date_start' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_ID_ADDRESS_ASC:
                $QuerySale->orderBy(['s.id_address' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_ID_ADDRESS_DESC:
                $QuerySale->orderBy(['s.id_address' => SORT_DESC]);
                break;


        }
        return $QuerySale;

    }

    public
    function search_without_pagination($salefilter)
    {

        $QuerySale = $this->getQuery($salefilter);


        $data = [
            'all_sales' => $QuerySale->all()
        ];

        return $data;
    }


    public
    function search_sale_list($salelist)
    {

        $query = Sale::find()->where(['in', 'id', explode(",", $salelist->list_of_ids)]);
        if ($salelist->sale_disactive != 0) $query->andWhere(['disactive' => $salelist->disactive]);
        if (($_GET['sort_by'] == 1) or (!isset($_GET['sort_by']))) $query->orderBy(['price' => SORT_ASC]);
        if ($_GET['sort_by'] == 2) $query->orderBy(['date_start' => SORT_DESC]);
        if ($_GET['sort_by'] == 3) $query->orderBy(['id_address' => SORT_ASC]);

        if ($_GET['moderated_status'] == 1) {
            $query->andWhere(['not in', 'id', explode(",", $salelist->ids_ok)]);
            $query->andWhere(['not in', 'id', explode(",", $salelist->ids_ban)]);
        }
        if ($_GET['moderated_status'] == 2) {
            $query->andWhere(['in', 'id', explode(",", $salelist->ids_ok)]);

        }
        if ($_GET['moderated_status'] == 3) {
            $query->andWhere(['in', 'id', explode(",", $salelist->ids_ban)]);
        }

        if ($_GET['unique'] == 1) $query->groupBy('floor,id_address,phone1');
        if ($_GET['unique'] == 2) $query->groupBy('floor,id_address');
        $_SESSION['coords_x'] = $query->average('coords_x');
        $_SESSION['coords_y'] = $query->average('coords_y');
        // echo  $_SESSION['coords_x']." -> ". $_SESSION['coords_y'];

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);


        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;
    }

    public
    function search_sale_id($id_lists)
    {

        $query = Sale::find()->where(['in', 'id', $id_lists]);


        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);


        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;
    }

    public
    function getQueryOnControls($salefilter)
    {
        $Queries = \app\models\Sale::find()->where('1=0');
        $onControlsTemplate = $salefilter->getOnControls();
        if ($onControlsTemplate) {
            foreach ($onControlsTemplate as $template) {
                $query = \app\models\Sale::find()
                    ->andwhere(['rooms_count' => $template->rooms_count])
                    ->andwhere(['id_address' => $template->id_address])
                    ->andwhere(['floor' => $template->floor])
                    ->andwhere(['between', 'grossarea',
                        $template->grossarea * (100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100,
                        $template->grossarea * (100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100])
                    ->andwhere(['>=', 'price', $template->price])
                    ->limit(1);

                $Queries->union($query);

            }
        }

        // info($Queries);
        return $Queries;
    }

    public
    function search_list($type_of_show, $salefilter)
    {

        if ($type_of_show == '1') {
            $list = $salefilter->black_list_id;
            $query = $salefilter->getOnBlacksQuery();
        }
        if ($type_of_show == '2') {
            $list = $salefilter->white_list_id;
            $query = $this->getQueryListsId($list);
        }
        if ($type_of_show == '3') {

            // $query = $this->getQueryOnControls($salefilter);
            $query = $salefilter->getOnControlsQuery();
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public
    function web_search($salefilter, $limits = 10)
    {
        $list = $salefilter->white;
        $query = Sale::find();
        $query->from(['s' => Sale::tableName()]);
        $query->joinWith(['addresses AS address']);
        $query->where(['in', 's.id', $list]);
        $query->orderBy(['s.price' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limits,
            ],

        ]);

        return $dataProvider;
    }

    public
    function search_dataprovider($salefilter, $limits = 20)
    {
        // add conditions that should always apply here


        $query = $this->getQuery($salefilter);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limits,
            ],
        ]);

        return $dataProvider;
    }

    public
    function search_for_map($salefilter)
    {
        $query = $this->getQuery($salefilter);
        return $query->limit(50)->all();
    }

    public
    function search_investigation($salefilter, $exceptions = [])
    {
        // add conditions that should always apply here


        $query = $this->getQuery($salefilter, 'local', $exceptions);


        return $query;
    }
}
