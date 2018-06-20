<?php
use yii\widgets\ActiveForm;
use app\models\SaleLists;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
$salelist = SaleLists::findOne(49);
?>
<?php
$body = "Оставьте заявку, чтобы получать выгодные предлоджения в Viber, Whatsapp, Vkontakte, E-mail.  ";
$body = " Телефон <br>" . \app\components\MdbTextInput::widget(['request_type' => 'post',
        'name' => 'phone',
        'label' => '',]) . " <br>Имя<br>
" . \app\components\MdbTextInput::widget(['request_type' => 'post',
        'name' => 'name',
        'label' => '',]) . " <br>

";
?>
<form action="<?php \yii\helpers\Url::to(['web/orders']); ?>" method='post' id='w0'>
    <?= \app\components\mdbModal::widget(
        [
            'header' => 'Предложение',
            'modalDialogClass' => 'modal-side modal-right modal-notify modal-info',
            'body' => $body,
            'trigger' => [
                'timeout' => "1000",
                'type' => 'timeout'],
//        'trigger' => [
//            'class' => 'btn btn-primary',
//            'body' => 'Мажми на меня ',
//            'type' => 'button'],
            'idModal' => 'id',
            'modalFooter' => [
                'class' => 'justify-content-center',
                'body' => Html::submitButton('<i class="fa fa-refresh" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm'])."
                     <a type='button' class='btn btn-outline-secondary-modal waves-effect' data-dismiss='modal'>Спасибо, ненадо.</a>
                     "]]) ?>

</form>
<div class="row">
    <div class="col-md-9 ">
        <h1>Варианты, которые Вы могли купить, но не успели...</h1>
        <h3>  <?php echo $salelist->name; ?> </h3>
        <?php echo \app\components\SaleTableWidgets::widget([
            'salelist' => $salelist,
            'sales' => $data,
            'type' => 'web',
            'options' => ['show_date_of_die']
        ]); ?>
    </div>


    <div class="col-md-3">
        <h3>Смотрите также:</h3>
        <?php
        $salelists = SaleLists::find()->where(['in', 'id', $salelist->getRelevantedByTags(5)])->andWhere(['<>', 'id', $salelist->id])->all();
        foreach ($salelists as $salelist1) {
            $salelist1 = \app\models\SaleLists::findOne($salelist1->id);
            ?>
            <div class="row">
                <div class="col-12">

                    <a href="<?= \yii\helpers\Url::to(['web/search-by', 'id' => $salelist1->id]); ?>">
                        <h5><span class="badge cyan animated pulse "><?php echo $salelist1->name; ?> </span></h5>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-9">
                    от <?php echo $salelist1->getMinPrice(); ?>
                </div>
                <div class="col-3"><span
                        class="badge badge-pill pink"><?php echo $salelist1->getCount(); ?>
                        ШТ.</span>
                </div>

            </div>

            <?
        }
        ?>


    </div>
</div>




