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
    <div class="row">
        <?php

        // echo YandexMaps::widget(['addresses' => $dataProvider->getModels()
        // ]);
        ?>


        <p>
            <? echo Html::a('Create Addresses', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </p>
        <?php Pjax::begin(); ?>    <?= GridView::widget([
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
                'floorcount',
                [
                    'label' => 'материал стен',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->RenderHouseType();
                    }
                ],
                [
                    'label' => 'pattern',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->pattern;
                    }
                ],
                [
                    'label' => 'Координаты',
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
                        $Realtags = explode(',', $model->tags_id);
                        $tags = '';
                        foreach ($Realtags as $realtag) {
                            $tag = Tags::findOne($realtag);
                            $tags .= " <span class=\"badge badge-" . $tag->color . "\">#" . $tag->name . "</span>";
                        }
                        return $tags;
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
        <?php Pjax::end(); ?>
    </div>
</div>
