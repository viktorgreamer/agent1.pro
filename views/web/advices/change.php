<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.10.2017
 * Time: 22:43
 */
use app\models\SaleFilters;

$this->title = "Как выгодно поменять недвижимость";
?>
<div class="row">

    <div class="col-8">
        <div class="row">
            <br>
            <h1>Как выгодно поменять недвижимость</h1>
            <br>

            <blockquote class="blockquote bq-success">
                <ol>
                    <li> Дорого продать вашу недвижимость.
                    </li>
                    <li> Недорого купить встречный вариант.
                    </li>
                    <li>Сделать, так чтобы и то и другое произошло одновременно.
                    </li>
                </ol>
            </blockquote>
            <blockquote class="blockquote bq-primary">
                <div class="row">
                    <div class="col-1">
                        <i class="fa fa-exclamation red-text fa-5x" aria-hidden="true"></i>
                    </div>
                    <div class="col-11">
                        У нас есть алгоритм из 7 пунктов для выгодного и "спокойного" обмена.
                        В силу "коммерческой тайны" мы не можем тут их раскрыть, но вы можете оставить
                        <a href="#" type="button" class="badge badge-info" style="font-size: larger" data-toggle="modal"
                           data-target="#modalContactForm"> заявку </a> или позвонить по
                        номеру 90-40-30 и узнать о них подробнее.
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

</div> <?php
echo $this->render('order',[ 'get' => 'advices']);
?>