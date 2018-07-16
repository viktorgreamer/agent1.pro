<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\components\YandexMaps;
use yii\bootstrap\Button;
use yii\bootstrap\Collapse;
Use app\models\Tags;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AddressesSearch */
/* @var $model app\models\Addresses */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addresses-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'addresses' => $dataProvider->getModels()]); ?>
    <? echo Html::a(ICON_EDIT, ['create'], ['class' => CLASS_BUTTON]) ?>
    <div class="row">

        <?php
        $session = Yii::$app->session;
        $module = $session->get('module'); ?>

                        <?= \app\components\YandexMaps::widget([
                            'addresses' => $dataProvider->getModels(), // передаем addresses
                            'module' => $module, // передаем текущий модуль (город в котором работаем, чтобы получить координаты центра и zoom по умолчанию
                            'polygon' => $searchModel->polygon_text,
                            'isEditablePolygon' => true // добавление кнопок для редактирования полигона
                        ]); ?>



        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //  'filterModel' => $searchModel,
            'emptyText' => '',
            'columns' => [
                // ['class' => 'yii\grid\SerialColumn'],

                'id',
                // 'coords_x',
                // 'coords_y',
                'address',
                //  'locality',
                // 'AdministrativeAreaName',
                // 'street',
                // 'house',
                // 'hull',
                // 'district',
               // 'floorcount',
                [
                    'label' => '',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->floorcount." ".$model->RenderHouseType()."<br> ".\app\models\Addresses::mapBalcon()[$model->balcon];
                    }
                ], [
                    'label' => 'Теги',
                    'format' => 'raw',
                    'value' => function ($model) {
            $body = " <a id=\"#AddTagsAddress".$model->id."\" type=\"button\"
                       data-toggle=\"modal\"
                       data-target=\"#TagsModal".$model->id."\"> <i
                                class=\"fa fa-tags green-text fa-2x\" aria-hidden=\"true\"></i>
                    </a>
                    <div class=\"modal fade\" id=\"TagsModal".$model->id."\" tabindex=\"-1\"
                         role=\"dialog\"
                         aria-labelledby=\"myModalLabel\" aria-hidden=\"true\">
                        <div class=\"modal-dialog modal-lg\" role=\"document\">
                            <div class=\"modal-content\">
                                <div class=\"modal-body\">
                                    ".$this->render('//tags/quick-add-form-alternative', [
                                        'parent_id' => $model->id,
                                        'realtags' => $model->tags,
                                        'type' => 'address',
                                        'id_address' => true
                                    ])."</div>
                            </div>
                        </div>
                    </div>";
                        return $body;
                    }
                ],
               /* [
                    'label' => 'pattern',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->pattern;
                    }
                ],*/
                [
                    'label' => 'Коор-ты',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $customurl = "https://yandex.ru/maps/?mode=search&text=" . $model->coords_x . ", " . $model->coords_y; //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
                            ['title' => Yii::t('yii', 'yandex maps'),
                                'data-pjax' => '0',
                                'target' => '_blank']);

                    }
                ],
                [
                    'label' => 'tags',
                    'format' => 'raw',
                    'value' => function ($model) {

                        return  Tags::render($model->tags);
                    }
                ],

                // 'address_string_variants:ntext',
                'year',
                'precision_yandex',
                'status',
                [
                    'label' => 'Ссылка',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a(
                            'Перейти',
                            $model->url,
                            [
                                'title' => 'Смелей вперед!',
                                'target' => '_blank'
                            ]
                        );
                    }
                ],



                [
                    'class' => \yii\grid\ActionColumn::className(),
                    'buttons' => [
                        'fix' => function ($url, $model, $key) {
                            return "<a href='#' class ='address-ajax-fix'  data-id=$model->id ><i class ='fa fa-check fa-2x ' > </i></a> ";
                        },
                        'not-living' => function ($url, $model, $key) {
                            return "<a href='#' class ='address-ajax-not-living' title='Нежилой объъект' data-id=$model->id ><i class='fa fa-university fa-2x' aria-hidden='true'></i></a> ";
                        },
                        'update' => function ($url, $model) {
                            $customurl = Yii::$app->getUrlManager()->createUrl(['addresses/update', 'id' => $model['id']]); //$model->id для AR
                            return \yii\helpers\Html::a('<i class="fa fa-pencil fa-2x" aria-hidden="true"></i>', $customurl,
                                ['title' => Yii::t('yii', 'Fix'),
                                    'data-pjax' => '0',
                                    'target' => '_blank']);
                        },
                        'view' => function ($url, $model) {
                            $customurl = Yii::$app->getUrlManager()->createUrl(['addresses/view', 'id' => $model['id']]); //$model->id для AR
                            return \yii\helpers\Html::a("<i class=\"fa fa-eye fa-2x\" aria-hidden=\"true\"></i>", $customurl,
                                ['title' => Yii::t('yii', 'Fix'),
                                    'data-pjax' => '0',
                                    'target' => '_blank']);
                        },
                        'show-yandex-map' => function ($url, $model) {
                            $customurl = "https://yandex.ru/maps/?mode=search&text=" . $model['locality'] . ", " . $model['address']; //$model->id для AR
                            return \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
                                ['title' => Yii::t('yii', 'yandex maps'),
                                    'data-pjax' => '0',
                                    'target' => '_blank']);
                        },
                        'delete' => function ($url, $model, $key) {
                            return "<a  class ='change-statuses' title='Удалить' data-id='$model->id' data-model='address' data-attrname='delete' data-value='0'><i class='fa fa-minus text-danger fa-2x' aria-hidden='true'></i></a> ";
                        },

                        'template' => '{view}{fix}  {update} {show-yandex-map}{not-living}{delete}',
                    ]


                ],
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered'
            ],

        ]); ?>
    </div>
</div>
