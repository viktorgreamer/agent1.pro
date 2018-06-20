<?php


namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;
use yii\helpers\Url;

$urls = [
    '0' => [
        'name' => '<i class="fa fa-info" aria-hidden="true"></i> Главная',
        'action' => 'index',
        'link' => Url::to(['web/index'])
    ],
    '1' => [
        'name' => '<i class="fa fa-users" aria-hidden="true"></i> Клиенты',
        'action' => 'clients',
        'link' => Url::to(['web/clients'])
    ],

    '3' => [
        'name' => '<i class="fa fa-list" aria-hidden="true"></i> Прайс-листы',
        'action' => 'contacts',
        'link' => Url::to(['web/search-by'])
    ],
//    '4' => [
//        'name' => '<i class="fa fa-bolt red-text animated flash infinite" aria-hidden="true"></i> Проданные варианты',
//        'action' => 'missed-sales',
//        'link' => Url::to(['web/missed-sales'])
//    ],
    '5' => [
        'name' => '<i class="fa fa-address-book" aria-hidden="true"></i> Советы',
        'action' => 'contacts',
        'link' => Url::to(['web/advices'])
    ],
    '6' => [
        'name' => '<i class="fa fa-address-book" aria-hidden="true"></i> Контакты',
        'action' => 'contacts',
        'link' => Url::to(['web/contacts'])
    ],


];

?>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolling-navbar indigo" style="padding-top: 0px;padding-bottom: 0px;">
            <div class="container">


                <a class="navbar-brand" href="#"><img src="icon.png" style="width: 40px">Виктория</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <? foreach ($urls as $url) { ?>
                            <li class="nav-item" <? if (Yii::$app->controller->action->id == $url['action']) echo "\"active\""; ?>>
                                <a class="nav-link" href="<?= $url['link']; ?>">
                                    <?= $url['name']; ?>
                                    <? if (Yii::$app->controller->action->id == $url['action']) echo "<span class=\"sr-only\">(current)</span>"; ?>
                                </a>
                            </li>
                        <?php }
                        ?>
                    </ul>
                </div>
            </div>

        </nav>
    </header>
    <br>
    <br>


    <?php

?>