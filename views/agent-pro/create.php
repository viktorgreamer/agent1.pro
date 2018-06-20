<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AgentPro */

$this->title = 'Create Agent Pro';
$this->params['breadcrumbs'][] = ['label' => 'Agent Pros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agent-pro-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
