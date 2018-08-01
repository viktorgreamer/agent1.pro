<?php

use yii\widgets\Menu;
use app\components\Mdb;
use app\models\User;

$user = Yii::$app->user->identity;
/* @var $user \app\models\User */

?>
<nav class="navbar navbar-expand-lg navbar-dark primary-color">
 <!-- Navbar brand -->
    <a class="navbar-brand" href="#"><?php echo \yii\helpers\Html::img(Yii::getAlias("@web") . "/web/icon.png"); ?>
        &nbsp;MIRS.PRO</a>
    <?php if (Yii::$app->user->can('admin')) { ?>
        <a href="#" data-activates="slide-out" class="btn p-3 m-0 button-collapse"><i
                    class="fa fa-bars"></i></a>
    <? } ?>

    <div id='available_actions' class="ml-3"></div>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav"
            aria-controls="basicExampleNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- Collapse button -->


    <!-- Collapsible content -->
    <div class="collapse navbar-collapse" id="basicExampleNav">
        <?php
        echo Menu::widget([
            'encodeLabels' => false,
            'items' => [
                /* [
                     'label' => 'ДЛЯ АДМИНТА',
                     'url' => ['site/admin-can-view'],
                     'visible' => Yii::$app->user->can('admin'),
                 ],*/
                /* [
                     'label' => 'Поиск',
                     'url' => ['sale/index2'],
                     'visible' => Yii::$app->user->can('testUser'),
                 ],*/
                /* [
                     'label' => 'ДЛЯ ГОСТЯ',
                     'url' => ['site/admin-can-view'],
                     'visible' => Yii::$app->user->isGuest,
                 ],*/
                [
                    'label' => "2" . Mdb::Fa('envelope-o'),
                    'url' => ['site/about'],
                    'visible' => Yii::$app->user->can(User::ROLE_TESTUSER),],
                ['label' => Mdb::Fa('search') . ' Поиск',
                    //'url' => ['services/index'],
                    'options' => ['class' => 'nav-item dropdown'],
                    'visible' => Yii::$app->user->can(User::ROLE_TESTUSER),
                    'template' => '<a href="{url}" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{label}</a>',
                    'items' => [
                        [
                            'label' => "",
                            'options' => [
                                'class' => 'dropdown-item',
                                'id' => "available_actions",
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                        ], [
                            'label' => Mdb::Fa('search') . ' Продажа',
                            'url' => ['sale/index2'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->can(User::ROLE_TESTUSER),
                        ],
                        [
                            'label' => Mdb::Fa('floppy-o') . ' Фильтры',
                            'url' => ['sale-filters/index-list'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->can(User::ROLE_PLAN1),
                        ],
                       /* [
                            'label' => ' Подменю для testUser',
                            'url' => ['services/valuation-services'],
                            'options' => ['class' => 'dropdown-item', 'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->can('testUser'),
                        ],*/
                    ],

                ],
                [
                    'label' => Mdb::Fa('sign-in') . ' Вход',
                    'url' => ['user/sign-in'],
                    'visible' => Yii::$app->user->isGuest,

                ],

                ['label' => Mdb::Fa('user') . " " . $user->fullname,
                    'url' => ['services/index'],
                    'options' => ['class' => 'nav-item dropdown'],
                    'template' => '<a href="{url}" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{label}</a>',
                    'items' => [
                        [
                            'label' => 'Ваши объявления',
                            'url' => ['sales/my'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                        ],
                        [
                            'label' => 'Настройки',
                            'url' => ['/user/profile'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                        ],
                        [
                            'label' => "Тариф: ".Yii::$app->user->identity->tarif,
                            'url' => ['/user/profile'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                        ],
                        [
                            'label' => 'Выход',
                            'url' => ['/user/sign-out'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                        ],
                    ],
                    'visible' => !Yii::$app->user->isGuest,
                    // 'linkTemplate' => '<a class="nav-link" href="{url}">{label}</a>',
                    // 'linkTemplate' => '<a class="dropdown-item" href="{url}">{label}</a>',
                ],


            ],
            'options' => [
                'class' => 'navbar-nav ml-auto'
            ],
            'linkTemplate' => '<a class="nav-link" href="{url}">{label}</a>',
            'submenuTemplate' => "\n<ul class='dropdown-menu'>\n{items}\n</ul>\n",

            'itemOptions' => ['class' => 'nav-item'],

            //  'activeCssClass' => 'active',

        ]);

        ?>


</nav>
