<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Selectors */

$this->title = 'Create Selectors';
$this->params['breadcrumbs'][] = ['label' => 'Selectors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="selectors-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
