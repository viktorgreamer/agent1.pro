<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Modulecontrol */

$this->title = 'Create Modulecontrol';
$this->params['breadcrumbs'][] = ['label' => 'Modulecontrols', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modulecontrol-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
