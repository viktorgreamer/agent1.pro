<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\SaleFilters;
use yii\widgets\ListView;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */


$_SESSION['title_to_copy'] = '';
$session = Yii::$app->session;

?>
<div class="sale-index">
    <? echo $this->render('_search_by_filter', ['salefilter' => $salefilter]); ?>
    <?php if ($salefilter->id) { ?>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_sale-table',

            'layout' => "{summary}\n{items}\n{pager}",
            'summary' => 'Показано {count} из {totalCount} предложений',
            'summaryOptions' => [
                'tag' => 'span',
                'class' => 'badge red'
            ],
            'viewParams' => ['controls' => true, 'salefilter' => $salefilter],

            'emptyText' => '<p>Нет вариантов</p>',
            'emptyTextOptions' => [
                'tag' => 'p'
            ],

            'pager' => [
                'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
                'linkOptions' => ['class' => 'page-link'],
                'firstPageCssClass' => 'page-item first',
                'prevPageCssClass' => 'page-item last',
                'nextPageCssClass' => 'page-item next',
                'activePageCssClass' => 'page-item active',
                'disabledPageCssClass' => 'page-item invisible'
            ],
        ]) ?>
    <?php } ?>

</div>


<!-- Button trigger modal -->


<? if ($_GET['export']) {
    $session = Yii::$app->session;
    $phone = $session->get('phone');
    $phone = explode(",", $phone);

    ?>
    <!-- Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Окно экспорта</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea id="export" ><?php echo $_SESSION['title_to_copy']."\n ТЕЛЕФОН : ". \app\models\Renders::phoneToString($phone[0]); ?></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
<? } ?>

<!--            <button type="button" class="btn btn-success btn-sm btn-rounded" onclick='ClipboardHelper.copyText("--><?// //= $_SESSION["title_to_copy"] ?>////
<!--")'>EXPORT</button>-->
<?php
$this->render('_js_sale', ['models' => $dataProvider->getModels()]);

?>


