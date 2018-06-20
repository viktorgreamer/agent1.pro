<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->first_name." ".$model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('РЕЖИМ MОДЕРАЦИИ', ['change', 'user_id' => $model->user_id,'moderated_mode' => true ], ['class' => 'btn btn-danger']) ?>
        <?= Html::a('Великий Новгород', ['change', 'user_id' => 1], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Тверь', ['change', 'user_id' => 2], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Псков', ['change', 'user_id' => 3], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Старая Русса', ['change', 'user_id' => 4], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Боровичи', ['change', 'user_id' => 5], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Кириши', ['change', 'user_id' => 6], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Великий Новгород - тест', ['change', 'user_id' => 7], ['class' => 'btn btn-primary']) ?>


    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'last_name',
            'email:email',
            'phone:ntext',
            'auth_date',
            'test_date',
            'exp_date',
            'city',
            'city_modules',
            'semysms_token',
            'vk_token',
            'list_or_vk_groups:ntext',
            'money',
        ],
    ]) ?>

</div>
