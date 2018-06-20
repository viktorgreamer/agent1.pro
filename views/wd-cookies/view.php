<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\WdCookies */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wd Cookies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wd-cookies-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'ip_port',
            'id_server',
            'body:ntext',
            'time:datetime',
        ],
    ]) ?>

</div>
