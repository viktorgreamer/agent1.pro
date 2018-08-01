<?php
use app\models\Renders;

/* @var $this yii\web\View */


$session = Yii::$app->session;
$module = $session->get('module'); ?>
<?= \app\components\YandexMaps::widget([
    'sales' => $sales, // передаем sales
    'module' => $module, // передаем текущий модуль (город в котором работаем, чтобы получить координаты центра и zoom по умолчанию
    'controls' => true, // кповки удаления или добавления в определенные списки
    'salefilter' => $salefilter,
    'isEditablePolygon' => true // добавление кнопок для редактирования полигона
]); ?>

<?  // echo   \Yii::$app->view->render('@app/views/sale/_mini_sale_similar', ['sales' => $sales, 'contacts' => true, 'salefilter' => $salefilter, 'controls' => true]); ?>
