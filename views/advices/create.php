<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Advices */

$this->title = Yii::t('app', 'Create Advices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Advices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="advices-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
