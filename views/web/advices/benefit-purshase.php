<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.10.2017
 * Time: 22:43
 */
use app\models\SaleFilters;

$this->title = "Как выгодно купить недвижимость";
?>
<div class="row">
    <div class="col-8">
        <div class="row">
            <br>
            <h1>Как выгодно купить недвижимость</h1>
            <br>

            <blockquote class="blockquote bq-success">
                <ol>
                    <li> Проверять 11 досок обьявлений недвижимости минимум каждые полчаса.
                    </li>
                    <li> Подавать рекламу в 15 источников 5 разными способами каждый день.
                    </li>
                    <li> Обзванивать более 100 риэлторов нашего города каждый день.
                    </li>
                    <li> Уметь торговаться.
                    </li>
                    <li> Правильно оценить состояние объекта и будущие вложения.
                    </li>
                </ol>
                </blockquote>
                <div class="row align-items-center">
                    <div class="col-1">
                        <i class="fa fa-check green-text fa-3x" aria-hidden="true"></i>
                    </div>
                    <div class="col-11">
                        <h4> Тогда у Вас есть действительно хорошие шансы купить недвижимость выгодно.</h4>
                    </div>
                </div>

            <blockquote class="blockquote bq-primary">
                <div class="row">
                    <div class="col-1">
                        <i class="fa fa-exclamation red-text fa-3x" aria-hidden="true"></i>
                    </div>
                    <div class="col-11"><h3> Но... </h3>
                        Вы можете просто оставить <a href="#" type="button" class="badge badge-info" style="font-size: larger" data-toggle="modal"
                                                     data-target="#modalContactForm"> заявку </a>
                        или позвонить по номеру 90-40-30, и быть в курсе всех новых выгодных предложений.
                    </div>
                </div>

            </blockquote>
        </div>
    </div>
    <div class="col-4">

        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4, 'not_q' => $_GET['q']]); ?>
        </div>
    </div>

</div>

            <!--Body-->
             <?php
           echo $this->render('order',[ 'get' => 'advices']);
            ?>
