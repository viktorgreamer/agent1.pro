<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Blabla */

$this->title = Yii::t('app', 'Create Blabla');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blablas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blabla-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
