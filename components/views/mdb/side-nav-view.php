<?php


namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;
use yii\helpers\Url;


if (Yii::$app->user->can('programmer')) {
    $sections = [
        '0' => [
            'name' => 'Продажа',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Поиск',
                        'link' => Url::to(['sale/index2'])
                    ],
                    '2' => [
                        'name' => 'Просмотр фильтров',
                        'link' => Url::to(['sale-filters/index'])
                    ],
                    '3' => [
                        'name' => 'Просмотр списков',
                        'link' => Url::to(['sale-lists/index'])
                    ],
                    '4' => [
                        'name' => 'Модерация',
                        'link' => Url::to(['sale/moderate'])
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
                    ], '3' => [
                    'name' => 'Аналитика цен',
                    'link' => Url::to(['sale-analitics/index'])
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
                    ],
                    '1' => [
                        'name' => 'Конфигурации',
                        'link' => Url::to(['parsing-configuration/index'])],
                    '2' => [
                        'name' => 'Контроль',
                        'link' => Url::to(['control-parsing/index'])
                    ],
                    '4' => [
                        'name' => 'Proxy',
                        'link' => Url::to(['proxy/index'])
                    ],
                    '5' => [
                        'name' => 'Sources',
                        'link' => Url::to(['sources/index'])
                    ],
                    '6' => [
                        'name' => 'Errors',
                        'link' => Url::to(['errors/index'])
                    ],
                    '7' => [
                        'name' => 'ErrorsLogs',
                        'link' => Url::to(['errors-log/index'])
                    ],
                    '8' => [
                        'name' => 'Selectors',
                        'link' => Url::to(['selectors/index'])
                    ], '9' => [
                    'name' => 'Sessions',
                    'link' => Url::to(['sessions/index'])
                ], '10' => [
                    'name' => 'Agent1.pro',
                    'link' => Url::to(['agent-pro/index'])
                ],

                ],
        ],
        '5' => [
            'name' => 'Tags',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Tags',
                        'link' => Url::to(['tags/index'])
                    ]
                ],


        ],
        '6' => [
            'name' => 'Заявки',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Заявки',
                        'link' => Url::to(['orders/index'])
                    ]
                ],


        ],
        '7' => [
            'name' => 'Setting',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Профиль',
                        'link' => Url::to(['user/profile'])
                    ],
                    '2' => [
                        'name' => 'Выход',
                        'link' => Url::to(['site/log-out'])
                    ]
                ],
        ],
        '8' => [
            'name' => 'Web',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Web',
                        'link' => Url::to(['web/index'])
                    ],
                    '1' => [
                        'name' => 'LangingPage',
                        'link' => Url::to(['web/landing-page'])
                    ]
                ],
        ],
        '9' => [
            'name' => 'console',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'main-script',
                        'link' => Url::to(['console/mainer'])
                    ]
                ],
        ],
        '10' => [
            'name' => 'ProgrammerModule',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'tests',
                        'link' => Url::to(['programmer/tests'])
                    ]
                ],
        ],


    ];
} else {
    $sections = [
        '0' => [
            'name' => 'Продажа',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Поиск',
                        'link' => Url::to(['sale/index2'])
                    ],

                    '2' => [
                        'name' => 'Просмотр фильтров',
                        'link' => Url::to(['sale-filters/index'])
                    ],
                    '3' => [
                        'name' => 'Просмотр списков',
                        'link' => Url::to(['sale-lists/index'])
                    ],
                    '4' => [
                        'name' => 'Модерация',
                        'link' => Url::to(['sale/moderate'])
                    ],
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

        '5' => [
            'name' => 'Tags',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Tags',
                        'link' => Url::to(['tags/index'])
                    ]
                ],


        ],
        '7' => [
            'name' => 'Setting',
            'subsections' =>
                [
                    '0' => [
                        'name' => 'Профиль',
                        'link' => Url::to(['user/profile'])
                    ],
                    '2' => [
                        'name' => 'Выход',
                        'link' => Url::to(['site/log-out'])
                    ]
                ],
        ],

    ];
}

$user = \Yii::$app->user->identity;

?>

<header>


    <!--/.Navbar-->


    <!-- SideNav Menu -->
    <ul id="slide-out" class="side-nav custom-scrollbar" style="width: 210px">

        <!-- Logo -->
        <li>
            <div class="text-center">
                <h3>Mirs.pro</h3>
            </div>
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
                                    <?php if (count($section['subsections']) > 0) {
                                        foreach ($section['subsections'] as $subsection) { ?>

                                            <li>
                                                <a href="<?= $subsection['link']; ?> "> <?= $subsection['name']; ?> </a>

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
// Yii::$app->view->registerJs("$('.button-collapse').sideNav('hide');", yii\web\View::POS_READY);
?>

