<?php


use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

    echo  ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_sale-table',

        'layout' => "{summary}\n{items}\n{pager}",
        'summary' => 'Показано {count} из {totalCount} предложений',
        'summaryOptions' => [
            'tag' => 'span',
            'class' => 'badge red'
        ],
        'viewParams' => ['controls' => true, 'salefilter' => $salefilter],

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




