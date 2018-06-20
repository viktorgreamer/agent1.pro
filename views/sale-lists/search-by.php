<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SaleLists;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $salelist SaleLists */

$this->title = $salelist->name;
$session = Yii::$app->session;
$list_of_ids = $session->get('list_of_ids');
if ($salelist->parent_salefilter) {
    $salefilters = \app\models\SaleFilters::find()->where(['in','id',explode(",",$salelist->parent_salefilter)])->all();
    foreach ($salefilters as $salefilter) {
        echo "<h4>имеется фильтр агрегатор".$salefilter->name."</h4>";
    }

}
if (empty($salelist)) {
    ?>
    У вас еще нет списков вариантов на продажу
    <?php
} else {


    ?>
    <div class="sale-lists-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?  // echo \app\components\TagsWidgets::widget(['salelist_id' => $salelist->id]);

        ?>
        <br>
        <?= Html::a('Экспорт в рассылку смс', ['export-ajax', 'id' => $salelist->id], ['class' => 'btn btn-primary btn-sm']) ?>

        <?= Html::a('Сохранить фото', ['download-photo', 'id' => $salelist->id], ['class' => 'btn btn-primary btn-sm']) ?>

        <?= Html::a('Irr-XML', ['xml-response', 'id' => $salelist->id], ['class' => 'btn btn-primary btn-sm']) ?>

        <button class="btn btn-danger btn-sm export-to-sms-api-button" data-id="<?= $salelist->id ?>">
            <i class="fa fa-share" aria-hidden="true"></i></button>

        <button id="#AddTags" type="button" class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#TagsModal"> +tags
        </button>



        <? if (!empty($list_of_ids)) { ?>

            <button class="btn btn-success btn-sm add-favourites-to-list" data-id_list="<?= $salelist->id ?>">Добавить
                избранное в этот лист<?php echo count(explode(',', $list_of_ids)); ?> </button>
            <?php
        }
        ?>



        <?= Html::a('Delete', ['delete', 'id' => $salelist->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <br>
        <div id="search-form">
            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>




        <?
        echo " всего " . count(explode(',', SaleLists::findOne($salelist->id)->list_of_ids)) ?>

    </div>

    <div class="container">
        <?php echo \app\components\SaleTableWidgets::widget([
            'salelist' => $salelist,
            'sales' => $data,
            'controls' => true,
            'options' => 'show_stat'
        ]); ?>

        <?php
        // display pagination
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
    <?php
}
?>
<?php
