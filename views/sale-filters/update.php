<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SaleFilters */

$this->title = 'Update Sale Filters: ' . $salefilter->name;
$this->params['breadcrumbs'][] = ['label' => 'Sale Filters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$session = Yii::$app->session;
$module = $session->get('module');
echo "<br>".span(" PLUS TAGS ".$salefilter->plus_tags);
echo "<br>".span(" MINUS TAGS ".$salefilter->minus_tags);
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
?>
<div class="sale-filters-update">

    <h1><?= Html::encode($this->title) ?></h1>


    <form action="update?id=<?= $salefilter->id; ?>" method="post" id="w0">
        <? echo $this->render('_form', [
            'salefilter' => $salefilter,
        ]) ?>

        <input type="text" name="id" hidden value="<?= $salefilter->id; ?>">
        <div class="md-form">
            <textarea type="text" name="komment" id="form76" class="md-textarea"><?= $salefilter->komment; ?></textarea>
            <label for="form76">Komment</label>
        </div>

        Photo
        <input type="text" name="photo" value="<?= $salefilter->photo; ?>">
    </form>
    <a id="#AddTagsSalefilter<?php echo $sale->id_address; ?>" type="button"
       data-toggle="modal"
       data-target="#TagsModal<?= $sale->id_address ?>"> <i
                class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
    </a>
    <div class="modal fade" id="TagsModal<?php echo $sale->id_address; ?>" tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <? echo $this->render('//tags/quick-add-form', [
                        'parent_id' => $salefilter->id,
                        'realtags' => \app\models\Tags::convertToArray($salefilter->tags_id),
                        'type' => 'salefilter'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>