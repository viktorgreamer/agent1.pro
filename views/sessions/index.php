<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Sessions;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel app\models\SessionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sessions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sessions-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {

            return ['class' => Sessions::getColors($model->status)];

        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_session',
            'current_url:ntext',
            'datetime_start:datetime',
            'datetime_check:datetime',
            'status',
            'id_server',
            'ip',

            [
                'label' => 'Buttons',
                'format' => 'raw',
                'value' => function ($model) {
                    $buttons = Html::a("УДАЛИТЬ", Url::to(['sessions/delete', 'id' => $model->id]), ['target' => '_blank']);

                    return $buttons;
                }
            ],

        ],
    ]); ?>
</div>
