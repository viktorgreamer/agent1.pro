<?php
use app\models\Sale;

/* @var $this yii\web\View */
/* @var $address \app\models\Addresses */

echo  $address->address." ".$address->floorcount." этажный " .Sale::HOUSE_TYPES[$address->house_type]." дом ".$address->year." года постройки";

echo "<br>".\app\models\Tags::render($address->tags);