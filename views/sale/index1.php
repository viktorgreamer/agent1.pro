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


    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_sale-table',

        'layout' => "{summary}\n{items}\n{pager}",
        'summary' => 'Показано {count} из {totalCount} предложений',
        'summaryOptions' => [
            'tag' => 'span',
            'class' => 'badge red'
        ],
        'viewParams' => ['controls' => true],

        'emptyText' => '<p>Нет вариантов</p>',
        'emptyTextOptions' => [
            'tag' => 'p'
        ],

        'pager' => [
            'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
            'linkOptions' => ['class' => 'page-link'],
            'firstPageCssClass' => 'page-item first',
            'prevPageCssClass' => 'page-item last',
            'nextPageCssClass' => 'page-item next',
            'activePageCssClass' => 'page-item active',
            'disabledPageCssClass' => 'page-item invisible'
        ],
    ]) ?>



</div>


