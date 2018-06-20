<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\controllers;

use app\components\Mdb;
use app\components\MdbActiveSelect;
use app\components\SaleWidget;
use app\models\AddressesSearch;
use app\models\AddressImport;
use app\models\Bla;
use app\models\MyChromeDriver;
use app\models\ParsingConfiguration;
use app\models\ParsingExtractionMethods;
use app\models\RenderSalefilters;
use app\models\SaleAnalitics;
use app\models\SaleAnaliticsAddress;
use app\models\Synchronization;
use app\models\Tags;
use yii\db\Command;
use yii;
use app\models\Agents;
use app\models\Sale;
use app\models\SaleHistory;
use yii\web\Controller;
use app\models\ParsingControl;
use app\models\SaleFilters;
use app\models\SaleSearch;
use app\models\Addresses;
use app\models\User;
use phpQuery;
use yii\helpers\ArrayHelper;
use yii\db\Migration;
use app\models\Control;
use app\models\SaleAnaliticsSameAddress;
use app\models\Parsing;
use app\models\Geocodetion;
use yii\db\Expression;
use yii\helpers\Html;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use app\models\ControlParsing;

class ConsoleController extends Controller
{

    public function actionMainer()
    {

      Control::mainScript();
        return $this->render('processing');
    }

    public function actionSyncNew()
    {
        info("тесты с '" . Synchronization::className() . "' статусами", 'success');
        echo "<hr>";

        $sync = new Synchronization();
        info("СОЗДАЕМ НОВУЮ МОДЕЛЬ", 'primery');
        $sync->renderStatuses();
        $sync->changingStatuses('NEW');
        $sync->renderStatuses();
        $sync->changingStatuses('PARSED');
        $sync->renderStatuses();
        $sync->geocodated = 1;
        $sync->changingStatuses('GEOCODATED');
        $sync->renderStatuses();
        if ($sync->geocodated != 9) $sync->changingStatuses('LOAD_ANALIZED');
        $sync->renderStatuses();
        $sync->changingStatuses('PROCESSED');
        $sync->renderStatuses();
        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();
        $sync->changingStatuses('MODERATED');
        $sync->renderStatuses();


        echo "<hr>";

        $sync->changingStatuses('ADDRESS_CHANGED');
        $sync->renderStatuses();
        $sync->changingStatuses('PARSED');
        $sync->renderStatuses();
        $sync->geocodated = 1;
        $sync->changingStatuses('GEOCODATED');
        $sync->renderStatuses();
        if ($sync->geocodated != 9) $sync->changingStatuses('LOAD_ANALIZED');
        $sync->changingStatuses('PROCESSED');
        $sync->renderStatuses();

        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();
        $sync->changingStatuses('MODERATED');
        $sync->renderStatuses();
        $sync->changingStatuses('SYNC');

        $sync->renderStatuses();


        echo "<hr>";
        $sync->changingStatuses('PRICE_CHANGED');
        $sync->renderStatuses();
        $sync->changingStatuses('PROCESSED');
        $sync->renderStatuses();
        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();

        echo "<hr>";

        $sync->changingStatuses('PRICE_CHANGED');
        $sync->renderStatuses();

        $sync->changingStatuses('PROCESSED');
        $sync->renderStatuses();
        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();


        return $this->render('processing');

    }

    public function actionSyncGeocodationError()
    {
        info("тесты с '" . Synchronization::className() . "' статусами", 'success');
        echo "<hr>";
        info("ПОСТУПИЛ НОВЫЙ ВАРИАНТ", 'primery');
        $sync = new Synchronization();
        info("создаем новую модель", 'info');
        $sync->renderStatuses();
        $sync->changingStatuses('NEW');
        $sync->renderStatuses();
        $sync->changingStatuses('PARSED');
        $sync->renderStatuses();
        $sync->geocodated = 9;
        $sync->changingStatuses('GEOCODATED');
        $sync->renderStatuses();
        if ($sync->geocodated != 9) $sync->changingStatuses('LOAD_ANALIZED');
        $sync->processed = 3;
        $sync->changingStatuses('PROCESSED');
        $sync->renderStatuses();
        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();
        $sync->changingStatuses('MODERATED');
        $sync->renderStatuses();

        $sync->changingStatuses('MANUAL_GEOCODATION');
        $sync->renderStatuses();
        $sync->load_analized = 3;
        $sync->changingStatuses('LOAD_ANALIZED');
        $sync->renderStatuses();
        $sync->sync = 3;
        $sync->changingStatuses('SYNC');
        $sync->renderStatuses();


        return $this->render('processing');

    }


    public function actionAggreratorsCheck()
    {

        $sale = Synchronization::findOne(27389);
        $sale->SalefiltersCheck();
        return $this->render('processing');
    }

    public function actionAutoloadTags()
    {
        $query = Synchronization::find()->where(['tags_autoload' => 0]);
        echo " count_lost=" . $query->count();
        $sales = $query->limit(50)->all();
        foreach ($sales as $sale) {
            $sale->AutoLoadTags();
            $sale->save();
        }
        return $this->render('processing');
    }


    public function actionTableGeneration()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {

            $prefix = $module->region;

            Yii::$app->db->createCommand("

CREATE TABLE IF NOT EXISTS `" . $prefix . "_addresses` (
  `id` int(11) NOT NULL,
  `coords_x` varchar(10) DEFAULT NULL COMMENT 'координата х',
  `coords_y` varchar(10) DEFAULT NULL COMMENT 'координата y',
  `address` varchar(256) DEFAULT NULL COMMENT 'текстовое название адреса возвращенное яндекс карты api',
  `street` varchar(50) DEFAULT NULL COMMENT 'название улицы',
  `house` int(3) DEFAULT '0' COMMENT 'номер дома',
  `hull` varchar(5) DEFAULT NULL COMMENT 'корпус',
  `locality` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `house_type` int(1) DEFAULT NULL,
  `floorcount` int(2) DEFAULT NULL COMMENT 'этажность дома',
  `address_string_variants` text COMMENT 'варианты названий адреса сериалтзованные в текст',
  `year` int(4) DEFAULT NULL COMMENT 'год постройки',
  `precision_yandex` varchar(10) DEFAULT NULL,
  `fullfilled` int(1) DEFAULT '0' COMMENT 'Все параметры по да',
  `status` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `" . $prefix . "_address_import` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `m2` varchar(12) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `floorcount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_phone_blacklist` (
  `id` int(11) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(1) NOT NULL,
  `count_ads` int(11) NOT NULL,
  `person` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale` (
  `id` int(11) NOT NULL,
  `date_start` int(11) DEFAULT NULL,
  `rooms_count` int(2) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `house_type` int(1) DEFAULT NULL COMMENT '1 пан, 2-кирп, 3- моноблок, 4- деревянный',
  `coords_x` float DEFAULT '0',
  `coords_y` float NOT NULL DEFAULT '0',
  `id_address` int(4) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `locality` varchar(200) DEFAULT NULL,
  `description` text,
  `floor` int(11) DEFAULT NULL,
  `floorcount` int(11) DEFAULT NULL,
  `id_sources` int(11) DEFAULT NULL,
  `grossarea` int(3) DEFAULT NULL,
  `images` text,
  `url` text,
  `status_unique_phone` int(1) DEFAULT '0',
  `load_analized` int(1) DEFAULT '0',
  `status_unique_date` int(1) DEFAULT '0',
  `status_blacklist2` tinyint(1) DEFAULT '0',
  `person` varchar(40),
  `id_irr_duplicate` text,
  `geolocated` int(1) DEFAULT '0',
  `processed` int(1) DEFAULT '0',
  `broken` int(1) DEFAULT '0',
  `average_price` int(10) DEFAULT '0',
  `average_price_count` int(6) DEFAULT '0',
  `average_price_address` int(10) DEFAULT '0',
  `average_price_address_count` int(3) DEFAULT '0',
  `average_price_same` int(10) NOT NULL DEFAULT '0',
  `average_price_same_count` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_analitics` (
  `id` int(5) NOT NULL,
  `rooms_count` int(2) NOT NULL,
  `grossarea` int(3) NOT NULL,
  `average_price` int(8) NOT NULL,
  `average_price_count` int(4) NOT NULL,
  `house_type` int(1) DEFAULT NULL,
  `floorcount` int(2) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `type_of_plan` int(1) DEFAULT NULL,
  `locality` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_analitics_address` (
  `id` int(5) NOT NULL,
  `id_address` int(4) DEFAULT NULL,
  `rooms_count` int(2) NOT NULL,
  `grossarea` int(3) NOT NULL,
  `average_price` int(8) NOT NULL,
  `average_price_count` int(4) NOT NULL,
  `house_type` int(1) DEFAULT NULL,
  `floorcount` int(2) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `type_of_plan` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_analitics_same_address` (
  `id` int(5) NOT NULL,
  `id_address` int(4) DEFAULT NULL,
  `house_type` int(1) DEFAULT NULL,
  `floorcount` int(2) DEFAULT NULL,
  `rooms_count` int(2) NOT NULL,
  `grossarea` int(3) NOT NULL,
  `average_price_400` int(10) DEFAULT NULL,
  `average_price_400_count` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;



CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_filters` (
  `id` int(6) NOT NULL,
  `user_id` int(4) DEFAULT NULL,
  `period_ads` int(3) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `rooms_count` varchar(30) DEFAULT NULL,
  `locality` varchar(200) DEFAULT NULL,
  `district` varchar(200) DEFAULT NULL,
  `polygon_text` text,
  `price_down` int(7) DEFAULT NULL,
  `price_up` int(11) DEFAULT NULL,
  `grossarea_down` int(11) DEFAULT NULL,
  `grossarea_up` int(11) DEFAULT NULL,
  `status_blacklist2` int(11) DEFAULT NULL,
  `date_of_ads` int(11) DEFAULT NULL,
  `floor_down` int(2) DEFAULT NULL,
  `floor_up` int(2) DEFAULT NULL,
  `floorcount_down` int(2) DEFAULT NULL,
  `floorcount_up` int(2) DEFAULT NULL,
  `not_last_floor` tinyint(1) DEFAULT NULL,
  `sort_by` int(1) DEFAULT NULL,
  `black_list_id` text,
  `white_list_id` text NOT NULL,
  `mail_inform` int(1) DEFAULT NULL,
  `sms_inform` int(1) DEFAULT NULL,
  `is_super_filter` tinyint(1) NOT NULL,
  `discount` int(2) NOT NULL,
  `date_start` int(2) NOT NULL,
  `date_finish` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_history` (
  `id` int(11) NOT NULL,
  `date_start` int(11) DEFAULT NULL,
  `rooms_count` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `phone1` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `house_type` int(1) DEFAULT NULL COMMENT '1 пан, 2-кирп, 3- моноблок, 4- деревянный',
  `coords_x` float DEFAULT '0',
  `coords_y` float NOT NULL DEFAULT '0',
  `id_address` int(4) DEFAULT NULL,
  `year` int(4) DEFAULT NULL,
  `locality` varchar(200) DEFAULT NULL,
  `description` text,
  `floor` int(11) DEFAULT NULL,
  `floorcount` int(11) DEFAULT NULL,
  `id_sources` int(11) DEFAULT NULL,
  `grossarea` int(3) DEFAULT NULL,
  `images` text,
  `url` text,
  `status_unique_phone` int(1) DEFAULT '0',
  `analized` int(1) DEFAULT '0',
  `status_unique_date` int(1) DEFAULT '0',
  `status_blacklist2` tinyint(1) DEFAULT '0',
  `person` varchar(40),
  `id_irr_duplicate` text,
  `broken` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `" . $prefix . "_sale_lists` (
  `id` int(6) NOT NULL,
  `user_id` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `list_of_ids` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



CREATE TABLE IF NOT EXISTS `" . $prefix . "_sms_api` (
  `id` int(6) NOT NULL,
  `id_sale` int(9) NOT NULL,
  `grossarea` int(3) NOT NULL,
  `rooms_count` int(2) NOT NULL,
  `text_sms` text COLLATE utf8_unicode_ci NOT NULL,
  `id_list` int(11) NOT NULL,
  `person` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `address` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `price` int(8) DEFAULT NULL,
  `user_id` int(4) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;


CREATE TABLE IF NOT EXISTS `" . $prefix . "_sms_not_to_send` (
  `id` int(6) NOT NULL,
  `phone` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;


ALTER TABLE `" . $prefix . "_addresses`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_address_import`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_phone_blacklist`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_analitics`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_analitics_address`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_analitics_same_address`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_filters`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_history`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sale_lists`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sms_api`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_sms_not_to_send`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `" . $prefix . "_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
  
  ALTER TABLE `" . $prefix . "_address_import` 
MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_phone_blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_sale_analitics`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_sale_analitics_address`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_sale_analitics_same_address`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_sale_filters`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `" . $prefix . "_sale_lists`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

ALTER TABLE `" . $prefix . "_sms_api`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

ALTER TABLE `" . $prefix . "_sms_not_to_send`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

")->execute();


        }
        return $this->render('index');
    }


    public function actionParsingAddresses()
    {
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            if ($module->status == 0) {
                $prefix = $module->region;
                $city = $module->region_rus;
                AddressImport::setTablePrefix($prefix);


                $string = file_get_contents("c:\\" . $prefix . ".json");
                $json_array = json_decode($string);
                echo "<pre>";
                var_dump($json_array->rows);
                echo "</pre>";
                /*  foreach ($json_array->rows as $row) {

                      echo "id=" . $row->rownumber . " " . $row->address . " " . $row->year . " " . $row->floors . " <a href=\"http://dom.mingkh.ru" . $row->url . "\"> url </a> </br>";


                      $import_address = new AddressImport;
                      $import_address->id = $row->rownumber;
                      $import_address->address = $row->address;
                      $import_address->floorcount = $row->floors;
                      $import_address->year = $row->year;
                      $import_address->url = "http://dom.mingkh.ru" . $row->url;
                      if (!$import_address->save()) var_dump($import_address->errors);
                      else echo $import_address->id . "success <br>";
                  }*/


                // $module->change_status(1);


            }
        }

        return $this->render('index');

    }


    // функция для импорта адресов
    public function actionImportAddresses()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            AddressImport::setTablePrefix($prefix);
            Addresses::setTablePrefix($prefix);

            $importing_addresses = AddressImport::find()
                ->all();

            foreach ($importing_addresses as $importing_address) {
                if (!Addresses::findOne($importing_address->id)) {
                    $new_address = new Addresses;
                    $new_address->id = $importing_address->id;
                    $new_address->coords_x = '';
                    $new_address->coords_y = '';
                    $new_address->street = '';
                    $new_address->house = '';
                    $new_address->hull = '';
                    $new_address->locality = '';
                    $new_address->district = '';
                    $new_address->house_type = 0;
                    $new_address->address_string_variants = '';
                    $new_address->precision_yandex = '';
                    $new_address->address_string_variants = '';
                    $new_address->fullfilled = 0;
                    $new_address->status = 0;
                    $new_address->address = $importing_address->address;
                    $new_address->year = $importing_address->year;
                    $new_address->floorcount = $importing_address->floorcount;
                    if ($new_address->save()) {
                        echo "<br> успешно импортирован адрес" . $importing_address->address;

                    }
                } //echo $importing_address->id." ранее импортирован";

            }
        }
    }

    // функция для заполнения таблицы с алресами дополнительными параметами
    public function actionFillingAddresses()
    {

        $module = Control::findOne(2);

        // foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
        $module->filling_addresses();
        // }


        return $this->render('yandex-geo');


    }


    public
    function actionParsingHistory()
    {

        $count_yes_sale = 0;
        $count_update_sale = 0;
        $count_no_sale = 0;
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            if ($module->status > 1) {
                SaleHistory::setTablePrefix($prefix);
                Sale::setTablePrefix($prefix);
                Addresses::setTablePrefix($prefix);
                SaleAnaliticsAddress::setTablePrefix($prefix);
                SaleAnalitics::setTablePrefix($prefix);
                for ($i = 1; $i <= 2; $i++) {
                    $count = 0;
                    // блог вычисления последнего парсинга
                    $parsing_control = new ParsingControl();
                    $mimute_interval = round((time() - $parsing_control->start_time($prefix, true)) / 60);
                    echo "<br> interval =";
                    // переходим в систему времени ads-api
                    $max_step = $module->max_step;
                    // ночной шаг делаем больше чтобы не тратить время !!! как вариант исключить вообще
                    if (in_array(date("H", $parsing_control->start_time($prefix, true)), [1, 2, 3, 4, 5, 6, 7])) {
                        $max_step = 450;
                        echo " max step 450";
                    }
                    $ads_api_start_time = $mimute_interval + 31;
                    if ($ads_api_start_time > $max_step) {
                        $ads_api_end_time = $ads_api_start_time - $max_step;
                    } else {
                        if ($module->status == 2) {
                            // меняем статус на  обработку параметров исходя из данных собранных из истории
                            $module->change_status(3);
                        }

                        $ads_api_end_time = 31;
                    }

                    $ads_api_start_time_string = "PT" . $ads_api_start_time . "M";
                    $ads_api_end_time_string = "PT" . $ads_api_end_time . "M";
                    echo $ads_api_start_time_string . " to " . $ads_api_end_time_string;


                    $parsing_control->date = time() - ($ads_api_end_time - 31) * 60;
                    $date = new \DateTime();    //текущее время
                    $date1 = clone $date;      //копируем объект даты
                    $date1->sub(new \DateInterval($ads_api_start_time_string));
                    $date2 = clone $date;      //копируем объект даты
                    $date2->sub(new \DateInterval($ads_api_end_time_string));

                    $login = 'usluginov53@gmail.com';
                    $token = "6fe5e160a50c84d65e46e2255dbde717";
                    $str = file_get_contents("http://ads-api.ru/main/api?user=" . urlencode($login) . "&token=" . urlencode($token)
                        . "&city=" . urlencode($city)
                        . "&category_id=2"
                        . "&nedvigimost_type=1"
                        . "&date1=" . urlencode($date1->format('Y-m-d H:i:s'))
                        . "&date2=" . urlencode($date2->format('Y-m-d H:i:s')));

//парсим ответ как json

                    $json = json_decode($str);
                    echo "<br>";
                    echo " квартир в городе <b> " . $city . "</b> ";
                    $count = count($json->data);
                    echo $count;
                    echo "<br>";


//проходим по всем объявлениям
                    if ($count != 0) {


                        foreach ($json->data as $adv)     //$adv - объект объявления
                        {  // отметаем посуточные варианты
                            if ($adv->price > 3000) {
                                $sale_history = New SaleHistory();
                                if ($module->status == 7) {

                                    switch ($sale_history->ParsingSaleHistoryDaily($adv)) {
                                        case "save" :
                                            $count_yes_sale++;
                                        case "update" :
                                            $count_update_sale++;
                                        case "error" :
                                            $count_no_sale++;
                                    };
                                } else {
                                    switch ($sale_history->ParsingSaleHistory($adv)) {
                                        case "save" :
                                            $count_yes_sale++;
                                        case "update" :
                                            $count_update_sale++;
                                        case "error" :
                                            $count_no_sale++;
                                    };
                                }


                            }
                        }
                    }
// парсинг комнат
                    $str = file_get_contents("http://ads-api.ru/main/api?user=" . urlencode($login) . "&token=" . urlencode($token)
                        . "&city=" . urlencode($city)
                        . "&category_id=2"
                        . "&nedvigimost_type=2"
                        . "&date1=" . urlencode($date1->format('Y-m-d H:i:s'))
                        . "&date2=" . urlencode($date2->format('Y-m-d H:i:s')));

//парсим ответ как json
//
//            echo "<div class='container'>";
//            echo "<table class='table table-striped'>";

                    $json = json_decode($str);
                    $count = count($json->data);
                    echo " комнаты в городе " . $city;

                    echo $count;

//проходим по всем объявлениям
                    if ($count != 0) {

//проходим по всем объявлениям
                        foreach ($json->data as $adv)     //$adv - объект объявления
                        {  // отметаем посуточные варианты
                            if ($adv->price > 3000) {
                                $sale_history = New SaleHistory();
                                switch ($sale_history->ParsingSaleHistory($adv, true)) {
                                    case "save" :
                                        $count_yes_sale++;
                                    case "update" :
                                        $count_update_sale++;
                                    case "error" :
                                        $count_no_sale++;
                                };

                            }
                        }
                    }


//            echo "</table>";
//            echo "</div>";

                    $log_rec = "Insert S" . $count_yes_sale . " R" . $count_yes_rent . " B" . $count_yes_buy . " RC" . $count_yes_rent_clients;
                    $log_rec = $log_rec . "Update S" . $count_update_sale . " R" . $count_update_rent . " B" . $count_update_buy . " RC" . $count_update_rent_clients;
                    $log_rec = $log_rec . "Error S" . $count_no_sale . " R" . $count_no_rent . " B" . $count_no_buy . " RC" . $count_no_rent_clients . "<br>";

                    $parsing_control->log = $log_rec;
                    $parsing_control->city_module = $prefix;
                    $parsing_control->name_table = $prefix . "_sale_history";
                    $parsing_control->save();
                    // не проходим дргуие циклы если подошли к концу
                    if ($ads_api_end_time < 33) break;

                }
                // делаем обработку на предмет анализа телефона
                PhoneBlacklist::setTablePrefix($prefix);

                $sales_for_processing = Sale::find()
                    ->select(['phone1', 'id', 'person'])
                    ->where(['processed' => 0])
                    ->limit(500)
                    ->all();
                echo " lost=" . count($sales_for_processing);

                if ($sales_for_processing) {
                    foreach ($sales_for_processing as $sale_for_processing) {
                        // если есть в phoneblacklist
                        if (!$sale_for_processing->is_in_phoneblacklist()) {
                            // если есть в ранее обработанных объявлениях
                            if (!$sale_for_processing->is_in_proccessed_sale()) {
                                // если нет ни там не там то обновляем как объявление уникальное
                                $sale_for_processing->update_as_homekeeper_sale();
                            }


                        }

                        $sale_for_processing->processed = 1;
                        $sale_for_processing->save();


                    }


                }
            }


        }
        return $this->render('index', ['count_no_rent' => $count_no_rent,
            'count_no_sale' => $count_no_sale,
            'count_no_buy' => $count_no_buy,
            'count_no_rent_clients' => $count_no_rent_clients,
            'count_yes_sale' => $count_yes_sale,
            'count_yes_rent' => $count_yes_rent,
            'count_yes_buy' => $count_yes_buy,
            'count_yes_rent_clients' => $count_yes_rent_clients,
            'count_update_sale' => $count_update_sale,
            'count_update_rent' => $count_update_rent,
            'count_update_buy' => $count_update_buy,
            'count_update_rent_cliens' => $count_update_rent_clients]);
    }

    // когда нми будет полность обработана и заполнена таблица addresses и salehistory дел
// делаем вставку пропущенных параметров
    public
    function actionParseMissingParameters()
    {
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            Sale::setTablePrefix($prefix);
            SaleHistory::setTablePrefix($prefix);

            Addresses::setTablePrefix($prefix);
// вставляем все гле пропущен house_type
            $sale_missing_the_house_type = Sale::find()
                ->where(['house_type' => 0])
                ->limit(200)
                ->all();

            foreach ($sale_missing_the_house_type as $item2) {
                $item2->parse_missing_house_type_parameters();

            }


            // вставляем все гле пропущен floorcount
            $sale_missing_the_floorcount = Sale::find()
                ->where(['floorcount' => 0])
                ->limit(200)
                ->all();
            foreach ($sale_missing_the_floorcount as $item2) {
                $item2->parse_missing_floorcount_parameters();


            }


            $salehistory_missing_the_house_type = SaleHistory::find()
                ->where(['house_type' => 0])
                ->limit(200)
                ->all();

            foreach ($salehistory_missing_the_house_type as $item2) {
                $item2->parse_missing_house_type_parameters();

            }


            // вставляем все гле пропущен floorcount
            $salehistory_missing_the_floorcount = SaleHistory::find()
                ->where(['floorcount' => 0])
                ->limit(200)
                ->all();
            foreach ($salehistory_missing_the_floorcount as $item2) {
                $item2->parse_missing_floorcount_parameters();


            }

            $sale_missing_the_year = Sale::find()
                ->where(['year' => null])
                ->limit(5000)
                ->all();

            foreach ($sale_missing_the_year as $item2) {
                $item2->import_year();

            }


            $sale_missing_the_year = SaleHistory::find()
                ->where(['year' => null])
                ->limit(5000)
                ->all();

            foreach ($sale_missing_the_year as $item2) {
                $item2->import_year();

            }
        }


        return $this->render('yandex-geo');
    }

    public function actionTryToFindSqure()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            SaleHistory::setTablePrefix($prefix);
            Addresses::setTablePrefix($prefix);

            $edit_sale = SaleHistory::find()
                ->where(['grossarea' => 0])
                ->andwhere(['<>', 'id_address', 0])
                ->andwhere(['analized' => 0])
                // ->andWhere(['>', 'date_start', time() - 60 * 60 * 24 * 2])
                ->limit(500)
                ->all();
            echo SaleHistory::find()
                ->where(['grossarea' => 0])
                ->andwhere(['<>', 'id_address', 0])
                ->andwhere(['analized' => 0])
                // ->andWhere(['>', 'date_start', time() - 60 * 60 * 24 * 2])
                ->count();

            if ($edit_sale) {
                foreach ($edit_sale as $item) {
// попытка поиска аналогичных объявлдений
                    $salehistory_for_analize = SaleHistory::findOne($item->id);
                    // echo "<br> объявления этого агента " . $salehistory_for_analize->try_to_find_same_phone_and_id_address();
                    $found_squre = $salehistory_for_analize->try_to_find_grossarea_in_description();
                    echo "S =" . $found_squre;
                    $fount_grossarea_alt = $salehistory_for_analize->try_to_find_same_squre_average();

                    if ($found_squre) {
                        $salehistory_for_analize->grossarea = $found_squre;
                        // присвоили пложащь
                        $salehistory_for_analize->analized = 1;
                        $salehistory_for_analize->save();

                    } elseif ($fount_grossarea_alt) {
                        $salehistory_for_analize->grossarea = $fount_grossarea_alt;
                        echo "S=" . $fount_grossarea_alt;
                        $salehistory_for_analize->analized = 1;
                        $salehistory_for_analize->save();
                    } else {
                        // $salehistory_for_analize->phpQueryParsingGroosarea();

                        // пока остался без пложади
                        $salehistory_for_analize->analized = 1;
                        $salehistory_for_analize->save();
                    }

                }
            }
        }
        return $this->render('index');
    }

    public function actionTryToFindYear()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент
        $prefix = $session->get('city_module');
        $city = $session->get('city');
        SaleHistory::setTablePrefix($prefix);
        Addresses::setTablePrefix($prefix);

        $edit_addresses = Addresses::find()
            ->andwhere(['year' => 0])
            ->andwhere(['locality' => $city])
            ->andwhere(['>', 'floorcount', 3])
            ->andWhere(['in', 'precision_yandex', ['exact']])
            // ->andWhere(['>', 'date_start', time() - 60 * 60 * 24 * 2])
            ->limit(500)
            ->orderBy('street, house')
            ->all();
        echo Addresses::find()
            ->andwhere(['year' => 0])
            ->andwhere(['locality' => $city])
            ->andwhere(['>', 'floorcount', 2])
            ->andWhere(['in', 'precision_yandex', ['exact', 'number']])
            ->count();

        if ($edit_addresses) {
            foreach ($edit_addresses as $address) {
// попытка поиска аналогичных объявлдений
                $message .= "<br> <a href='../addresses/update?id=" . $address->id . "' class='btn btn-success'> Update </a>";
                $message .= "" . $address->address . " " . $address->floorcount . " эт";
                $edit_addresses = Addresses::findOne($address->id);
                $response = $edit_addresses->try_to_find_year();
                // var_dump($response);
                $message .= $response['message'];


            }

        }


        return $this->render('addresses', [
            'message' => $message
        ]);
    }

    public function actionTryToFindFloorcount()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент
        $prefix = $session->get('city_module');
        $city = $session->get('city');
        SaleHistory::setTablePrefix($prefix);
        Addresses::setTablePrefix($prefix);

        $edit_addresses = Addresses::find()
            ->andwhere(['floorcount' => 0])
            ->andwhere(['locality' => $city])
            //  ->andwhere(['>', 'floorcount', 3])
            ->andWhere(['in', 'precision_yandex', ['exact']])
            // ->andWhere(['>', 'date_start', time() - 60 * 60 * 24 * 2])
            ->limit(500)
            ->orderBy('street, house')
            ->all();
        echo Addresses::find()
            ->andwhere(['floorcount' => 0])
            ->andwhere(['locality' => $city])
            //  ->andwhere(['>', 'floorcount', 2])
            ->andWhere(['in', 'precision_yandex', ['exact', 'number']])
            ->count();

        if ($edit_addresses) {
            foreach ($edit_addresses as $address) {
// попытка поиска аналогичных объявлдений
                $message .= "<br> <a href='../addresses/update?id=" . $address->id . "' class='btn btn-success'> Update </a>";
                $message .= "" . $address->address . " " . $address->floorcount . " эт";
                $edit_addresses = Addresses::findOne($address->id);
                $message .= $edit_addresses->try_to_find_floorcount_in_description();
                // var_dump($response);
                // $message .= $response['message'];


            }

        }


        return $this->render('addresses', [
            'message' => $message
        ]);
    }

    public function actionTryToFindHouseType()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент
        $prefix = $session->get('city_module');
        $city = $session->get('city');
        SaleHistory::setTablePrefix($prefix);
        Addresses::setTablePrefix($prefix);

        $edit_addresses = Addresses::find()
            ->andwhere(['house_type' => 0])
            ->andwhere(['locality' => $city])
            //  ->andwhere(['>', 'floorcount', 3])
            ->andWhere(['in', 'precision_yandex', ['exact']])
            // ->andWhere(['>', 'date_start', time() - 60 * 60 * 24 * 2])
            ->limit(100)
            ->orderBy('street, house')
            ->all();
        echo Addresses::find()
            ->andwhere(['house_type' => 0])
            ->andwhere(['locality' => $city])
            //  ->andwhere(['>', 'floorcount', 2])
            ->andWhere(['in', 'precision_yandex', ['exact', 'number']])
            ->count();

        if ($edit_addresses) {
            foreach ($edit_addresses as $address) {
// попытка поиска аналогичных объявлдений
                $message .= "<br> <a href='../addresses/update?id=" . $address->id . "' class='btn btn-success'> Update </a>";
                $message .= "" . $address->address . " " . $address->floorcount . " эт";
                $edit_addresses = Addresses::findOne($address->id);
                $message .= $edit_addresses->try_to_find_floorcount_in_description();
                // var_dump($response);
                // $message .= $response['message'];


            }

        }


        return $this->render('addresses', [
            'message' => $message
        ]);
    }

    public function actionShowAddresses()
    {


        $addresses = Addresses::find()
            // ->where(['year' => 0])
            ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
            ->limit(10)
            ->all();

        return $this->render('show-addresses', [
            'addresses' => $addresses
        ]);


    }

    public function actionShowSameAddresses()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент

        $city = $session->get('city');
        $addresses1 = Addresses::find()
            ->where(['locality' => $city])
            ->andwhere(['year' => 0])
            ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
            ->limit(50)
            ->all();
        $addresses_ids = [];
        foreach ($addresses1 as $address1) {
            $addresses2 = Addresses::find()
                ->where(['locality' => $city])
                //->where(['floorcount' => $address1->floorcount])
                // ->andwhere(['house_type' => $address1->house_type])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();
            foreach ($addresses2 as $address2) {
                if ($address1->is_in_this_radius($address2, 200)) {

                    array_push($addresses_ids, $address2->id, $address1->id);
                }
            }
        }

        $addresses = Addresses::find()
            ->where(['in', 'id', $addresses_ids])
            ->all();

        return $this->render('show-addresses', [
            'addresses' => $addresses
        ]);


    }

    public function actionShowSameAddressesFloorcount()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент

        $city = $session->get('city');
        $addresses1 = Addresses::find()
            ->where(['locality' => $city])
            ->andwhere(['floorcount' => 0])
            ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
            ->limit(50)
            ->all();
        $addresses_ids = [];
        foreach ($addresses1 as $address1) {
            $addresses2 = Addresses::find()
                ->where(['locality' => $city])
                //->where(['floorcount' => $address1->floorcount])
                // ->andwhere(['house_type' => $address1->house_type])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();
            foreach ($addresses2 as $address2) {
                if ($address1->is_in_this_radius($address2, 200)) {

                    array_push($addresses_ids, $address2->id, $address1->id);
                }
            }
        }

        $addresses = Addresses::find()
            ->where(['in', 'id', $addresses_ids])
            ->all();

        return $this->render('show-addresses-floorcount', [
            'addresses' => $addresses
        ]);


    }

    public function actionShowSameAddressesHousetype()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент

        $city = $session->get('city');
        $addresses1 = Addresses::find()
            ->where(['locality' => $city])
            ->andwhere(['house_type' => 0])
            ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
            ->limit(50)
            ->all();
        $addresses_ids = [];
        foreach ($addresses1 as $address1) {
            $addresses2 = Addresses::find()
                ->where(['locality' => $city])
                //->where(['floorcount' => $address1->floorcount])
                // ->andwhere(['house_type' => $address1->house_type])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();
            foreach ($addresses2 as $address2) {
                if ($address1->is_in_this_radius($address2, 200)) {

                    array_push($addresses_ids, $address2->id, $address1->id);
                }
            }
        }

        $addresses = Addresses::find()
            ->where(['in', 'id', $addresses_ids])
            ->all();

        return $this->render('show-addresses-house_type', [
            'addresses' => $addresses
        ]);


    }

    public function actionShowSameAddressesNumbers()
    {

        $session = Yii::$app->session;

// присваиваем определяем регион в котором работаем на данный момент

        $city = $session->get('city');
        $addresses1 = Addresses::find()
            ->where(['locality' => $city])
            ->andwhere(['precision_yandex' => 'number'])
            ->limit(100)
            ->all();
        $addresses_ids = [];
        foreach ($addresses1 as $address1) {
            $addresses2 = Addresses::find()
                ->where(['locality' => $city])
                ->where(['street' => $address1->street])
                ->andwhere(['house' => $address1->house])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();
            foreach ($addresses2 as $address2) {
                if ($address1->is_in_this_radius($address2, 200)) {

                    array_push($addresses_ids, $address2->id, $address1->id);
                }
            }
        }

        $addresses = Addresses::find()
            ->where(['in', 'id', $addresses_ids])
            ->all();

        return $this->render('show-addresses-numbers', [
            'addresses' => $addresses
        ]);


    }


    public
    function actionAnalitics()
    {
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            echo $city;
            SaleHistory::setTablePrefix($prefix);
            SaleAnaliticsAddress::setTablePrefix($prefix);
            SaleAnaliticsSameAddress::setTablePrefix($prefix);
            SaleAnalitics::setTablePrefix($prefix);
            PhoneBlacklist::setTablePrefix($prefix);
            Addresses::setTablePrefix($prefix);

            $edit_sale = SaleHistory::find()
                ->select(['id'])
                ->where(['in', 'analized', [0, 1, 2, 3, 4]])
                ->limit(100)
                ->all();
            echo " lost" . SaleHistory::find()->where(['in', 'analized', [0, 1, 2, 3, 4]])->count();

            if ($edit_sale) {
                foreach ($edit_sale as $item) {
                    $edit_sale_item = SaleHistory::find()
                        ->select(['id', 'rooms_count', 'id_address', 'floorcount', 'grossarea', 'house_type', 'locality', 'year'])
                        ->where(['id' => $item['id']])
                        ->one();

                    if ($edit_sale_item->is_full()) {
                        // создаем статистику по адресу
                        $edit_sale_item->create_or_update_statistic_address_start();

                        // создаем статистику по данным параметрам
                        $edit_sale_item->create_or_update_statistic_start();

                        $edit_sale_item->create_or_update_statistic_same_address_start();


                    }
                    $edit_sale_item->analized = 4;
                    $edit_sale_item->save();

                }
            }
        }


        return $this->render('processing');
    }

    public function actionSaleLoadStatistic()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;

            Sale::setTablePrefix($prefix);
            SaleAnalitics::setTablePrefix($prefix);
            SaleAnaliticsAddress::setTablePrefix($prefix);
            Addresses::setTablePrefix($prefix);

            $sales_for_analise = Sale::find()
                ->where(['>', 'date_start', time() - 15 * 24 * 60 * 60])
                ->andwhere(['status_unique_advert' => 1])
                ->limit(500)
                ->all();

            foreach ($sales_for_analise as $sale_for_analise) {

                if ($sale_for_analise->is_full()) {
                    $sale_for_analise->load_statistic();

                }
                $sale_for_analise->status_unique_advert = 2;
                $sale_for_analise->save();

            }
        }
        return $this->render('processing');
    }

    public function actionMain()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            $starttime = time();

            if ($module->status == 0) {
                $module->table_generation();
                $module->parsing_and_import_addresses();
                $module->change_status(1);
            }
            if ($module->status == 1) {
                $module->filling_addresses();

            }
            if ($module->status == 2) {
                sleep(10);
                $module->parsing_history_start();
            }

            if ($module->status == 3) {
                echo " parssing";
                $module->change_status(5);
                //  $this->redirect('parsing/detailed-sync');


            }

            if ($module->status == 5) {
                echo " geocodation";
                if (!$module->geocodetion()) $module->change_status(7);
            }
            /* if ($module->status == 6) {
                 //$module->change_status(8);
                 $module->load_sale_statistic();

             }*/

            if ($module->status == 7) {
                echo " load stat";
                if (!$module->load_sale_statistic()) $module->change_status(2);

            }


            $endtime = time();
            $interval = $endtime - $starttime;


        }
        return $this->render('processing');
    }


    public
    function actionGeocodetion()
    {


        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;


            if ($module->status != 9) $module->geocodetion();


            return $this->render('processing');
        }
    }

    public
    function actionConsoleAnalize()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;


            if ($module->status != 9) $module->analitics();


            return $this->render('processing');
        }
    }

    public
    function actionParseMissingParameter()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;


            if ($module->status != 9) {
                $log = $module->parse_missing_parameters();

                if ($log == $module->log) echo " <h3>ставлять больше нечего</h3>"; else $module->log($log);
                //  if (!$module->save()) my_var_dump($module->getErrors());
            }


            return $this->render('processing');
        }
    }

    public
    function actionMainDaily()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;
            $starttime = time();

            if ($module->status == 8) {
                if (!$module->parsing_history_start()) $module->change_status(10);
            }


            if ($module->status == 10) {
                $module->geocodetion();
            }


            if ($module->status == 5) {
                $module->try_to_find_squre();
            }
            if ($module->status == 6) {
                $module->analitics();

            }

            if ($module->status == 7) {
                $module->load_sale_statistic();

            }

            if ($module->status == 8) {
                $module->parsing_history_daily();

            }
            $endtime = time();
            $interval = $endtime - $starttime;


        }
        return $this->render('processing');
    }

    public
    function actionExportYearsFromYandex()
    {
        $modules = Control::find()
            ->where(['<>', 'status', 9])
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            $prefix = $module->region;
            $city = $module->region_rus;


            Addresses::setTablePrefix($prefix);

            $missed_years_address = Addresses::find()
                ->where(['year' => 0])
                ->andwhere(['locality' => $city])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();
            echo "<br> число адресов где пропущен year" . count($missed_years_address);

            foreach ($missed_years_address as $address) {
                $yandex_address = Parsing::find()
                    ->where(['<>', 'year', 0])
                    ->andWhere(['coords_x' => $address->coords_x])
                    ->andwhere(['coords_y' => $address->coords_y])
                    ->one();
                if ($yandex_address->year != 0) {
                    echo "удалось найти пропущенный год постройки";
                    echo "<br> в адресе" . $address->address . " присволи год" . $yandex_address->year . " <br>";
                    $address->set_year($yandex_address->year);


                }

            }

            $missed_floorcount_address = Addresses::find()
                ->where(['floorcount' => 0])
                ->andwhere(['locality' => $city])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();

            echo "<br> число адресов где пропущен floorcount" . count($missed_floorcount_address);


            foreach ($missed_floorcount_address as $address) {
                $parsing_address = Parsing::find()
                    ->where(['<>', 'floorcount', 0])
                    ->where(['coords_x' => $address->coords_x])
                    ->andwhere(['coords_y' => $address->coords_y])
                    ->one();
                if ($yandex_address->floorcount != 0) {
                    echo "удалось найти пропущенный параметр этажность";
                    echo "<br> в адресе" . $address->address . " присволи этажность" . $yandex_address->floorcount . " <br>";
                    $address->set_floorcount($parsing_address->floorcount);


                }

            }


            $missed_house_type_address = Addresses::find()
                ->where(['house_type' => 0])
                ->andwhere(['locality' => $city])
                ->andwhere(['in', 'precision_yandex', ['exact', 'number']])
                ->all();

            echo "<br> число адресов где пропущен house_type" . count($missed_house_type_address);

            foreach ($missed_house_type_address as $address) {
                $parsing_address = Parsing::find()
                    ->where(['<>', 'house_type', 0])
                    ->where(['coords_x' => $address->coords_x])
                    ->andwhere(['coords_y' => $address->coords_y])
                    ->one();
                if ($yandex_address->house_type != 0) {
                    echo "удалось найти пропущенный параметр тип дома";
                    echo "<br> в адресе" . $address->address . " присволи тип дома" . $yandex_address->house_type . " <br>";
                    $address->set_house_type($parsing_address->house_type);


                }

            }


        }
    }


    public
    function actionPrices()
    {
        $prices = [1400, 1500, 1568, 2000, 500, 5000, 1560, 1700, 1300, 1450, 1250, 1500, 1550, 1460, 1300];
        asort($prices);
        echo implode(",", $prices);
        $new_prices = $prices;
        $average = round(array_sum($prices) / count($prices));
        echo " cреднее число равно" . $average;
        foreach ($prices as $price) {
            if (($price > $average) or ($price * 1.8 < $average)) {
                echo " - " . $price;
                unset($new_prices[array_search($price, $prices)]);
                //  echo "-  удалили его";
            }
        }
        $average = round(array_sum($new_prices) / count($new_prices));
        echo "<br>новое cреднее число равно" . $average;
        echo "<br>" . implode(",", $new_prices);


        asort($new_prices);
        $count = count($new_prices);
        echo "<br>" . implode(",", $new_prices);
        echo "<br> кол-во элементов в массиве:" . $count;
        echo "<br> брем только 30 % цен нижних цена в новом массиве" . round(0.3 * $count);
        $new_prices = array_slice($new_prices, 0, round(0.3 * $count));
        echo "<br> новый массив после отрезки бесполезных вариантов : " . implode(",", $new_prices);
        $average = round(array_sum($new_prices) / count($new_prices));
        echo "<br>новое cреднее число равно" . $average;


        return $this->render('charts', compact(['prices', 'average', 'new_prices']));


    }

    public
    function actionPricesSale()
    {
        $sale = Sale::find()
            ->where(['rooms_count' => 1])
            // ->andWhere(['between', 'price', 1300000, 1500000])
            ->andWhere(['id' => 144260432])
            ->andWhere(['disactive' => 0])
            ->orderBy(new Expression('rand()'))
            ->one();
        if ($sale->is_full()) {
            // $SaleAnaliticsSameAddress = New SaleAnalitics();
            // $SaleAnaliticsSameAddress = New SaleAnaliticsSame();
            $SaleAnaliticsSameAddress = New SaleAnaliticsAddress();

            $array = $SaleAnaliticsSameAddress->ShowStatisticAddress($sale);

        }
        $sales = $sale;


        return $this->render('charts', compact(['sales', 'prices', 'average', 'new_prices', 'simple_average_price', 'sliced_average_price', 'message', 'sale', 'sales_history']));


    }


    public
    function actionAgentsAdsCounts()
    {

        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
            if ($module->region == "Pskov") {
                $prefix = $module->region;
                $city = $module->region_rus;
                $agents = Agents::find()
                    ->where(['status' => 0])
                    ->limit(10)
                    ->all();
                echo " lost=" . Agents::find()->where(['status' => 0])->count();
                foreach ($agents as $agent) {
                    $agent->irr_count = SaleHistory::find()->where(['id_sources' => 1])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->yandex_count = SaleHistory::find()->where(['id_sources' => 2])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->avito_count = SaleHistory::find()->where(['id_sources' => 3])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->youla_count = SaleHistory::find()->where(['id_sources' => 4])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->cian_count = SaleHistory::find()->where(['id_sources' => 5])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->status = 1;
                    if (($agent->irr_count > 1) or ($agent->yandex_count > 1) or ($agent->avito_count > 1) or ($agent->youla_count > 1) or ($agent->cian_count > 1)) $agent->person_type = 1;
                    $agent->count_ads = $agent->irr_count + $agent->yandex_count + $agent->avito_count + $agent->youla_count + $agent->cian_count;
                    $agent->irr_count = Sale::find()->where(['id_sources' => 1])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->yandex_count = Sale::find()->where(['id_sources' => 2])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->avito_count = Sale::find()->where(['id_sources' => 3])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->youla_count = Sale::find()->where(['id_sources' => 4])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->cian_count = Sale::find()->where(['id_sources' => 5])->andWhere(['phone1' => $agent->phone])->count();
                    $agent->save();

                }

            }
        }


        return $this->render('processing');
    }

    public
    function actionSaleSetStatusAgents()
    {
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
            if ($module->region == "Velikiy_Novgorod") {
                $prefix = $module->region;
                $city = $module->region_rus;

                $sales = Sale::find()
                    ->where(['status_unique_date' => 1])
                    ->limit(250)
                    ->all();
                echo " lost" . Sale::find()
                        ->where(['status_unique_date' => 1])->count();

                foreach ($sales as $sale) {
                    //  echo "<br> find phones".$sale->phone1." in agents base  --->>>>";
                    $agent = Agents::find()->where(['phone' => $sale->phone1])->one();
                    if ($agent) {
                        //   echo " yes, i've found the ".$agent->person;
                        $sale->status_blacklist2 = $agent->person_type;
                    } else {
                        //  echo " no, i haven't found anything;";
                        $sale->status_blacklist2 = 0;
                    }

                    if ($sale->status_blacklist2 == 0) echo " присвоили хозяина";
                    if ($sale->status_blacklist2 == 1) echo " присвоили агента";
                    $sale->status_unique_date = 2;
                    $sale->save();

                }
            }
        }
        return $this->render('processing');
    }


    public
    function actionTryToFindIrrDublicate()
    {
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            if ($module->status == 2) {
                $prefix = $module->region;
                $city = $module->region_rus;
                $starttime = time();
                SaleHistory::setTablePrefix($prefix);


                $edit_sale = SaleHistory::find()
                    ->where(['id_sources' => 1])
                    ->andwhere(['<>', 'status_unique_phone', 1])
                    // ->andWhere(['>', 'date_start', time() - 60*60*24*10])

                    ->orderBy(['id' => SORT_DESC])
                    ->limit(300)
                    ->all();
                echo " lost" . SaleHistory::find()
                        ->where(['id_sources' => 1])
                        ->andwhere(['<>', 'status_unique_phone', 1])
                        ->count();

                if ($edit_sale) {
                    foreach ($edit_sale as $item) {
                        $salehistory_irr_dublicate = SaleHistory::findOne($item['id']);
                        if ($salehistory_irr_dublicate) {
                            $salehistory_irr_dublicate->export_from_irr_dublicates_if_exists();
                            $salehistory_irr_dublicate->status_unique_phone = 1;
                            // данный метод применим только не тесте
                            if ($salehistory_irr_dublicate->save()) echo "success save"; else {
                                var_dump($salehistory_irr_dublicate->errors);
                            };
                        }
                    }
                }
                echo "salehistory нечего обрабатывать";
            }

        }


    }

    public function actionGeocodetionTest($id = 0)
    {
        if ($id != 0) {
            $sale = Sale::findOne($id);

            echo "<a href='" . $sale->url . "'>link</a> пытаемся прогеокодировать адрес: " . $sale->address;
            // $sale->house_type = 2;
            // $sale->floorcount = 5;
            $geocodetion = New Geocodetion();
            // загружает в модель sale данные после геокодирования
            $geocodetion->load_to_sale($sale, 'Великий Новгород', 'Новгородская область');
            $SaleWidget = new SaleWidget();
            $SaleWidget->Load($sale);

            $message .= "<br> " . $SaleWidget->admin_title;
            $message .= "<br> " . $SaleWidget->description;
            $message .= "<br> sale->geocodated" . $sale->geocodated;
            echo $message;
            if (!$sale->save()) my_var_dump($sale->getErrors());
        }
        return $this->render('processing');
    }

    public function actionGeocodetionTestCustom()
    {
        $message = '';
        $session = Yii::$app->session;
        $sale = new Sale();
        $sale->rooms_count = 1;
        $sale->floor = 3;
        $sale->description = 'Продам 2ккв квартиру по адресу Великий Новгород Б. Московская  д.110 ';
        $sale->address = 'Б. Московская';

        $sale->house_type = 2;
        $sale->floorcount = 9;
        $geocodetion = New Geocodetion();
        // загружает в модель sale данные после геокодирования
        $message .= " <hr>";
        $geocodetion->load_to_sale($sale, 'Великий Новгород', 'Новгородская область');
        $SaleWidget = new SaleWidget();
        $SaleWidget->Load($sale);
        $message .= $session->getFlash('xml_string');
        $message .= "<br>geocodation=" . $session->getFlash('geocodation');
        $message .= "<br>address_in_address=" . $session->getFlash('address_in_address');
        $message .= "<br>id_address_by_string=" . $session->getFlash('id_address_by_string');
        $message .= "<br>SearchAddressPattern" . $session->getFlash('SearchAddressPattern');
        $message .= "<br>address_in_description=" . $session->getFlash('address_by_description');

        $message .= "<br> " . $SaleWidget->admin_title;
        $message .= "<br> " . $SaleWidget->description;
        $message .= "<br> sale->geocodated" . $sale->geocodated;

        return $this->render('processing', compact('message'));
    }


    public
    function actionGeocodetionTest1()
    {
        $today = date("Y-m-d", time() + 10 * 24 * 60 * 60);
        $today .= 'T';
        $today .= date("H:i:s", time() + 10 * 24 * 60 * 60);
        echo $today;

        $id_addresses = Addresses::find()
            ->where(['precision_yandex' => 'number'])
            ->limit(250)
            ->all();
        $ids = [];
        foreach ($id_addresses as $id_address) {
            $ids[] = $id_address->id;
        }
        $sales = Sale::find()
            ->where(['in', 'id_address', $ids])
            ->andwhere(['geocodated' => 0])
            ->limit(100)
            ->all();
        foreach ($sales as $sale) {
            echo "<h4>" . $sale->address . "</h4> <a href='" . $sale->url . "'> link 2</a>";
            $geocodetion = New Geocodetion();
            // загружает в модель sale данные после геокодирования
            $geocodetion->load_to_sale($sale);
            echo " <hr>";
            echo " загружены данные геокодирования в модель sale" . $sale->address . " id _address" . $sale->id_address;
            echo "<br> floorcount=" . $sale->floorcount . " house_type" . $sale->house_type . " year" . $sale->year . " geocodated=" . $sale->geocodated;
            $sale->save();
        }


        return $this->render('processing');


    }

    public
    function actionParsing($id = 0)
    {
        $message = '';
        $session = Yii::$app->session;
        $modules = Control::find()
            ->all();

        foreach ($modules as $module) {
            if ($module->status <> 9) {
                // если задан конкретный id парсим только его
                if ($id == 0) {
                    $sales = Sale::find()
                        ->where(['in', 'id_sources', [1, 2, 3, 5]])
                        // ->andwhere(['id' => 143324777])
                        //  ->andwhere(['in','rooms_count' ,  [30]])
                        // ->andwhere(['grossarea' => 0])
                        ->andwhere(['in', 'disactive', [0, 1, 2]])
                        ->orderBy('date_of_check')
                        ->andwhere(['<', 'date_of_check', time() - 24 * 60 * 60])
                        //  ->andwhere(['<', 'date_start', time() - 100*24 * 60 * 60])
                        // ->where(['floor' => 0])
                        //->andwhere(['h' => 0])
                        ->limit(5)
                        ->all();
                    $message = " sale_count_lost=" . Sale::find()
                            ->where(['in', 'id_sources', [1, 2, 3, 5]])
                            //      ->andwhere(['in','rooms_count' ,  [30]])

                            ->andwhere(['in', 'disactive', [0, 1, 2]])
                            ->andwhere(['<', 'date_of_check', time() - 24 * 60 * 60])
                            // ->andwhere(['grossarea' => 0])
                            ->count();

                    foreach ($sales as $sale) {
                        $SaleWidget1 = new SaleWidget();
                        $SaleWidget1->Load($sale);


                        $message .= "<hr>";
                        $message .= Html::a('OneParsing', ['console/parsing', 'id' => $sale->id]);
                        $message .= "<br>";
                        $message .= Html::a('OneGeocodation', ['console/geocodetion-test', 'id' => $sale->id]);
                        $session->setFlash('sale1', $SaleWidget1->admin_title);
                        sleep(rand(0.2, 0.5));
                        $parsingModel = New Parsing();
                        if ($parsingModel->IsConnected()) {


                            $parsingModel->LoadParsedInfoToSale($sale, $module);
                            $SaleWidget2 = new SaleWidget();
                            $SaleWidget2->Load($sale);
                            $session->setFlash('sale2', $SaleWidget2->admin_title);

                            $sale->date_of_check = time();
                            // синхронизация с sale history
                            $salehistory = SaleHistory::findOne($sale->id);
                            if ($salehistory) {
                                if (!$salehistory->grossarea) $salehistory->grossarea = $sale->grossarea;
                                if (!$salehistory->floorcount) $salehistory->floorcount = $sale->floorcount;
                                if (!$salehistory->floor) $salehistory->floor = $sale->floor;
                                if (!$salehistory->house_type) $salehistory->house_type = $sale->house_type;
                                if (!$salehistory->year) $salehistory->year = $sale->year;
                                if (!$salehistory->living_area) $salehistory->living_area = $sale->living_area;
                                if (!$salehistory->kitchen_area) $salehistory->kitchen_area = $sale->kitchen_area;
                                $salehistory->save();
                            }
                            if (!$sale->save()) my_var_dump($sale->getErrors());
                            $message .= "<br>" . $session->getFlash('sale1');
                            $message .= "<br>";
                            $message .= $session->getFlash('sale2');
                            $message .= $session->getFlash('address');
                            $message .= $session->getFlash('disactive');
                        } else echo "<br> ждем соединения";
                    }

                } else {
                    $sale = Sale::findOne($id);
                    $SaleWidget1 = new SaleWidget();
                    $SaleWidget1->Load($sale);


                    $message .= "<hr>";
                    $message .= Html::a('OneParsing', ['console/parsing', 'id' => $sale->id]);
                    $message .= "<br>";
                    $message .= Html::a('OneGeocodation', ['console/geocodetion-test', 'id' => $sale->id]);
                    $session->setFlash('sale1', $SaleWidget1->admin_title);
                    sleep(rand(1, 2));
                    $parsingModel = New Parsing();
                    $parsingModel->LoadParsedInfoToSale($sale, $module);
                    $SaleWidget2 = new SaleWidget();
                    $SaleWidget2->Load($sale);
                    $session->setFlash('sale2', $SaleWidget2->admin_title);

                    $sale->date_of_check = time();
                    // синхронизация с sale history
                    $salehistory = SaleHistory::findOne($sale->id);
                    if ($salehistory) {
                        if (!$salehistory->grossarea) $salehistory->grossarea = $sale->grossarea;
                        if (!$salehistory->floorcount) $salehistory->floorcount = $sale->floorcount;
                        if (!$salehistory->floor) $salehistory->floor = $sale->floor;
                        if (!$salehistory->house_type) $salehistory->house_type = $sale->house_type;
                        if (!$salehistory->year) $salehistory->year = $sale->year;
                        if (!$salehistory->living_area) $salehistory->living_area = $sale->living_area;
                        if (!$salehistory->kitchen_area) $salehistory->kitchen_area = $sale->kitchen_area;
                        $salehistory->save();
                    }
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                    $message .= "<br>" . $session->getFlash('sale1');
                    $message .= "<br>";
                    $message .= $session->getFlash('sale2');
                    $message .= $session->getFlash('address');
                    $message .= $session->getFlash('disactive');
                }

            }
        }

        return $this->render('processing', compact('message'));


    }

    public
    function actionParsingOne()
    {
        $sale = Sale::find()
            ->where(['id' => 126682977])
            ->one();


        $message = "<hr><a href='" . $sale->url . "' target='_blank'>link </a> <br> выбрали " . $sale->id . "Sale" . $sale->title . " cost= "
            . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . " kitchen_area " . $sale->kitchen_area . "living_area " . $sale->living_area . "
<br> year " . $sale->year . " floorcount=" . $sale->floorcount . " floor=" . $sale->floor . " house_type= " . $sale->house_type;
        echo $message;
        // sleep(rand(1,2));
        $parsingModel = New Parsing();
        $parsingModel->LoadParsedInfoToSale($sale);
        $message = "<a href='" . $sale->url . "'>link </a> <br> выбрали " . $sale->id . "Sale" . $sale->title . " cost= "
            . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . " kitchen_area " . $sale->kitchen_area . "living_area " . $sale->living_area . "
<br> year " . $sale->year . " floorcount=" . $sale->floorcount . " floor=" . $sale->floor . " house_type= " . $sale->house_type;
        echo $message;
        $sale->date_of_check = time();
        // синхронизация с sale history
        $salehistory = SaleHistory::findOne($sale->id);
        if ($salehistory) {
            if (!$salehistory->grossarea) $salehistory->grossarea = $sale->grossarea;
            if (!$salehistory->floorcount) $salehistory->floorcount = $sale->floorcount;
            if (!$salehistory->floor) $salehistory->floor = $sale->floor;
            if (!$salehistory->house_type) $salehistory->house_type = $sale->house_type;
            if (!$salehistory->year) $salehistory->year = $sale->year;
            if (!$salehistory->living_area) $salehistory->living_area = $sale->living_area;
            if (!$salehistory->kitchen_area) $salehistory->kitchen_area = $sale->kitchen_area;
            $salehistory->save();
        }
        if (!$sale->save()) my_var_dump($sale->getErrors());


        return $this->render('processing');


    }

    public
    function actionParsingSaleHistory()
    {
        $sales = SaleHistory::find()
            ->where(['in', 'id_sources', [5, 1, 2, 3]])
            //  ->andwhere(['id' => 141819339])
            //  ->andwhere(['in','rooms_count' ,  [30]])
            // ->andwhere(['grossarea' => 0])
            ->andwhere(['in', 'disactive', [0, 2]])
            ->orderBy(new Expression('rand()'))
            ->andwhere(['<', 'date_of_check', time() - 24 * 60 * 60])
            ->andwhere(['>', 'date_start', time() - 100 * 24 * 60 * 60])
            // ->where(['floor' => 0])
            //->andwhere(['h' => 0])
            ->limit(5)
            ->all();
        echo " sale_count_lost=" . SaleHistory::find()
                ->where(['in', 'id_sources', [5, 1, 2, 3]])
                //      ->andwhere(['in','rooms_count' ,  [30]])
                ->andwhere(['in', 'disactive', [0, 2]])
                ->andwhere(['>', 'date_start', time() - 100 * 24 * 60 * 60])
                ->andwhere(['<', 'date_of_check', time() - 24 * 60 * 60])
                ->count();
        foreach ($sales as $sale) {

            $message = "<hr><a href='" . $sale->url . "'>link </a> <br> выбрали " . $sale->id . "Sale" . $sale->title . " cost= "
                . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . " kitchen_area " . $sale->kitchen_area . "living_area " . $sale->living_area . "
<br> year " . $sale->year . " floorcount=" . $sale->floorcount . " floor=" . $sale->floor . " house_type= " . $sale->house_type;
            echo $message;
            // sleep(rand(1,2));
            $parsingModel = New Parsing();
            $parsingModel->LoadParsedInfoToSale($sale);
            $message = "<a href='" . $sale->url . "'>link </a> <br> выбрали " . $sale->id . "Sale" . $sale->title . " cost= "
                . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . " kitchen_area " . $sale->kitchen_area . "living_area " . $sale->living_area . "
<br> year " . $sale->year . " floorcount=" . $sale->floorcount . " floor=" . $sale->floor . " house_type= " . $sale->house_type;
            echo $message;
            $sale->date_of_check = time();
            if (!$sale->save()) my_var_dump($sale->getErrors());


        }
        return $this->render('processing');


    }

    public
    function actionCheckConnection()
    {

        for ($i = 1; $i <= 100; $i++) {


            $html = my_curl_response('https://2ip.ru');
            $pq = phpQuery::newDocument($html);

            $ip = $pq->find('.ip')->find('big')->text();
            echo $ip;
            if (empty($ip)) echo "no internet connection";
            if ($ip != '') echo "internet connection exists";
        }

    }

    public function actionAutoFixAddresses()
    {
        $addresses = Addresses::find()
            ->where(['status' => 0])
            ->andwhere(['locality' => 'Великий Новгород'])
            ->limit(50)
            ->orderBy(new Expression('rand()'))
            ->all();
        foreach ($addresses as $address) {
            $count_of_true_floorcount = SaleHistory::find()
                ->where(['id_address' => $address->id])
                ->andwhere(['floorcount' => $address->floorcount])
                ->count();
            $count_of_false_floorcount = SaleHistory::find()
                ->where(['id_address' => $address->id])
                ->andwhere(['<>', 'floorcount', $address->floorcount])
                ->count();
            $count_of_true_house_type = SaleHistory::find()
                ->where(['id_address' => $address->id])
                ->andwhere(['house_type' => $address->house_type])
                ->count();
            $count_of_false_house_type = SaleHistory::find()
                ->where(['id_address' => $address->id])
                ->andwhere(['<>', 'house_type', $address->house_type])
                ->count();
            echo "<hr><br> в адресе " . $address->id . " " . $address->address . " правильных этажей " . $count_of_true_floorcount . " и неправильных этажей" . $count_of_false_floorcount;
            echo "<br> в адресе " . $address->address . " правильный тип дома " . $count_of_true_house_type . " и неправильный" . $count_of_false_house_type;
            if ($count_of_false_house_type == 0) $count_of_false_house_type = 1;
            if ($count_of_false_floorcount == 0) $count_of_false_floorcount = 1;
            if (($count_of_true_floorcount / $count_of_false_floorcount > 5) and ($count_of_true_house_type / $count_of_false_house_type > 5)) {
                echo "<h4>ADDRESS IS TRUE</h4>";
                $address->Fix();
            }
            echo "<br><a href='#' class ='address-ajax-fix'  data-id=$address->id ><i class ='fa fa-check fa-2x ' > </i></a> ";
        }
        return $this->render('processing');


    }

    public function actionExtractingMethods()
    {
        echo "<br> <h3>getOriginalIdFromUrl</h3>";
        $url = 'http://velnovgorod.irr.ru/real-estate/apartments-sale/secondary/1-komn-kvartira-bol-shaya-moskovskaya-ul-128a-advert673588147.html';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";
        $url = 'https://realty.yandex.ru/offer/5771313137222718209/?page=10&rgid=551292&type=SELL&category=APARTMENT&roomsTotal=2&sort=RELEVANCE&maxCoordinates=500&showUniquePoints=NO&pageSize=20&minTrust=NORMAL&offerIds=2632197701642511110&offerIds=154217518214570497&offerIds=2632197701642511116&offerIds=1360764071278464670&offerIds=1762269&offerIds=3889241202125760512&offerIds=2266250751293143040&offerIds=1360764071277535383&offerIds=7569503878167650329&offerIds=7569503878167647728&offerIds=143940490050744064&offerIds=4946689&offerIds=2632197701642511884&offerIds=5121023917328501760&offerIds=1943183662999215104&offerIds=5440415947259222250&offerIds=3887153177565120257&offerIds=5771313137222718209&offerIds=7053066616972823552&offerIds=4142657037062796800&initialPage=10%20Title%202-%D0%BA%D0%BE%D0%BC%D0%BD%D0%B0%D1%82%D0%BD%D0%B0%D1%8F%20%D0%BA%D0%B2%D0%B0%D1%80%D1%82%D0%B8%D1%80%D0%B0,%2045%20%D0%BC%C2%B2';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";
        $url = 'https://spb.cian.ru/sale/flat/161692520/';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";
        $url = 'https://www.avito.ru/velikiy_novgorod/kvartiry/1-k_kvartira_30.8_m_59_et._804795178';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";
        $url = 'https://youla.io/irkutsk/nedvijimost/prodaja-komnati/komnata-ot-10-do-15-m2-595e748262e1c675b3161895';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";
        $url = 'blslls  lsll s  ls';
        echo $url;
        echo "<h4>" . ParsingExtractionMethods::getOriginalIdFromUrl(
                $url) . "</h4>";


        echo "<br> <h3>extractNumbersFromString</h3>";
        $pattern = "/из \d+ предложений/";
        $string = 'из 18956 предложений';
        echo " pattern = \"" . $pattern . "\" string:" . $string;
        echo "<h4>" . ParsingExtractionMethods::extractNumbersFromString($pattern, $string) . "</h4>";
        return $this->render('processing');
    }

    public function actionSetOriginalId()
    {
        $sales = SaleHistory::find()->where(['id_in_source' => ''])->andWhere(['<>', 'url', ''])->limit(1000)->all();

        if (count($sales) != 0) {
            foreach ($sales as $sale) {
                $sale->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($sale->url);
                if (!$sale->save()) {
                    echo $sale->id;
                    my_var_dump($sale->getErrors());
                }

                echo " <br> Success" . $sale->url;
            }
        } else echo "Nothing to change";

    }

    public function actionSetConfigSources()
    {
        $configs = ParsingConfiguration::find()->all();
        foreach ($configs as $config) {

            $url = $config->start_link;
            $sources = ['irr', 'yandex', 'avito', 'cian', 'youla'];
            foreach ($sources as $source) {
                $pattern = "/$source/";
                if (preg_match($pattern, $url, $output_array)) {
                    echo " <br> установим source:" . $source;

                    $config->setSource($source);

                }

            }
        }


    }

    public function actionRenderingMethods()
    {

        echo Mdb::Fa('save');
    }

    public function actionGetCacheAddresses()
    {
        $session = Yii::$app->session;
        $module = $session->get('module');

        $all_addressed = Geocodetion::getAllAddresses($module);
    }

    public function actionPublish()
    {
        $SyncsQuery = Synchronization::find()->where(['disactive' => 3])->andWhere(['in', 'sync', [null, 0]])->andWhere(['<>', 'phone1', '']);
        echo " count to Synchronisation" . $SyncsQuery->count();
        $Syncs = $SyncsQuery->limit(50)->all();
        $n = 0;
        if ($Syncs) {
            foreach ($Syncs as $sync) {
                $sale = Sale::findOne($sync->id);
                if ($sale) {
                    $sale->setAttributes($sync->getAttributes());
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                } else {
                    $sale = new Sale();
                    $sale->setAttributes($sync->getAttributes());
                    if (!$sale->save()) my_var_dump($sale->getErrors());

                }
                if ($sync->setSynchronized()) {
                    $n++;
                    echo "<br> id =" . $sync->id . " успешно синхронизировано" . $sync->id_sources . " ))";
                }
            }
        }
        echo "<br> успешно синхронизировано " . $n . " записей";
        return $this->render('processing', compact('message'));
    }

    public function actionChromeTest()
    {
        $browser = MyChromeDriver::Open();
        $url = 'https://www.avito.ru/velikiy_novgorod/komnaty/komnata_16.2_m_v_1-k_29_et._1020485160';
        $browser->get($url);
        $browser->quit();


    }

    public function actionAddressTest($q = '')
    {
        if ($q == '') $q = ' московская дом65 к 7';
        echo " <br> ищем адрес в <h5>" . $q . " </h5>";
        return AddressesSearch::QuickSearch($q);


    }

    public function actionSendMail()
    {
        echo " i am switch mailer";
        my_var_dump(Yii::$app->mailer->compose()
            ->setTo('an.viktory@gmail.com')
            ->setFrom(['viktorgreamer1@yandex.ru'])
            ->setSubject('nothing_interesting')
            ->setTextBody('efwefwefwefwefwef')
            ->send());
    }

    public function actionSalefilterCheck()
    {
        $salefilters = SaleFilters::find()->where(['mail_inform' => 1])->all();
        echo "число фильтров для информирования по email" . count($salefilters);
        $sale = Sale::findOne(2196);
        if ($salefilters) {
            foreach ($salefilters as $salefilter) {
                if ($salefilter->Is_in_salefilter($sale)) {
                    echo "<h1> YES i am in sale filter</h1>";
                    $SaleWidwet = new SaleWidget();
                    $SaleWidwet->Load($sale);
                    Yii::$app->mailer->htmlLayout = 'layouts/html';
                    my_var_dump(Yii::$app->mailer->compose()
                        ->setTo('an.viktory@gmail.com')
                        ->setFrom(['viktorgreamer1@yandex.ru' => 'agent1.pro'])
                        ->setSubject("По фильтру:" . " " . $salefilter->name)
                        ->setTextBody("! есть соответствие " . $SaleWidwet->title . " " . $sale->url)
                        ->send());
                } else echo "<h1> NO i am in sale filter</h1>";
            }
        }


    }

    public function actionTestTagsAddress()
    {
        $address = Addresses::findOne(9);
        echo " <br>" . $address->address;
        my_var_dump($address->tags);
        echo Tags::render($address->tags);
        return $this->render('processing');
    }

    public function actionTestSaleFilter()
    {
        $model = new SaleFilters();
        echo " <br><div class='row'><div class='col-1'>";


        //  info($model->attributeLabels()['period_ads']);
        echo MdbActiveSelect::widget(['model' => $model, 'name' => 'period_ads', 'options' => SaleFilters::DEFAULT_PERIODS_ARRAY]);
        //  $model->save();
        echo "</div> </div> ";
        echo " <br><div class='row'><div class='col-1'>";


        //  info($model->attributeLabels()['period_ads']);
        echo Mdb::ActiveSelect($model, 'period_ads', SaleFilters::DEFAULT_PERIODS_ARRAY, ['multiple' => true]);
        //  $model->save();
        echo "</div> </div> ";

        return $this->render('processing');
    }

    public function actionTestAddressChanging()
    {
        $pattern = 'р-н Торговая сторона, Лени Голикова б-р, 2';
        $highstack = 'р-н Торговая сторона, Лени Голикова б-р, 2 📢📲';

        info($pattern);
        info($highstack);
        my_var_dump(preg_match("/" . $pattern . "/", $highstack));
        $sale = new Synchronization();

        info(" Было " . $highstack);
        $sale->address = $highstack;
        if (!$sale->save()) my_var_dump($sale->getErrors());
        $new_sale = Synchronization::findOne($sale->id);
        info(" Стало " . $new_sale->address);
        return $this->render('processing');
    }

    public function actionTest45429787()
    {
        $response['price'] = 4100000;
        $active_item = Synchronization::find()
            //  ->select('id,id_in_source, address_line, price,date_of_check,url,date_start')
            ->where(['id_in_source' => 45429787])->one();
        if ($active_item) {
            // делаем update
            $return = '';
            // если ADDRESS_CHANGED
            // если PRICE_CHANGED
            if ($active_item->price != $response['price']) {
                $active_item->date_of_check = time();
                $log = [4, time(), $active_item->price, $response['price']];
                $active_item->price = $response['price'];
                //  $active_item->addLog($log);
                // echo "<br>" . Sale::RenderOneLog($log);
                info("id=" . $active_item->id . " " . $active_item->changingStatuses('PRICE_CHANGED'), 'alert');
                $return .= "PRICE_CHANGED";

            }

            $delay = time() - $active_item->date_of_check;
            // если THE_SAME
            if ($delay > 1000) {
                //  echo "<br> !!!объект остался прежним";
                if ($response['date_start'] != $active_item->date_start) {
                    $log = [7, time(), date("d.m.y H:i:s", $active_item->date_start), date("d.m.y H:i:s", $response['date_start'])];
                    //  echo "<br>" . Sale::RenderOneLog($log);
                    $active_item->changingStatuses('DATESTART_UPDATED');
                    $active_item->date_start = $response['date_start'];
                    //    $active_item->addLog($log);
                } else $active_item->changingStatuses('THE_SAME');

                // значит ничего не изменилось
                $return .= ' THE_SAME';
            } // else  echo "<br> объект проверяли " . $delay . " секунд назад";


            // TODO ОТМЕНИТЬ КОГДА ВСЕ СВЕРИМ
            // $active_item->address_line = $response['address_line'];
            $active_item->price = $response['price'];
            // обновляем время проверки
            // $active_item->date_of_check = time();
            if ($return) echo "<br>СОХРАНЯЕМ ИЗМЕНЕННЫЕ СТАТУСЫ : " . $return . " ЦЕНА" . $active_item->price;

            // if ($active_item->save()) my_var_dump($active_item->getErrors());
            $active_item->save();

        }
        $active_item->save();
        return $this->render('processing');

    }

    public function actionTestSalefilterOnControls()
    {
        $salefilters = SaleFilters::findOne(94);
        info(count($salefilters->getOnControls()));

        return $this->render('processing');
    }

    public function actionTestProxy()
    {
        $driver = \app\models\ChromeDriver\MyChromeDriver::Open();
        $driver->get('https://yandex.ru/');
        sleep(2);
        $driver->findElement(WebDriverBy::xpath("//*[@id=\"text\"]"))->sendKeys("Срочный выкуп недвижимости великий новгород");
        sleep(2);
        // $driver->findElement(WebDriverBy::xpath('/html/body/div[1]/div[3]/div[2]/mtre/div[8]/div/div[2]/div/div[2]/div/form/div[2]/button'))->click();
        sleep(2);
        $driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
        $driver->findElement(WebDriverBy::partialLinkText('Но если все таки своими силами срочно продать квартиру в Великом Новгороде или в Новгородской области не получается то вам придется прибегнуть в услуге "срочный выкуп недвижимости в Великом Новгороде'))->click();
        sleep(10);
        //   echo $driver->getPageSource();
        $driver->quit();
        return $this->render('processing');
    }

    public function actionTestAgent()
    {
        $sales = Sale::find()
            // ->where(['id_address' => null])
            ->joinWith(['agent'])
            ->joinWith('addresses')
            ->limit(20)
            // ->orderBy(new Expression('rand()'))
            ->all();
        foreach ($sales as $sale) {
            echo "<hr>";
            echo $sale->renderAddress();
            echo $sale->renderContacts();
        }
        //  my_var_dump($variants_of_name);
        return $this->render('processing');
    }

    public function actionTestBla()
    {
        info('test-bla');
//        for ($n = 1; $n <= 10000; $n++) {
//            $count = round(rand(1, 7));
//            $arr = [];
//            info("count = " . $count);
//            for ($i = 1; $i <= $count; $i++) {
//                array_push($arr, round(rand(1, 100)));
//            }
//            // my_var_dump($arr);
//            $tag = Tags::convertToString($arr);
//            info(" Tags : " . $tag, 'alert');
//            $bla = new Bla();
//            $bla->text1 = $tag;
//            if (!$bla->save()) my_var_dump($bla->getErrors());
//
//        }

        $searching_tags_and = [2, 34, 56];
        $searching_tags_or = [99];
        $searching_tags_not = [];
        info(" ищем числа AND '" . implode(",", $searching_tags_and) . "' OR '"
            . implode(",", $searching_tags_or) . "' NOT '" . implode(",", $searching_tags_not) . "' ");
        $blasQuery = Bla::find();
        foreach ($searching_tags_and as $tag) {
            $blasQuery->andWhere(['like', 'text1', "," . $tag . ","]);
        }
        foreach ($searching_tags_not as $tag) {
            $blasQuery->andWhere(['not like', 'text1', "," . $tag . ","]);
        }
//        foreach ($searching_tags_or as $tag) {
//            $blasQuery->orWhere(['like', 'text1', "," . $tag . ","]);
//        }
        info(" всего " . $blasQuery->count() . " записей");
        $blas = $blasQuery->all();
        if ($blas) {
            foreach ($blas as $bla) {
                info(" нашли id='" . $bla->id . "' " . $bla->text1, 'alert');
                my_var_dump(Tags::convertToArray($bla->text1));
            }
        }


        return $this->render('processing');
    }

    public function actionTestTags()
    {
        info('TestTags');
        /*   if (!isset($_SESSION['id'])) $_SESSION['id'] = 1;
           $sales = Sale::find()
               ->from(['s' => Sale::tableName()])
             //  ->joinWith('tags')
               ->where(['>', 's.id', $_SESSION['id']])
               ->where(['s.tags_id' => NULL])
              // ->andwhere(['not in', 's.disactive', [1,2]])
               ->limit(100)->all();
          $counts = count($sales);
           if ($counts) {
               $_SESSION['id'] = max(ArrayHelper::getColumn($sales, 'id'));
               info($_SESSION['id']);

               info("counts=".$counts);
               foreach ($sales as $sale) {
                   $tags  = $sale->tags;
                   if ($tags) {
                       $tags = Tags::convertToString(ArrayHelper::getColumn($tags, 'tag_id'));
                       info($tags);
                       $sale->tags_id = $tags;
                       if (!$sale->save()) my_var_dump($sale->getErrors());
                   } else {
                       info("no tags",'alert');
                       $sale->tags_id = '';
                       if (!$sale->save()) my_var_dump($sale->getErrors());

                   }
               }
           } else info('NOTHING TO SEARCH');*/

        $sales = Sale::find()
            ->from(['s' => Sale::tableName()])
            //  ->joinWith('tags')
            ->where(['>', 's.id', $_SESSION['id']])
            ->where(['s.tags_id' => NULL])
            // ->andwhere(['not in', 's.disactive', [1,2]])
            ->limit(100)->all();
        $counts = count($sales);
        if ($counts) {
            $_SESSION['id'] = max(ArrayHelper::getColumn($sales, 'id'));
            info($_SESSION['id']);

            info("counts=" . $counts);
            foreach ($sales as $sale) {
                $tags = $sale->tags;
                if ($tags) {
                    $tags = Tags::convertToString(ArrayHelper::getColumn($tags, 'tag_id'));
                    info($tags);
                    $sale->tags_id = $tags;
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                } else {
                    info("no tags", 'alert');
                    $sale->tags_id = '';
                    if (!$sale->save()) my_var_dump($sale->getErrors());

                }
            }
        } else info('NOTHING TO SEARCH');


        return $this->render('processing');
    }

    public function actionTestActiveTags()
    {
        $sale = Sale::findOne(1);
        return $this->render('//tags/quick-add-form', [
            'parent_id' => $sale->id,
            'type' => 'sale',
            'realtags' => $sale->tags

        ]);
    }

    public function actionReorderTagsAddress()
    {
        $addresses = Addresses::find()->all();
        foreach ($addresses as $address) {
            info($address->tags_id);
            $address->setTags($address->getTags());
            info($address->tags_id);
            $address->save();
        }
        return $this->render('processing');
    }

    public function actionTestYandexMap()
    {

        return $this->render('modal');
    }

    public function actionTestSpam()
    {

        $driver = \app\models\ChromeDriver\MyChromeDriver::Open();
        $driver->get('https://victoriabrides.com');
        sleep(5);
        $driver->quit();


        return $this->render('processing');
    }

    public function actionAddProvince()
    {

        $module = Control::findOne(1);
        $Query_addresses = Addresses::find()
            //  ->where(['id' => 3009]);
            ->where(['AdministrativeAreaName' => ''])
            ->orwhere(['AdministrativeAreaName' => NULL])
            ->orderBy(new Expression('rand()'))
            ->limit(20);
        info(" count lost = " . Addresses::find()
                ->where(['AdministrativeAreaName' => ''])
                ->orwhere(['AdministrativeAreaName' => NULL])->count());

        $addresses = $Query_addresses->all();
        if ($addresses) {
            info(implode(',', ArrayHelper::getColumn($addresses, 'id')));
            foreach ($addresses as $address) {
                info($address->address);
                info($address->id);
                $geocodation = new Geocodetion();
                $geocodation->get_yandex_request($address, $module->region_rus, $module->oblast_rus);
                info("AdministrativeAreaName " . $geocodation->AdministrativeAreaName);
                $address->AdministrativeAreaName = $geocodation->AdministrativeAreaName;
                $address->coords_x = $geocodation->coords_x;
                $address->coords_y = $geocodation->coords_y;
                if (!$address->save()) my_var_dump($address->getErrors());
            }
        }


        return $this->render('processing');
    }


}




