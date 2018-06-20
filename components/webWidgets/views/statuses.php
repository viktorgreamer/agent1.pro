<?php


$script = <<< JS
$('.change-statuses').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        url: '/statuses/set',
        data: {name: name},
        type: 'get',
        success: function (data) {
            toastr.success(data);
          },
    });
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
