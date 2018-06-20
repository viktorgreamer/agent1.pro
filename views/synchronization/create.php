<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Synchronization */

$this->title = 'Create Synchronization';
$this->params['breadcrumbs'][] = ['label' => 'Synchronizations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="synchronization-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
