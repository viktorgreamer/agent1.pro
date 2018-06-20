<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ErrorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Errors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="errors-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create', ['create'], ['class' => CLASS_BUTTON_SUCCESS]) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'fatality',
            'name',

            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['errors/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil fa-2x" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },

                ],
                'template' => '{update}',
            ]
        ],
    ]); ?>
</div>
