<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SeachingParsing */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parsings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parsing-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Parsing', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return \app\components\SaleWidget::widget([
            'sale'=> $model]);
        },
    ]) ?>
</div>
