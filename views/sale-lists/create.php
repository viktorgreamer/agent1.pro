<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SaleLists */

$this->title = 'Create Sale Lists';
$this->params['breadcrumbs'][] = ['label' => 'Sale Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-lists-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
