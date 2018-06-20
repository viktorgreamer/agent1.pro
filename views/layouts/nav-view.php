<?php
use yii\helpers\Url;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\bootstrap\Dropdown;


$sections = [
    '0' => [
        'name' => 'Продажа',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Поиск',
                    'link' => Url::to(['sale/search'])
                ],
                '1' => [
                    'name' => 'Поиск по фильтрам',
                    'link' => Url::to(['sale/search-by-filter'])
                ],
                '2' => [
                    'name' => 'Просмотр фильтров',
                    'link' => Url::to(['sale-filters/index'])
                ],
                '3' => [
                    'name' => 'Просмотр списков',
                    'link' => Url::to(['sale-lists/index'])
                ],
            ],
    ],
    '1' => [
        'name' => 'Аналитика',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Графики',
                    'link' => Url::to(['sale-analitics/create-graph'])
                ],
                '1' => [
                    'name' => 'Анализ других агентов',
                    'link' => Url::to(['agents/index'])
                ]
            ],
    ],
    '2' => [
        'name' => 'Постинг',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Списки',
                    'link' => Url::to(['sale-lists/index'])
                ],
                '1' => [
                    'name' => 'Поиск по фильтрам',
                    'link' => Url::to(['sale-lists/export'])
                ],
                '2' => [
                    'name' => 'Рассылка смс',
                    'link' => Url::to(['sms-api/index'])
                ]
            ],
    ],
    '3' => [
        'name' => 'Addresses',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Индекс',
                    'link' => Url::to(['addresses/index'])
                ],'1' => [
                    'name' => 'auto-fix-addresses',
                    'link' => Url::to(['console/auto-fix-addresses'])
                ],

            ],
    ],
    '4' => [
        'name' => 'Parsing',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Индекс',
                    'link' => Url::to(['synchronization/index'])
                ],'1' => [
                    'name' => 'Собираемые конфигурации',
                    'link' => Url::to(['parsing-configuration/index'])
                ],

            ],
    ]


];
$setting = [
    '0' => [
        'name' => 'Setting',
        'subsections' =>
            [
                '0' => [
                    'name' => 'Профиль',
                    'link' => Url::to(['user/profile'])
                ],
                '1' => [
                    'name' => 'Сменя пользователя',
                    'link' => Url::to(['sale-lists/export'])
                ],
                '2' => [
                    'name' => 'Рассылка смс',
                    'link' => Url::to(['sms-api/index'])
                ]
            ],
    ],

];
?>



<!--Navbar-->
<div class="container">

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <h5>Agent1.pro</h5>
                    <small>(<?= $user->city ?>)</small>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
                <ul class="nav navbar-nav">
                    <?
                    if (count($sections) > 0) {
                        foreach ($sections as $key => $section) { ?>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <?= $section['name']; ?><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php
                                    if (count($section['subsections']) > 0) {
                                        foreach ($section['subsections'] as $subsection) { ?>
                                            <li>  <a class="dropdown-item" href="<?= $subsection['link']; ?> "> <?= $subsection['name']; ?></a> </li>
                                        <? }
                                    } ?>


                                </ul>
                            </li>
                        <? }
                    } ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?
                    if (count($setting) > 0) {
                        foreach ($setting as $key => $section) { ?>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <?= $section['name']; ?><span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php
                                    if (count($section['subsections']) > 0) {
                                        foreach ($section['subsections'] as $subsection) { ?>
                                            <li>  <a class="dropdown-item" href="<?= $subsection['link']; ?> "> <?= $subsection['name']; ?></a> </li>
                                        <? }
                                    } ?>


                                </ul>
                            </li>
                        <? }
                    } ?>
                </ul>
            </div>
        </div>
    </nav>

</div>


