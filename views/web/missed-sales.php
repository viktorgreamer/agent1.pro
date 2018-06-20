<?php
use yii\widgets\ActiveForm;
use app\models\SaleLists;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);

?>


    <!--Modal: Contact form-->
    <div class="modal fade" id="modalContactForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog cascading-modal" role="document">
            <!--Content-->
            <div class="modal-content">

                <!--Header-->
                <div class="modal-header light-blue darken-3 white-text">
                    <h4 class="title"><i class="fa fa-pencil"></i>Контактная форма
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <!--Body-->
                <form action="<?= \yii\helpers\Url::to(['web/missed-sales']) ?>" method="post">
                    <div class="modal-body mb-0">
                        <div class="md-form form-sm">
                            <i class="fa fa-user prefix"></i>
                            <input type="text" name="name" id="form19" class="form-control">
                            <label for="form19">Как к вам обращасться?</label>
                        </div>

                        <div class="md-form form-sm">
                            <i class="fa fa-phone prefix"></i>
                            <input type="text" id="form21" name="phone" class="form-control">
                            <label for="form21">Телефон</label>
                        </div>

                        <div class="md-form form-sm">
                            <i class="fa fa-pencil prefix"></i>
                            <textarea type="text" id="form8" name="description" class="md-textarea mb-0"></textarea>
                            <label for="form8">Описание вашей заявки</label>
                        </div>

                        <div class="text-center mt-1-half">
                            <button class="btn btn-info mb-2" type="submit">Отправить <i class="fa fa-send ml-1"></i>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
            <!--/.Content-->
        </div>
    </div>
    <!--Modal: Contact form-->


    <!-- Central Modal Medium Success -->
    <div class="modal fade" id="centralModalSuccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-side modal-bottom-right modal-notify modal-info" role="document">
            <!--Content-->
            <div class="modal-content">
                <!--Header-->
                <div class="modal-header">
                    <p class="heading lead"></p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">&times;</span>
                    </button>
                </div>

                <!--Body-->
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fa fa-check fa-2x mb-3 animated rotateIn"></i>
                        <h3>Хотите узнать, как непропускать выгодные предложения?</h3>
                    </div>
                </div>

                <!--Footer-->
                <div class="modal-footer justify-content-center">
                    <a type="button" class="btn btn-primary-modal" data-toggle="modal" data-target="#modalContactForm"
                       data-dismiss="modal">Да, я хочу!</a>

                    <a type="button"
                       class="btn btn-outline-secondary-modal waves-effect contact-form-missed-sale-notice"
                       data-dismiss="modal">Спасибо,
                        не надо.</a>
                </div>
            </div>
            <!--/.Content-->
        </div>
    </div>
    <!-- Central Modal Medium Success-->
<?php
$session = Yii::$app->session;

if ($session->getFlash('order_get')) {
    $script = <<<JS
toastr["info"]("Заявка успешно отправлена, ожидайте звонка в ближайщие несколько минут!");
JS;
    $this->registerJs($script, yii\web\View::POS_READY);

} else {
    if (!$session->get('contact-form-missed-sale-notice')) {
        $JS = <<<JS
function showModal() {
    $('#centralModalSuccess').modal('show');
    $.ajax({
        url: '/web/set-contact-form-shown?q=ms',
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
}
setTimeout(showModal, 10000);
JS;
        $this->registerJs($JS, \yii\web\View::POS_READY);
    }

}
?>

    <div class="row">
        <div class="col-md-8 ">
            <h1>Варианты, которые Вы могли купить, но не успели...</h1>
            <h3>  <?php echo $salelist->name; ?> </h3>
            <?php echo \app\components\SaleTableWidgets::widget([
                'salelist' => $salelist,
                'sales' => $data,
                'type' => 'web',
                'options' => ['show_date_of_die']
            ]); ?>
            <?php echo \yii\widgets\LinkPager::widget([
                'pagination' => $pages,
                'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
                'linkOptions' => ['class' => 'page-link'],
                'firstPageCssClass' => 'page-item first',
                'prevPageCssClass' => 'page-item last',
                'nextPageCssClass' => 'page-item next',
                'activePageCssClass' => 'page-item active',
                'disabledPageCssClass' => 'page-item disabled'
            ]);
            ?>
        </div>

        <div class="col-md-4">
            <h3>Смотрите также:</h3>
            <?php
            $salelists = SaleLists::find()->where(['in', 'id', $salelist->getRelevantedByTags(5,'public-sold')])->andWhere(['<>', 'id', $salelist->id])->all();
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

            <div class="row">
                <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4]); ?>
            </div>
        </div>


    </div>

<?php


