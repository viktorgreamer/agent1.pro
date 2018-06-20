<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Parsing */

$this->title = 'Create Parsing';
$this->params['breadcrumbs'][] = ['label' => 'Parsings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parsing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
