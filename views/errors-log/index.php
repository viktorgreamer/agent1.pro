<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ErrorsLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Errors Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="errors-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Errors Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_error',
            'time:datetime',
            'body',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
