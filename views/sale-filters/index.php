<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Tags;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleFiltersSearch2 */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sale Filters';
$this->params['breadcrumbs'][] = $this->title;
$session = Yii::$app->session;
$user_full_name = $session->get('first_name') . " " . $session->get('last_name');
?>
<div class="sale-filters-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <? // echo Html::a('Create Sale Filters', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <!--  --><?= GridView::widget(['dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             'id',
            [
                'label' => 'Агент',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = \app\models\User::findOne($model->user_id);
                    return $user->fullname;
                }
            ],
            ['label' => 'Тип Объекта',
                'format' => 'raw',
                'value' => function ($model) {
                    {
                        $body = '';
                        if ($model->rooms_count) {
                            foreach ($model->rooms_count as $item) {
                                if ($model->rooms_count[0] != $item) $body .= ", ";
                                $body .= \app\models\Sale::ROOMS_COUNT_ARRAY[$item];
                            }

                        } else $body = 'Все';
                    }

                    return "<h4>" . $body . "</h4>";
                }],
            [
                'label' => 'название',
                'format' => 'raw',
                'value' => function ($model) {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['sale/search-by-filter', 'id' => $model['id']]); //$model->id для AR
                    return \yii\helpers\Html::a($model->name, $customurl,
                        ['title' => Yii::t('yii', 'поиск по фильтру'),
                            'target' => '_blank']);
                }
            ],

            [
                'label' => 'Ценою',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<h6>" . \app\models\RenderSalefilters::PricesCoridor('', $model) . "</h6>";
                }
            ],
            [
                'label' => 'Дополнительно',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<h6>" . \app\models\RenderSalefilters::SquareCoridor('Площадью:', $model) . "</h6>"
                        . \app\models\RenderSalefilters::YearsCoridor('', $model) . " "
                        . \app\models\RenderSalefilters::FloorsCoridor('', $model) . " "
                        . \app\models\RenderSalefilters::FloorCountsCoridor('Этаностью:', $model) . "<br>"
                        . \app\models\RenderSalefilters::HouseType('тип дома: ', $model);

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
                'label' => 'tags',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\components\TagsWidgets::widget(['salefilter_tags_id' => $model->tags]);
                }
            ],

            // 'komment:ntext',
            'plus_tags',

            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['sale/index2', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil fa-2x" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },

                    'delete' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['sale-filters/delete', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-trash fa-2x" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                    'load' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['sale-filters/download-photo-and-resize', 'id' => $model['id']]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-download fa-2x" aria-hidden="true"></i>', $customurl,
                            ['target' => '_blank']);
                    },
                ],
                'template' => '{update}{delete}{load}',
            ]
        ],
    ]);
    ?>

    <?php Pjax::end(); ?></div>
