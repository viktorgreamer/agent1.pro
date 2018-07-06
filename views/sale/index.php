<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use app\models\SaleFilters;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales');
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->controller->action->id == 'search-by-filter') $this->title = "Поиск по фильру " . $salefilter->name;   ?>


<div class="sale-index">
    <? if (Yii::$app->controller->action->id == 'search-by-filter') {
        echo $this->render('_search_by_filter', ['salefilter' => $salefilter]);
    } else echo $this->render('_sale_search_grid', ['salefilter' => $salefilter]); ?>

    <? if ($_GET['view'] == SaleFilters::VIEW_MAP) {
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' =>
                    [
                        'pagesize' => 100]
            ]);
        echo $this->render('_map',
            [
                'sales' => $dataProvider->getModels(),
                'salefilter' => $salefilter
            ]);
    } else {
        echo $this->render('_list_view',
            [
                'dataProvider' => new ActiveDataProvider(['query' => $query]),
                'salefilter' => $salefilter
            ]);

    }

    ?>


</div>


