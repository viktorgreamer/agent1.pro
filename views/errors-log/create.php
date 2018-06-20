<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ErrorsLog */

$this->title = 'Create Errors Log';
$this->params['breadcrumbs'][] = ['label' => 'Errors Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="errors-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
