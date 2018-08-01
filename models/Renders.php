<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 24.09.2017
 * Time: 23:54
 */

namespace app\models;


use app\components\SaleWidget;
use yii\helpers\Html;
use Yii;

abstract class Renders
{
    const REPLACE = 1;
    const TRIM = 2;

    public static function YPlacemark($array)
    {

        return "myMap.geoObjects.add(new ymaps.Placemark([" . $array['coords_x'] . "," . $array['coords_y'] . "], {
            
             balloonContentHeader:  '',
            balloonContentBody: '" . $array['title'] . "',
            balloonContentFooter: '" . $array['days_ago'] . "',
            
            hintContent: '" . $array['title'] . "'
            }));";

    }

    public static function YPlacemarks($id_addresses, $type = 'main', $id)
    {
        $message = '';
        if ($type != 'address') {
            foreach ($id_addresses as $id_address) {
                $message .= "myMap_" . $id . ".geoObjects.add(new ymaps.Placemark(
            [" . $id_address[0]['coords_x'] . "," . $id_address[0]['coords_y'] . "], 
            {
                balloonContent: '";


                foreach ($id_address as $sale) {
                    $sale = Sale::findOne($sale->id);
                    if ($sale) {
                        if ($type == 'public') $message .= $sale->renderLong_title() . " " . $sale->renderContacts() . " <hr>";
                        else $message .= $sale->renderLong_title() . " " . Renders::phoneToString($sale->phone1) . "<hr>";
                    }

                }
                $message .= "',";
                if (count($id_address) > 1) $message .= "
                iconContent:  '" . count($id_address) . "'";

                $message .= "},));";

            }
        }
        if ($type == 'address') {
            foreach ($id_addresses as $address) {
                $message .= "myMap_" . $id . ".geoObjects.add(new ymaps.Placemark(
            [" . $address->coords_x . "," . $address->coords_y . "], 
            {
                balloonContent: '";
                $message .= $address->address;

                $message .= "',";
//                if (count($id_address) > 1) $message .= "
//                iconContent:  '" . count($id_address) . "'";

                $message .= "},));";

            }
        }

        return $message;

    }

    public static function devYPlacemarks($id_addresses, $type = 'main')
    {
        $message = '';
        foreach ($id_addresses as $id_address) {
            $message .= "myCollection.add(new ymaps.Placemark(
            [" . $id_address[0]['coords_x'] . "," . $id_address[0]['coords_y'] . "], 
            {
                balloonContent: '";
            foreach ($id_address as $sale) {
                $salewidget = new SaleWidget();
                $salewidget->Load($sale);
                if ($type == 'public') $message .= $salewidget->title . " " . $salewidget->price . " <hr>";
                else $message .= $salewidget->title . " " . $sale->phone1 . " " . $salewidget->url . "  " . $salewidget->price . " <hr>";
            }
            $message .= "',";
            if (count($id_address) > 1) $message .= "
                iconContent:  '" . count($id_address) . "'";

            $message .= "},));";

        }


        return $message;

    }

    public static function PriceFormat($price)
    {
        return number_format($price, 0, ".", ".") . "<small>руб.</small>";
    }

    public static function Counter($id, $from, $to, $duration)
    {
        return \app\widgets\CounterWidget::widget([
            'from' => $from,
            'to' => $to,
            'time' => $duration,
            'id' => $id


        ]);
    }


    public static function Price($price)
    {
        return "<strong class='red-text'><b>" . number_format($price, 0, ".", ".") . "</b></strong><small>руб.</small>";
    }

    public
    function phoneToString($phone)
    {
        return substr($phone, 0, 1) . "-" . substr($phone, 1, 3) . "-" . substr($phone, 4, 3) . "-" . substr($phone, 7, 2) . "-" . substr($phone, 9, 2);

    }

    public static function Days_ago($timestamp, $type = 'primary')
    {

        return " <div class='text-" . $type . "'>" . round(((time() - $timestamp) / 86400), 0) . " дн. назад </div>";


    }

    public static function Sec_ago($timestamp, $type = 'primary')
    {

        return " <div class='text-" . $type . "'>" . round((time() - $timestamp), 0) . " </div>";


    }

    public static function IMPLODE($body, $size = 1)
    {
        $array = explode(",", $body);
        $return = '';
        if ($array) {
            $array_chunk = array_chunk($array, $size);
            foreach ($array_chunk as $array) {
                if (!empty($array)) {
                    $return .= implode(',', $array) . "<br>";
                }
            }
        }
        return $return;

    }

    public static function toModal($icon = "+", $message = '')
    {
        return \Yii::$app->view->render('@app/views/my-debug/_modal_info', ['icon' => $icon, 'message' => $message]);

    }

    public static function toAlert($message = '', $type = SUCCESS)
    {
        return \Yii::$app->view->registerJs("toastr." . $type . "('" . $message . "');", yii\web\View::POS_READY);

    }

    public static function StaticView($type = '', $options = [])
    {

        return \Yii::$app->view->render("@app/views/" . $type, $options);


    }

    public static function renderSources($id)
    {
        return Html::img("@web/images/logo/" . Sale::IMG_SOURCES[$id]);
    }


    public static function Highlighting($body = '', $needle = '', $borders = 0)
    {

        if (($needle) && ($body)) {

            $start_point = strpos($body, $needle);
            //  echo span("start point = ".$start_point);
            $end_point = $start_point + strlen($needle);
            //  echo span("end_point = ".$end_point);
            $pre = substr($body, $start_point - $borders, $borders);
            //   echo span("pre = ".$pre);

            $suf = substr($body, $end_point, $borders);
            //  echo span("suf = ".$suf);
            $all = $pre . "<my_selection_start><b class='red-text text-uppercase' style='font-size: 15px'; >" . $needle . "</b><my_selection_start_end> " . $suf;
            return $all;

        } else return '';


    }

    public static function SystemMail($message)
    {


        Yii::$app->mailer->compose()
            ->setTo('an.viktory@gmail.com')
            ->setFrom(['viktorgreamer1@yandex.ru' => 'agent1.pro'])
            ->setSubject("SYSTEM_MAIL")
            ->setTextBody($message)
            ->send();

    }

    public static function BackGroundRow($color = 0)
    {

        switch ($color) {
            case COLOR_DISACTIVE:
                {
                    return "style='background-color: gainsboro'";
                }

        }


    }

    public static function SOLD()
    {

//       return "<div class=\"float-right\">
//                    <span class='badge badge-pill red animated pulse infinite'>продан</span>
//                </div>";

        return Html::img("/web/icons/sold.png", ['width' => 128, 'class' => 'sold animated fadeInRightBig']);

    }

    public static function MODERATED()
    {

        return "<i class=\"fa fa-check green-text fa-2x\" aria-hidden=\"true\" title=\"Проверен\"></i>";

    }


}