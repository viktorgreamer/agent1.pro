<?php
?>




<div class="modal fade" id="15sec_promo_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                    <h3>Хотите оформим заявку по вашим уникальным требованиям?</h3>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer justify-content-center">
                <a type="button" class="btn btn-primary-modal" data-toggle="modal" data-target="#modalContactForm"
                   data-dismiss="modal">Да, я хочу!</a>

                <a type="button" class="btn btn-outline-secondary-modal waves-effect" data-dismiss="modal">Спасибо,
                    не надо.</a>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
<?php
$session = Yii::$app->session;
if ($session->getFlash('order_get')) {
    $script = <<<JS
toastr["info"]("Заявка успешно отправлена, ожидайте звонка в ближайщие несколько минут!");
JS;
    $this->registerJs($script, yii\web\View::POS_READY);

} else {
    if (!$session->get('contact-form-search-by-notice')) {
        $JS = <<<JS
function showModal() {
    $('#15sec_promo_modal').modal('show');
    $.ajax({
        url: '/web/set-contact-form-shown?q=sb',
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
}
setTimeout(showModal, 15000);
JS;
        $this->registerJs($JS, \yii\web\View::POS_READY);
    }

}
?>

