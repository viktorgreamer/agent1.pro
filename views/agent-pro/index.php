<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AgentProSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agent Pros';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agent-pro-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Agent Pro', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'status',
            'id_error',
            'time:datetime',
            'status_parsingsync',
             'status_detailed_parsing',
             'status_processing',
             'status_parsing_avito_phones',
             'status_analizing',
             'status_parsing_new',
             'status_sync',
             'status_geocogetion',
            'id_sources',

            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['agent-pro/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil fa-2x" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },

                ],
                'template' => '{update}',
            ]
        ],
    ]); ?>
</div>
