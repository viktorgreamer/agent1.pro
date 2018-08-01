<?php

use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->title = 'Mirs.pro';
?>
<br>
<br>
<?= $this->render('_intro'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_stats'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_features'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<? // $this->render('_animate'); ?>
<?= $this->render('_public_filters'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_prices'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_footer'); ?>

