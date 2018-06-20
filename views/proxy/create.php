<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Proxy */

$this->title = Yii::t('app', 'Create Proxy');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Proxies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="proxy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
