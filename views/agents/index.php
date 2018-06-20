<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Agents */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agents-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search'); ?>

    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'label' => 'варианты имени',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status == 1) return $model->getVariants_of_person();
                    else return '';
                }
            ],
            'phone',
            'person',
            // 'date',
            // 'type',
            'avito_count',
            'irr_count',
            'yandex_count',
            'cian_count',
            'youla_count',
            //'status',
            'count_ads',

            [
                'label' => 'person_type',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->person_type == 0) return span(\app\models\Agents::PERSON_TYPE[$model->person_type], 'primary');
                    if ($model->person_type == 1) return span(\app\models\Agents::PERSON_TYPE[$model->person_type], 'danger');
                }
            ],

            // ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
            'linkOptions' => ['class' => 'page-link'],
            'firstPageCssClass' => 'page-item first',
            'prevPageCssClass' => 'page-item last',
            'nextPageCssClass' => 'page-item next',
            'activePageCssClass' => 'page-item active',
            'disabledPageCssClass' => 'page-item invisible'
        ],
    ]); ?>
    <?php Pjax::end(); ?></div>
