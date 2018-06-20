<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.10.2017
 * Time: 22:43
 */
use app\models\SaleFilters;

$this->title = "Cколько стоит моя недвижимость?";
?>
<div class="row">
    <div class="col-8">
        <span class="row">
            <br>
            <h1> Cколько стоит моя недвижимость?</h1>
            <br>
            <p> Предлагаем вам <span class="badge badge-info" style="font-size: larger">бесплатную оценку</span>
                стоимости вашей недвижимости точностью  <span class="badge badge-info"
                                                              style="font-size: larger">+-2%.</p></span>:

        <div class="row">
            <div class="col-11">
                <p class="h4"> Оценка бесплатная и ни к чему вас не обязывает.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-11">
                Оставить <a href="#" type="button" class="badge badge-info" style="font-size: larger" data-toggle="modal"
                               data-target="#modalContactForm"> заявку </a>
                или позвонить по номеру 90-40-30.
            </div>
        </div>
    </div>


    <div class="col-4">

        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4, 'not_q' => $_GET['q']]); ?>
        </div>
    </div>
</div>

<?php
echo $this->render('order',[ 'get' => 'advices']);
?></div>