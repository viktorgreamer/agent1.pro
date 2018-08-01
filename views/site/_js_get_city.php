<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 26.07.2018
 * Time: 16:06
 */

?>
<script src="http://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
<div id="user-city"></div>
<div id="user-region"></div>
<div id="user-country"></div>
<?php

$script = <<< JS
window.onload = function () {
      jQuery("#user-city").text(ymaps.geolocation.city);
      jQuery("#user-region").text(ymaps.geolocation.region);
      jQuery("#user-country").text(ymaps.geolocation.country);
}
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>

