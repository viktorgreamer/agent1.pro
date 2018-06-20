<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'user_id',
          //  'network',
          //  'identity',
            'first_name',
            'last_name',
             'email:email',
            // 'phone:ntext',
             'password',
            // 'auth_date',
            // 'test_date',
            // 'exp_date',
            // 'city',
            // 'extra',
            // 'rent',
            // 'sale',
            // 'city_modules',
            // 'semysms_token',
            // 'vk_token',
            // 'list_or_vk_groups:ntext',
            // 'money',
            // 'irr_id_partners',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
