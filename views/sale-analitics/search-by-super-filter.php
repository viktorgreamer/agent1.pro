<?php

use yii\helpers\Html;


use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use app\models\SaleFilters;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$session = Yii::$app->session;

$items = ArrayHelper::map(SaleFilters::find()
    ->where(['is_super_filter' => 1])
    ->andwhere(['user_id' => $session->get('user_id')])
    ->all(), 'id', 'name');
$param = ['options' => [$salefilter->id => ['selected' => true]]]


?>


<div class="sale-index">


    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'action' => 'search-by-super-filter'
    ]) ?>


    <div class="row">
        <div class="col-xs-4">Название фильтра


            <?= $form->field($salefilter, 'id')->dropdownList($items, $param)->label('') ?>

            <div class="form-group">
                <select name="type_of_show">
                    <option value="not_starred" <?php if ($_GET['type_of_show'] == "not_starred") echo "selected"; ?>>Неотложенные</option>
                    <option value="all" <?php if ($_GET['type_of_show'] == "all") echo "selected"; ?>>Все</option>
                    <option value="starred" <?php if ($_GET['type_of_show'] == "starred") echo "selected"; ?>>Отложенные</option>
                    <option value="banned" <?php if ($_GET['type_of_show'] == "banned") echo "selected"; ?> >Заблокированные</option>
                </select>

                                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>


        </div>

        <?php echo \app\components\SaleAnaliticsTableWidgets::widget([
            'salefilter' => $superfilter,
            'sales' => $data['data']
        ]); ?>






        <?php
        // display pagination
        echo LinkPager::widget([
            'pagination' => $data['pages'],
        ]);
        ?>


    </div>
