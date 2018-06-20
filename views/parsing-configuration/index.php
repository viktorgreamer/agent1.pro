<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Actions;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ParsingConfigurationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parsing Configurations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parsing-configuration-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            //  ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'label' => 'Source',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\Sale::ID_SOURCES[$model->id_sources];
                }
            ], [
                'label' => 'Город',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->module;
                }
            ],
            [
                'label' => 'Urls',
                'format' => 'raw',
                'value' => function ($model) {
                    return " <a href='" . $model->start_link . "' target = '_blank'> Start</a>
                             <hr> <a href='" . $model->non_start_link . "' target = '_blank'>Non Start</a>";
                }
            ],
            'last_ip',
            'success_stop',
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\ParsingConfiguration::STATUS[$model->active];
                }
            ],
            'last_timestamp:datetime',
            'last_timestamp_new:datetime',

            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['parsing-configuration/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                    'activate' => function ($url, $model) {
                       return Actions::renderChangeStatus($model->id, Actions::PARSING_CONFIGURATION, Actions::PARSING_CONFIGURATION_ACTIVE,
                            Actions::PARSING_CONFIGURATION_ACTIVE_ACTIVATE ,
                            Actions::getIcons(Actions::PARSING_CONFIGURATION,Actions::PARSING_CONFIGURATION_ACTIVE_ACTIVATE));
                    },
                    'disactivate' => function ($url, $model) {
                       return Actions::renderChangeStatus($model->id, Actions::PARSING_CONFIGURATION, Actions::PARSING_CONFIGURATION_ACTIVE,
                            Actions::PARSING_CONFIGURATION_ACTIVE_DISACTIVATE ,
                            Actions::getIcons(Actions::PARSING_CONFIGURATION,Actions::PARSING_CONFIGURATION_ACTIVE_DISACTIVATE));
                    },
                ],
                'template' => '{update}{activate}{disactivate}',
            ]

        ],
    ]); ?>
</div>
