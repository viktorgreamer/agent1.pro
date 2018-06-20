<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ControlParsing */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Control Parsing',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Control Parsings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="control-parsing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
