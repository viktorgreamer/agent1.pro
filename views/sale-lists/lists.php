<?php

echo \app\components\MdbSelect::widget([
    'request_type' => 'get',
    'name' => 'id',
    'placeholder' => 'Название списка',
    'options' => \app\models\SaleLists::getMyListsAsArray($_GET['type']),
    'label' => 'Название списка',
    'color' => 'primary'
]); ?>