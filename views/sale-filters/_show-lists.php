<?php
use app\components\Mdb;
?>
<?php
$salefilter = new \app\models\SaleFilters();

?>
<?php echo Mdb::ActiveSelect($salefilter, 'id', $salefilters); ?>