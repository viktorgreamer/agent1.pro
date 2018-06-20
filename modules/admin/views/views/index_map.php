<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sales');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sale-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <? if (Yii::$app->controller->action->id == 'search-by-filter') {
        echo $this->render('_search_by_filter', ['salefilter' => $salefilter]);
    } else echo $this->render('_search', ['salefilter' => $salefilter]); ?>


    <?= $this->render('_map', ['sales' => $sales, 'salefilter' => $salefilter]); ?>

<!--    --><?// foreach ($sales as $sale) {
//        echo $this->render('_sale-table', ['sales' => $sales, 'salefilter' => $salefilter, 'controls' => true]);
//
//    }

    ?>


</div>


