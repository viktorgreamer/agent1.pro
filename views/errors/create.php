<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Errors */

$this->title = 'Create Errors';
$this->params['breadcrumbs'][] = ['label' => 'Errors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="errors-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
