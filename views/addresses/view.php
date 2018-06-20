<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use app\components\YandexMaps;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $model app\models\Addresses */

$this->title = $model->address;
$this->params['breadcrumbs'][] = ['label' => 'Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="addresses-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <!--        --><? //= Html::a('Delete', ['delete', 'id' => $model->id], [
        //            'class' => 'btn btn-danger',
        //            'data' => [
        //                'confirm' => 'Are you sure you want to delete this item?',
        //                'method' => 'post',
        //            ],
        //        ]) ?>
    </p>
    <?php
    // echo YandexMaps::widget(['addresses' => $address]);
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            //  'coords_x',
            // 'coords_y',
            'address',
             'street',
             'house',
             'hull',
            'AdministrativeAreaName',
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
                    $customurl = "https://yandex.ru/maps/?mode=search&text=" . $model->coords_x.", ".$model->coords_y; //$model->id для AR
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

                    return \app\components\TagsWidgets::widget(['tags' => $model->tags]);
                }
            ],
            'locality',
            'district',
            [
                'label' => 'материал стен',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->RenderHouseType();
                }
            ],
            'floorcount',
            //  'address_string_variants:ntext',
            'year',
             'precision_yandex',
        ],
    ]) ?>
    <?php

    ?>

    <button id="#AddTags" type="button" class="btn btn-primary btn-xs"
            data-toggle="modal"
            data-target="#TagsModal"> +tags
    </button>
    ";


    <div class="modal fade" id="TagsModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">
                <div class="modal-body">


                    <? echo $this->render('//tags/quick-add-form-address', [
                        'id' => $model->id,
                    ]);

                    ?>




                </div>
            </div>
        </div>
    </div>


</div>
