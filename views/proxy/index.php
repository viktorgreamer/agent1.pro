<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Actions;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Proxies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proxy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Proxy'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ip',
            'port',
            'fulladdress',
            'login',
            'password',
            'add_time:datetime',
            'time:datetime',
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function ($model) {
                    return \app\models\Proxy::renderStatus($model->status);
                }
            ],
            [
                'label' => 'Buttons',
                'format' => 'raw',
                'value' => function ($model) {
                   $buttons .= Html::a(ICON_EDIT2, Url::to(['proxy/update', 'id' => $model->id]), ['target' => '_blank']);
                   $buttons .= Actions::renderChangeStatus($model->id, Actions::PROXY, Actions::PROXY_STATUS,
                       Actions::PROXY_STATUS_ACTIVE , ICON_PLAY2
                   );
                   $buttons .= Actions::renderChangeStatus($model->id, Actions::PROXY, Actions::PROXY_STATUS,
                       Actions::PROXY_STATUS_DISACTIVE , ICON_PAUSE2
                   );
                   return $buttons;
    }
            ],


        ],
    ]); ?>
</div>
