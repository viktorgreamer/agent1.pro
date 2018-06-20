<?php

use yii\helpers\Html;
use yii\grid\GridView;



$this->title = 'Экспорт списка в рассылку смс';

?>
<div class="sms-api-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]);


    ?>

    <h5><?= " Экспортировали ".count($sale)." записей." ?></h5>
</div>
