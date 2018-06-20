<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Control;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Controls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="control-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <? // Html::a(Yii::t('app', 'Create Control'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
           // 'fed_okrug',
            'region',
           // 'type_of_region',
            'region_rus',
            // 'oblast_rus',
            // 'max_step',

            [
                'label' => 'parsing_status',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status == 2) return "ACTIVE";
                }
            ],
            // 'is_analized',
            // 'coords_x',
            // 'coords_y',
            // 'zoom',
            // 'log',
            // 'info_array:ntext',
             'last_timestamp:datetime',
             'last_check_of_die:datetime',
             'last_check_of_lost:datetime',

            [
                'label' => 'parsing_status',
                'format' => 'raw',
                'value' => function ($model) {
                   return Control::STATUSES[$model->parsing_status];
                }
            ],
             'last_check_controlparsing:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
