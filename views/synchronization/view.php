<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Synchronization */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Synchronizations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="synchronization-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
                     'id',
            [
                'label' => 'Источник',
                'format' => 'raw',
                'value' => function ($model) {
                    return " <a href='" . $model->url . "' target='_blank'> " . \app\models\Sale::ID_SOURCES[$model->id_sources] . "</a> <br> id=" . $model->id_in_source;
                }
            ],

            [
                'label' => 'время',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<i class=\"fa fa-hourglass-start\" aria-hidden=\"true\"></i>" . date("d.m.y H:i:s", $model->date_start) .
                        "<br> <i class=\"fa fa-check\" aria-hidden=\"true\"></i>" . date("d.m.y H:i:s", $model->date_of_check)
                        . "<br> <i class=\"fa fa-trash-o\" aria-hidden=\"true\"></i>" . date("d.m.y H:i:s", $model->date_of_die);
                }
            ],
            [
                'label' => 'addresses',
                'format' => 'raw',
                'value' => function ($model) {
                    $area_string = $model->grossarea;
                    if ($model->living_area != 0) $area_string .= "/" . $model->living_area;
                    if ($model->kitchen_area != 0) $area_string .= "/" . $model->kitchen_area;
                    $area_string .= "м2";
                    if ($model->id_address == null) $id_address = 'no id_address';
                    else {
                        $id_address = "<br>" . \yii\helpers\Html::a("id_address=" . $model->id_address,
                                Yii::$app->getUrlManager()->createUrl(['addresses/view', 'id' => $model->id_address]),
                                ['title' => Yii::t('yii', 'Fix'),
                                    'data-pjax' => '0',
                                    'target' => '_blank']);
                    }
                    return $model->title . "<br>" . $area_string . "<br>" . $model->address_line . "<br>" . $model->address . $id_address . " year=" . $model->year . "<br>" .
                        \app\models\Renders::Price($model->price). $model->renderPhone();


                }
            ],
            [
                'label' => 'Координаты',
                'format' => 'raw',
                'value' => function ($model) {
                    $customurl = "https://yandex.ru/maps/?mode=search&text=" . $model->coords_x.", ".$model->coords_y; //$model->id для AR
                    return \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
                        ['title' => Yii::t('yii', 'yandex maps'),
                            'data-pjax' => '0',
                            'target' => '_blank']);

                }
            ],
            [
                'label' => 'статус',
                'format' => 'raw',
                'value' => function ($model) {
                    return " STATUS: " . \app\models\Sale::DISACTIVE_CONDITIONS_ARRAY[$model->disactive] .
                        " <hr style=\"margin-top: 0px; margin-bottom: 0px;\">parsed: " . \app\models\Sale::TYPE_OF_PARSED[$model->parsed] .
                        " <hr style=\"margin-top: 0px; margin-bottom: 0px;\">geocodated: " . \app\models\Geocodetion::GEOCODATED_STATUS_ARRAY[$model->geocodated] .
                        " <hr style=\"margin-top: 0px; margin-bottom: 0px;\">processed: " . \app\models\Sale::TYPE_OF_PROCCESSED[$model->processed] .
                        " <hr style=\"margin-top: 0px; margin-bottom: 0px;\">load_analized: " . \app\models\Sale::TYPE_OF_ANALIZED[$model->load_analized] .
                        " <hr style=\"margin-top: 0px; margin-bottom: 0px;\">sync: " . \app\models\Sale::TYPE_OF_SYNC[$model->sync];

                }
            ],


            [
                'label' => 'log',
                'format' => 'raw',
                'value' => function ($model) {

                    return $model->RenderProcessingLog();

                }
            ],
 [
                'label' => 'log',
                'format' => 'raw',
                'value' => function ($model) {

                    return $model->RenderLog();

                }
            ],

            [
                'label' => "admin",
                'format' => 'raw',
                'value' => function ($model) {
                    $buttons = '';
                    $buttons .= "<a class='change-status' data-status_name='geocodated' data-id_item=" . $model->id . "><i class='fa fa-map-marker fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-status' data-status_name='load_analized' data-id_item=" . $model->id . "><i class='fa fa-area-chart fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-status' data-status_name='parsed' data-id_item=" . $model->id . "><i class='fa fa-clone fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-status' data-status_name='processed' data-id_item=" . $model->id . "><i class='fa fa-spinner fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-status' data-status_name='sync' data-id_item=" . $model->id . "><i class='fa fa-link fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-statuses' data-model='sync' data-value='6' data-attrname='disactive' data-id=" . $model->id . "><i class='fa fa-play fa-2x' aria-hidden='true'></i></a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\">".\app\models\Actions::renderChangeStatus($model->id,\app\models\Actions::SYNC, \app\models\Actions::SALE_PROCESSED,2,'PROCESSED');
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-statuses' data-model='sync' data-value='0' data-attrname='id_similar' data-id=" . $model->id . ">SIMILAR</a>";
                    $buttons .= "<hr style=\"margin-top: 0px; margin-bottom: 0px;\"><a class='change-statuses' data-model='sync' data-value='' data-attrname='phone1' data-id=" . $model->id . "><i class='fa fa-phone fa-2x' aria-hidden='true'></i></a>";

                    return $buttons;
                }
            ],
        ],
    ]) ?>

</div>
