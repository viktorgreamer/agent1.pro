<?php $style = <<< CSS
#preloader { position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: #333 url('web/loading.gif') no-repeat center center; }
CSS;

$this->registerCss($style);

$script = <<< JS
$('.mirs_preloader').on('click', function (e) {
    $('#preloader').show();
  /* $('#preloader').fadeOut('slow',function(){
    $(this).remove();
    });*/
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<?php $form = \yii\widgets\ActiveForm::begin(); ?>

<button class="btn-success btn" type="submit"><i class="fa fa-search mirs_preloader"></i></button>

<?php \yii\widgets\ActiveForm::end(); ?>
