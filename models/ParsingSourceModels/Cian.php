<?php

namespace app\models\ParsingSourceModels;

use app\models\Parsing;
use app\models\Sale;
use yii\base\Model;
use phpQuery;
use app\models\ParsingExtractionMethods;

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 20.12.2017
 * Time: 21:17
 */
class Cian extends Sale

{
    public function UnparsePage($pq, $url)
    {
        // достаем параметр $rooms_count
        $rooms_count = $pq->find('.title--2oO4e')->text();
        if (preg_match("/\d/", $rooms_count, $numbers)) {
            // echo "<br> парсим квартиру";
            $this->rooms_count = $numbers[0];
        };
        if (preg_match("/свободной/", $rooms_count, $numbers)) {
            // echo "<br> парсим комнату";
            $this->rooms_count = 10;
        };
        if (preg_match("/комната/", $rooms_count, $numbers)) {
            // echo "<br> парсим комнату";
            $this->rooms_count = 30;
        };
        if (preg_match("/студия/", $rooms_count, $numbers)) {
            $this->rooms_count = 20;
        };
        //  echo "<br> ROOMS_COUNT = " . $this->rooms_count;
        // вычистяем количество елементов полного адреса (последние 2 отвечают за улицу и дом)
        $count = count($pq->find('a.address-item--1jDfG'));
        //берем блок с адресами
        $street = $pq->find('.address--D3O4n')->find("a:eq(".($count-2).")")->text();
        $house = $pq->find('.address--D3O4n')->find("a:eq(".($count-1).")")->text();
        $this->address = $street . ", " . $house;


        $gallery_list = $pq->find('div.fotorama__nav__frame--thumb');
        $images = [];
        foreach ($gallery_list as $item) {

            preg_match("/src=\".+\.jpg/", phpQuery::pq($item), $array_of_images);

            $image = preg_replace("/src=\"/", "", $array_of_images[0]);
            //   echo "<br> <img src=\"".$image."\">";
            array_push($images, $image);
        }
        //  my_var_dump($images);


        $this->images = serialize($images);
        //  echo " <br>" . $this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find('h2.title--2iUH-')->text();
       // echo "<br> person= " . $div_person;
        $div_phone = $pq->find('a.phone--1OSCA')->text();
        preg_match("/\d+\s\d{3}\s\d{3}-\d{2}-\d{2}/", $div_phone, $output_array);

        $div_phone = preg_replace("/\D+/", "", $output_array[0]);
        $this->phone1 = preg_replace("/\A7/", "8", $div_phone);
        //echo "<br> phone= " . $this->phone1;
        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find('p.description-text--3SshI')->text();

        //  echo "<br> div_descriprion= " . $div_descriprion[0];
        $this->description = $div_descriprion;
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find('.info-block--1hVvz')->text());
        //  echo "<br> object_descr_props= " . $object_descr_props;

        // вырезаем блок с этажами
        preg_match_all("/Этаж\d+из\d+/", $object_descr_props, $floors);
      //  my_var_dump($floors);
        $floors = preg_split("/из/", $floors[0][0]);
        $this->floor = preg_replace("/\D+/", "", $floors[0]);
        $this->floorcount = preg_replace("/\D+/", "", $floors[1]);

        // достаем параметр $grossarea
        preg_match("/Общая(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $grossarea);
        $this->grossarea = round(floatval($grossarea[1]),1);


        // достаем параметр $living_area
        preg_match("/Жилая(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $living_area);

        $this->living_area = round(floatval($living_area[1]));

        // достаем параметр $kitchen_area
        preg_match("/Кухня(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $kitchen_area);
        $this->kitchen_area = round(floatval($kitchen_area[1]));

        // достаем параметр $year
        preg_match("/Построен(\d+)/", $object_descr_props, $year);
        $this->year = $year[1];


        // достаем параметр $house_type
        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find('.container--22Qpp')->text());
        echo "<br> object_descr_props= " . $object_descr_props;
        preg_match("/Панельный|Кирпичный|Деревянный|Монолитный|Кирпично-Монолитный|Сталинский/", $object_descr_props, $house_type);

        $this->house_type = preg_match_house_type($house_type[0]);

        // достаем параметр $price
        $price = str_replace("/(?:\s|&nbsp;)+/", '',$pq->find('.price--residential--37Rgt')->text());
       $this->price = preg_replace("/\D+/", "", preg_split("/или/", $price)[0]);;

        if (preg_match('/комната/', $pq->find('div.object_descr_title')->text(), $output_array)) {


            // достаем параметр $grossarea
            preg_match("/Площадькомнаты:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $grossarea);
            $this->grossarea = floatval(str_replace(',', '.', $grossarea[1]));


        }


    }



}