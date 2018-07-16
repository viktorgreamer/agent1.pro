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
    <?php echo $this->render('_search', ['salefilter' => $salefilter]); ?>


    <?= ListView::widget([
        'dataProvider' =>  new ActiveDataProvider(['query' => $query]),
        'itemView' => '_moderate_view',

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

<?
$script = <<<JS


JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);