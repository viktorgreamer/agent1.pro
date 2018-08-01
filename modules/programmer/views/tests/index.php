<?php
/* @var $this yii\web\View */
?>
<h1>Список доступных тестов</h1>

<?php

$sections = ['tests', 'views','ftp', 'vk','cian','yandex','avito','irr','processing','webdriver','sale-filter','tags','users','stats'];
?>

<div class="tabs-wrapper">
    <ul class="nav classic-tabs tabs-cyan" role="tablist">
        <? foreach ($sections as $key=>$section) { ?>
            <li class="nav-item">
                <a class="nav-link waves-light <? if ($key ==0) echo " active"; ?>" data-toggle="tab" href="#<?= $section; ?>"
                   role="tab"><?= $section; ?></a>
            </li>
        <? } ?>
    </ul>
</div>


<div class="tab-content card">
    <? foreach ($sections as $key=>$section) {
       $actions = array_filter($allactions, function ($element) use ($section) {
            return preg_match("/\/programmer\/" . $section . "\//", $element);
        });

        ?>
        <div class="tab-pane fade <? if ($key ==0) echo " in show active"; ?>" id="<?= $section; ?>" role="tabpanel">
            <p><?php foreach ($actions as $action) {
                    echo "<br>" . \yii\helpers\Html::a(preg_replace("/\/programmer\//", "", $action), \yii\helpers\Url::to($action), ['target' => '_blank']);

                } ?>
        </div>
    <? } ?>
</div>




