<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SaleSimilar */

$this->title = Yii::t('app', 'Create Sale Similar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sale Similars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-similar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
