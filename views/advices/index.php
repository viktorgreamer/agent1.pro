<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Advices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advices-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Advices'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'q',
            'type',


            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [
                    'update' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['advices/update', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                    'view' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['advices/view', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-eye" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                    'delete' => function ($url, $model) {

                        $customurl = Yii::$app->getUrlManager()->createUrl(['advices/delete', 'id' => $model->id]); //$model->id для AR
                        return \yii\helpers\Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', $customurl, ['target' => '_blank']);
                    },
                ],
                'template' => '{update}{delete}{view}',
            ]

        ],
    ]); ?>
</div>
