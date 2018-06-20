<?php
use app\components\Mdb;
use app\models\Sale;
use yii\db\Expression;
$session = Yii::$app->session;
$module = $session->get('module');


/* @var $this yii\web\View */

$query = Sale::find()
    ->from(['s' => Sale::tableName()])
    ->joinWith(['agent AS agent'])
    ->joinWith(['addresses AS address'])
    //->where(['id_address'=> 1])
    ->orderBy(new Expression('rand()'))
    ->limit(10);
$query_list = clone $query;
$sales = $query->all();
?>

<?= \app\components\YandexMaps::widget(['sales' => $sales, 'module' => $module]); ?>

<?= $this->render('@app/views/sale/_mini_sale_similar', ['sales' => $query_list->all(), 'contacts' => true]); ?>