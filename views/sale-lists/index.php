<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sale Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-lists-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_form1', ['model' => $searchModel]); ?>


    <?= GridView::widget(['dataProvider' => $dataProvider,
        'columns' => [// ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Агент',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\User::getFullName($model->user_id);
                }
            ],
            [
                'label' => 'название',
                'format' => 'raw',
                'value' => function ($model) {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['sale-lists/search-by', 'id' => $model['id']]); //$model->id для AR
                    return \yii\helpers\Html::a($model->name, $customurl,
                        ['title' => Yii::t('yii', 'поиск по списку'),
                            'target' => '_blank']);
                }
            ],
            [
                'label' => 'web',
                'format' => 'raw',
                'value' => function ($model) {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['web/search-by', 'id' => $model['id']]); //$model->id для AR
                    return \yii\helpers\Html::a('<i class="fa fa-share"></i>', $customurl,
                        ['title' => Yii::t('yii', 'Просмотр публичный'),
                            'target' => '_blank']);
                }
            ],
            [
                'label' => 'same lists',
                'format' => 'raw',
                'value' => function ($model) {
                    $salelists = $model->getLocalityLists();
                    $body = '';
                    if ($salelists) {
                        foreach ($salelists as $salelist) {
                            $body .= "<span class='badge badge-success'>" . $salelist->name . "</span><br>";
                        }
                    } else $body = 'nothing';


                    return $body;
                }
            ],
            [
                'label' => 'recommended lists',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->similar_lists) {
                        $ids = explode(",", $model->similar_lists);
                        $salelists = \app\models\SaleLists::find()->where(['in', 'id', $ids])->all();

                        $body = '';
                        foreach ($salelists as $salelist) {
                            $body .= "<span class='badge badge-info'>" . $salelist->name . "</span><br>";
                        }
                    } else $body = 'nothing';
                    return $body;
                }
            ],
            [
                'label' => 'tags',
                'format' => 'raw',
                'value' => function ($model) {

                    return \app\components\TagsWidgets::widget(['salelist_tags_id' => $model->tags]);
                }
            ],
            [
                'label' => 'тип',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\SaleFilters::TYPE_OF_FILTERS_ARRAY[$model->type];
                }
            ],

            [
                'label' => 'Кол-во',
                'format' => 'raw',
                'value' => function ($model) {

                    return count(explode(",", $model->list_of_ids));
                }
            ],
            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['sale-lists/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>update<br>', $customurl, ['target' => '_blank']);
                    },
                    'delete' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['sale-lists/delete', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-trash" aria-hidden="true"></i>delete', $customurl, ['target' => '_blank']);
                    },
                ],
                'template' => '{update}{delete}',
            ]

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
