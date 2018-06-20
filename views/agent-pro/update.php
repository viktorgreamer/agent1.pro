<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AgentPro */

$this->title = 'Update Agent Pro: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agent Pros', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="agent-pro-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
