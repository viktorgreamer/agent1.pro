<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Actions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tags';
$this->params['breadcrumbs'][] = $this->title;


?>


<div class="tags-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'название',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<span class=\"badge badge-" . $model->color . "\">#" . $model->name . "</span>";
                }
            ],

            [
                'label' => 'parent',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->global_parent == 0) return "Объект"; else return "Клиент";
                }
            ],
            [
                'label' => 'a_type',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\Tags::A_TYPES[$model->a_type];
                }
            ],
            'locality',
            [
                'label' => 'type',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\Tags::TYPES_ARRAY[$model->type];
                }
            ],
            [
                'label' => 'Parent_tag',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->id_parent) return \app\models\Tags::TYPES_ARRAY[$model->type];
                }
            ],
            [
                'label' => 'Публичность',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\Tags::PUBLIC_ARRAY[$model->publish];
                }
            ], [
                'label' => 'SEAHCABLE',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->searchable) return span(Actions::renderChangeStatus($model->id, Actions::TAGS, Actions::TAGS_SEARCHABLE, Actions::TAGS_SEARCHABLE_FALSE, ICON_NOSEARCH), SUCCESS);
                    else  return span(Actions::renderChangeStatus($model->id, Actions::TAGS, Actions::TAGS_SEARCHABLE, Actions::TAGS_SEARCHABLE_TRUE, ICON_SEARCH), DANGER);;
                }
            ],
            // 'color',
            'komment',

            // ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['tags/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                    'delete' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['tags/delete', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                ],
                'template' => '{update}{delete}',
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
