<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->getFullname();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <?php Yii::$app->formatter->locale = 'ru-RU'; ?>

    <?= DetailView::widget([
        'model' => $model,
        'id' => 'user_detai_view',
       // 'options' => ['class' => 'table-sm'],
        'attributes' => [
            'id',
           // 'network',
           // 'identity',
            'first_name',
            'last_name',
            'email:email',
            'phone:ntext',
          //  'password',
            'auth_date:date',
           // 'test_date:date',
            [
                'label' => 'Тестовый период',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->test_date) return "До ".Yii::$app->formatter->asDatetime($model->test_date);
                    else return "";
                }
            ],
            [
                'label' => 'Дата окончания',
                'format' => 'raw',
                'value' => function ($model) {
                   if ($model->exp_date) return Yii::$app->formatter->asDatetime($model->exp_date);
                   else return "";
                }
            ],
     //       'exp_date:date',
           // 'city',
           // 'extra',
           // 'rent',
            'sale',
          //  'city_modules',
           // 'semysms_token',
           // 'vk_token',
          //  'list_or_vk_groups:ntext',
            'money',
           // 'irr_id_partners',
        ],
    ]) ?>

</div>
