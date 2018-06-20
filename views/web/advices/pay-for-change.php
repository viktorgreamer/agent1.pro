<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 10.10.2017
 * Time: 22:43
 */
use app\models\SaleFilters;

$this->title = "Cколько доплатить в случае обмена?";
?>
<div class="row">
    <div class="col-8">
        <div class="row">
            <br>
            <h1> Узнать сколько доплатить в случае обмена?</h1>
            <br>
            <p> Предлагаем вам <span class="badge badge-info" style="font-size: larger">бесплатную пятиминутную</span>
                консультацию суммы доплаты (в обе строны) в случае обмена.</p>
            <p> По желанию клиента консультация может продлиться и больше, но нам будет достаточно пяти минут <span
                        class="badge badge-info" style="font-size: larger">чтобы</span>:
            </p>
            <blockquote class="blockquote bq-primary">
                <div class="row">
                    <div class="col-11">
                        <ul>
                            <li>
                                Оценить ваше жилье с точностью +-2%
                            </li>
                            <li>
                                Предложить вам несколько конкретных вариантов с ценами
                            </li>
                            <li>Рассказать о всех нюансах обмена
                            </li>
                        </ul>
                    </div>

                </div>

            </blockquote>
        </div>
        <div class="row">
            <div class="col-11">
                <p class="h4"> Консультация бесплатная и ни к чему вас не обязывает.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-11">
                Оставить
                <a href="#" type="button" class="badge badge-info" style="font-size: larger" data-toggle="modal"
                   data-target="#modalContactForm"> заявку </a>
                или позвонить по номеру 90-40-30.
            </div>
        </div>

    </div>
    <div class="col-4">

        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4,'not_q' => $_GET['q']]); ?>
        </div>
    </div>
</div>

<?php
echo $this->render('order',[ 'get' => 'advices']);
?>