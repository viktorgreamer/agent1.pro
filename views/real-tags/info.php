<?php
$js = <<< JS
toastr.success('$message')";
alert('i am here');
JS;
$this->registerJs($js, yii\web\View::POS_READY);