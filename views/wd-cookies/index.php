<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WdCookiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wd Cookies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wd-cookies-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Wd Cookies', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ip_port',
            'id_server',
            [
                'label' => 'BODY',
                'format' => 'raw',
                'value' => function ($model) {
                    $body = json_decode($model->body,true);
                    if ($body) {
                        $table = "<table>";
                        foreach ($body as $item) {
                            $table .= "<tr><td>" . $item['name'] . "</td><td>" . mb_strimwidth($item['value'],0,80) . "</td><td>" . $item['domain'] . "</td></tr>";
                        }
                        $table .= "</table>";
                        return $table;
                    }
                }
            ],
            'time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
