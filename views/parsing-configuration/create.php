<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ParsingConfiguration */

$this->title = 'Create Parsing Configuration';
$this->params['breadcrumbs'][] = ['label' => 'Parsing Configurations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parsing-configuration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
