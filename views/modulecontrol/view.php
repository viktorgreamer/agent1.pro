<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Modulecontrol */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Modulecontrols', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modulecontrol-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['Edit', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'list_of_cities:ntext',
            'last_parsing',
        ],
    ]) ?>

</div>
