<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ControlParsing */

$this->title = Yii::t('app', 'Create Control Parsing');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Control Parsings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="control-parsing-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
