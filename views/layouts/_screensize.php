<?php
$script = <<< JS
screen = screen.width;
    $.ajax({
        url: '/site/screen-size',
        data: {screen:screen},
        type: 'post',
        
    });

JS;
$this->registerJs($script, yii\web\View::POS_READY);
