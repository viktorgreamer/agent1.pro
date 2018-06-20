<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sale;
use app\models\SaleFilters;

use yii\data\Pagination;
use yii\db\Expression;
use yii\db\ActiveQuery;

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
    public function search($salefilter_input, $mail_inform = false, $count_days)
    {

        $salefilter = new SaleFilters();
        $salefilter = $salefilter_input;
        $query = Sale::find();

        $query->andfilterWhere(['rooms_count' => $salefilter->rooms_count]);
        
        $query->andFilterWhere(['>=', 'price', $salefilter->price_down]);

        $query->andFilterWhere(['<=', 'price', $salefilter->price_up]);

        $query->andFilterWhere(['>=','floor', $salefilter->floor_down]);
        $query->andFilterWhere(['<=','floor', $salefilter->floor_up]);

        $query->andFilterWhere(['>=','floorcount', $salefilter->floorcount_down]);
        $query->andFilterWhere(['<=','floorcount', $salefilter->floorcount_up]);

       
        $query->andFilterWhere(['<>', 'floorcount', new Expression('floor')]);

        $query->andFilterWhere(['>=','grossarea', $salefilter->grossarea_down]);
        $query->andFilterWhere(['<=','grossarea', $salefilter->grossarea_up]);

        $query->andFilterWhere(['not in', 'id', explode(",",$salefilter->black_list_id)]);

        if ($mail_inform === false ) $query->andFilterWhere(['>=', 'date_start', (time() - $salefilter['period_ads'] * 86400)]);

        else $query->andFilterWhere(['>=', 'date_start', (time() - $count_days*86400)]); // рисылает где время обновления меньше 10 минут


        // add conditions that should always apply here


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

    public function search_sale_list($salelist)
    {

                 $query = Sale::find()->where(['in', 'id', explode(",", $salelist->list_of_ids)]);


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
}
