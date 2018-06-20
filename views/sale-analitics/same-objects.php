<?php

use yii\widgets\ActiveForm;


?>


<div class="sale-index">

   <?php echo \app\components\SaleTableWidgets::widget([
        'sales' => $saleshistory
    ]); ?>


</div>
