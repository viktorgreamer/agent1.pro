<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.08.2017
 * Time: 12:45
 */

namespace app\components;


use app\models\Sale;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class YandexMaps extends Widget
{
    public $options;
    public $module;
    public $salefilter;
    public $controls;
    public $isEditablePolygon = false;
    public $sales;
    public $polygon;
    public $map_id;
    public $addresses;
    public $type; // {sales|addresses}

    public function init()
    {

        parent::init();
        if (!$this->map_id) $this->map_id = substr(md5(rand()), 0, 10);
        if (!$this->polygon) {
            if (!$this->salefilter->polygon_text) $this->polygon = json_encode([]);
            else {
                $this->polygon = $this->salefilter->polygon_text;
            }
        }

        if ($this->sales) {
            $this->sales = array_group_by($this->sales, 'id_address');


        }
        // \Yii::$app->view->registerJs("toastr.success('".my_var_dump($this->module).");", View::POS_READY);


    }

    public function run()
    {
        if ($this->sales) return $this->render('yandex-maps',
            [
                'module' => json_encode(['coords_x' => $this->module->coords_x, 'coords_y' => $this->module->coords_y, 'zoom' => $this->module->zoom]),
                'isEditablePolygon' => json_encode($this->isEditablePolygon),
                'polygon' => $this->polygon,
                'map_id' => $this->map_id,
                'salefilter' => $this->salefilter,
                'controls' => $this->controls,
                'Placemarks' => json_encode($this->generatePlacemarks()),
                'addresses' => $this->addresses,
                'type' => $this->type]);

        if ($this->addresses) {

            return $this->render('yandex-maps',
                [
                    'module' => json_encode(['coords_x' => $this->module->coords_x, 'coords_y' => $this->module->coords_y, 'zoom' => $this->module->zoom]),
                    'isEditablePolygon' => json_encode($this->isEditablePolygon),
                    'polygon' => $this->polygon,
                    'map_id' => $this->map_id,
                    'Placemarks' => json_encode($this->generatePlacemarksAddress()),
                    'type' => $this->type]);
        }
    }

    public function LoadAddress($addresses)
    {
        $this->addresses = $addresses;
    }

    public function ShowOneAddress($address)
    {
        echo
            "myPlacemark$address->id = new ymaps.Placemark(
            
            [$address->coords_y,$address->coords_x], 
            { 
            hintContent: '$address->address',
             balloonContentBody:' Год: $address->year, $address->floorcount Этажей, " . $this->RenderHouseType($address->house_type) . "',
        });
         myMap.geoObjects.add(myPlacemark$address->id);
        
        
        ";

    }


    public function generatePlacemarks()
    {
        $Placemarks = [];

        foreach ($this->sales as $key => $address) {
            $balloonContentBody = '';
            if (count($address) > 1) $iconContent = count($address); else $iconContent = '';
            // если id_address существует тo ставим его координаты
            if ($key !== 0) {
                $message = " COORDS" . $key;
                $coords = [$address[0]->addresses->coords_x, $address[0]->addresses->coords_y];
            } else {
                $module = $this->module;
                $message = "NO COORDS" . $key;
                //  \Yii::$app->view->registerJs("toastr.success('".$module->coords_x." ".$module->coords_y."');", View::POS_READY);
                $coords = [$module->coords_x, $module->coords_y];
            }
            $balloonContentBody = $message . " " . \Yii::$app->view->render('@app/views/sale/_mini-sales', ['sales' => $address, 'contacts' => true, 'salefilter' => $this->salefilter, 'controls' => $this->controls]);
            $properties = ['balloonContentBody' => $balloonContentBody, 'iconContent' => $iconContent];
            $placemark = [$coords, $properties];
            array_push($Placemarks, $placemark);
        }
        //  my_var_dump(json_encode($Placemarks));
        return $Placemarks;


    }

    public function generatePlacemarksAddress()
    {
        /* @var $address \app\models\Addresses */
        $Placemarks = [];

        foreach ($this->addresses as $key => $address) {
            $balloonContentBody = '';
            $iconContent = '';
            // если id_address существует тo ставим его координаты
            /*   if ($key !== 0) {
                   $message = " COORDS" . $key;
                   $coords = [$address[0]->addresses->coords_x, $address[0]->addresses->coords_y];
               } else {
                   $module = $this->module;
                   $message = "NO COORDS" . $key;
                   //  \Yii::$app->view->registerJs("toastr.success('".$module->coords_x." ".$module->coords_y."');", View::POS_READY);
                   $coords = [$module->coords_x, $module->coords_y];
               }*/
            $coords = [$address->coords_x, $address->coords_y];
            $balloonContentBody = $message . " " . \Yii::$app->view->render('@app/views/addresses/_list', ['address' => $address]);
            $properties = ['balloonContentBody' => $balloonContentBody, 'iconContent' => $iconContent];
            $placemark = [$coords, $properties];
            array_push($Placemarks, $placemark);
        }
        //  my_var_dump(json_encode($Placemarks));
        return $Placemarks;


    }


}