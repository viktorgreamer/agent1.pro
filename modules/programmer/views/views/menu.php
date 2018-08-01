<?php

use yii\widgets\Menu;

?>
<nav class="navbar navbar-expand-lg navbar-dark primary-color">

    <!-- Navbar brand -->
    <a class="navbar-brand" href="#">Navbar</a>

    <!-- Collapse button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav"
            aria-controls="basicExampleNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible content -->
    <div class="collapse navbar-collapse" id="basicExampleNav">
        <?php
        echo Menu::widget([
            'items' => [
                [
                    'label' => 'ДЛЯ АДМИНТА',
                    'url' => ['site/admin-can-view'],
                    'visible' => Yii::$app->user->can('admin'),
                ],
                [
                    'label' => 'ДЛЯ testUser',
                    'url' => ['site/admin-can-view'],
                    'visible' => Yii::$app->user->can('testUser'),
                ],
                [
                    'label' => 'ДЛЯ ГОСТЯ',
                    'url' => ['site/admin-can-view'],
                    'visible' => Yii::$app->user->isGuest,
                ],
                ['label' => 'О компании', 'url' => ['site/about']],
                ['label' => 'Услуги',
                    'url' => ['services/index'],
                    'options' => ['class' => 'nav-item dropdown'],
                    'template' => '<a href="{url}" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{label}</a>',
                    'items' => [
                        [
                            'label' => 'Подменю для гостя',
                            'url' => ['services/juridical-services'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->isGuest,
                        ],
                        [
                            'label' => 'Подменю для Admin',
                            'url' => ['services/juridical-services'],
                            'options' => [
                                'class' => 'dropdown-item',
                                'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->can('admin'),
                        ],
                        [
                            'label' => ' Подменю для testUser',
                            'url' => ['services/valuation-services'],
                            'options' => ['class' => 'dropdown-item', 'style' => 'padding-top: 4px;padding-bottom: 4px;'],
                            'visible' => Yii::$app->user->can('testUser'),
                        ],
                    ],
                    // 'linkTemplate' => '<a class="nav-link" href="{url}">{label}</a>',
                    // 'linkTemplate' => '<a class="dropdown-item" href="{url}">{label}</a>',
                ],
                ['label' => 'Контакты', 'url' => ['site/contacts']]

            ],
            'options' => [
                'class' => 'navbar-nav mr-auto'
            ],
            'linkTemplate' => '<a class="nav-link" href="{url}">{label}</a>',
            'submenuTemplate' => "\n<ul class='dropdown-menu'>\n{items}\n</ul>\n",

            'itemOptions' => ['class' => 'nav-item'],

            'activeCssClass' => 'active',
            'encodeLabels' => 'false',
        ]);

        ?>

</nav>
