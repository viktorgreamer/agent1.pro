<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\WdCookies */

$this->title = 'Create Wd Cookies';
$this->params['breadcrumbs'][] = ['label' => 'Wd Cookies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wd-cookies-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
