<?php

use yii\widgets\ActiveForm;


?>


<div class="sale-index">


    <?php echo \app\components\OneSaleWidget::widget([
        'sales' => $sales
    ]); ?>


    <hr>




</div>
