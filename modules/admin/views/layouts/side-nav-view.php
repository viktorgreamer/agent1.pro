<?php


namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;
use yii\helpers\Url;

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
                ], '1' => [
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
                ], '1' => [
                'name' => 'Собираемые конфигурации',
                'link' => Url::to(['parsing-configuration/index'])
            ],

            ],
    ],
    '5' => [
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

    <header>
<!-- SideNav slide-out button -->
<a href="#" data-activates="slide-out" class="btn btn-primary p-3 button-collapse"><i class="fa fa-bars"></i></a>

<!-- SideNav Menu -->
<ul id="slide-out" class="side-nav fixed custom-scrollbar" style="width: 210px">

    <!-- Logo -->
    <li>

            <h3>Agent1.pro</h3>


    </li>
    <!--/. Logo -->


    <!-- Side navigation links -->
    <li>
        <ul class="collapsible collapsible-accordion">
            <?
            if (count($sections) > 0) {
                foreach ($sections as $key => $section) { ?>
                    <!-- Collapsible link -->
                    <li><a class="collapsible-header waves-effect arrow-r"><i
                                    class="fa fa-chevron-right"></i><?= $section['name']; ?><i
                                    class="fa fa-angle-down rotate-icon"></i></a>
                        <div class="collapsible-body">
                            <ul>
                                <?php
                                if (count($section['subsections']) > 0) {
                                    foreach ($section['subsections'] as $subsection) { ?>

                                        <li><a href="<?= $subsection['link']; ?> "> <?= $subsection['name']; ?> </a>

                                        </li>
                                    <? }
                                } ?>
                            </ul>
                        </div>
                    </li>

                <? }
            } ?>

        </ul>
    </li>
    <!-- Side navigation links -->

</ul>
<!-- SideNav Menu -->
</header>
<?php

?>