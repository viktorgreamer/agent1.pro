<?php
/* @var $this yii\web\View */
?>
<h1>Список доступных тестов</h1>

<p>

</p>

<?php
foreach ($actions as $action) {
    echo "<br>".\yii\helpers\Html::a($action,\yii\helpers\Url::to($action), ['target' => '_blank']);

}
