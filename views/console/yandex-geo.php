<?php
echo "<br>";
echo "число новых адресов" . $countNewAddresses;
?>



<?


echo $result . "<br>";
if ($countGeocode) {

    echo '<div style="margin-top:1em">Всего обработано адресов: ' . $countGeocode . '</div>';

    if ($countGeocodeFault) {

        echo '<div style="color:red">Не удалось прогеокодировать: ' . $countGeocodeFault . '</div>';

    }

} else {

    echo '<div>Таблица с адресами пуста.</div>';

}
echo $message;


?>


