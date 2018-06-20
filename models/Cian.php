<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 28.05.2018
 * Time: 16:30
 */

namespace app\models;


use yii\base\Model;
use app\utils\P;

class Cian extends Model
{

    public $rooms_count;

    const ID_SOURCE = 5;
    const DEBUG_MODE = true;

    static $tableContainerDivClass;
    static $tableTitleDivClass;
    static $tableTimeDivClass;
    static $tableStarredDivClass;
    static $tableAddressDivClass;
    static $tablePriceDivClass;
    static $tableUrlDivClass;
    static $totalCountDivClass;
    static $paginationListDivClass;



    static $pageTitleDivClass;
    static $pageAddressItemDivClass;
    static $pageAddressDivClass;
    static $pagePhotoRamaDivClass;
    static $pageLinkCoordsDivClass;
    static $pagePersonTitleDivClass;
    static $pagePhoneDivClass;
    static $pageDescriptionDivClass;
        static $pageInfoBlockDivClass;
        static $pageInfoBlock2DivClass;

    // метод для анализа страницы на селекторы

    public static function loadTableClasses($pageSource)
    {

        self::tableContainer_div_class($pageSource);


        $pq = \phpQuery::newDocument($pageSource);
        self::totalCount_div_class($pageSource);
        self::paginationList_div_class($pageSource);

        $container_div_class = Cian::$tableContainerDivClass;
        // going to miniContainer
        // echo "<br>\$container_div_class =" . $container_div_class;
        $pageSource = $pq->find("div." . $container_div_class)->eq(0)->html();

        self::tableTime_div_class($pageSource);
        self::tableTitle_div_class($pageSource);
        self::tableStarred_div_class($pageSource);
        self::tablePrice_div_class($pageSource);
        self::tableAddress_div_class($pageSource);
        self::tableUrl_div_class($pageSource);


    }

    // Данный метод проверяет и загружает в статические свойства вгенерированные классы CIAN
    public static function loadPageClasses($pageSource)
    {
        self::page_title_div_class($pageSource);
        self::page_address_div_class($pageSource);
        self::page_address_item_div_class($pageSource);
        self::page_photorama_div_class($pageSource);
        self::page_link_coords_div_class($pageSource);
        self::page_person_title_div_class($pageSource);
        self::page_phone_div_class($pageSource);
        self::page_description_div_class($pageSource);
        self::page_info_block_div_class($pageSource);
        self::page_info_block2_div_class($pageSource);

    }

    public static function throwError($id_error) {
        AgentPro::stop($id_error);
        info(AgentPro::ErrorLogs()[$id_error], 'danger');
        if (!self::DEBUG_MODE) die();
    }

    public static function page_title_div_class($pageSource)
    {
        if (preg_match("/class=\"(title--.{3,20})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);
            return self::$pageTitleDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_TITLE_CLASS_CIAN);
        }


    }

    public static function page_info_block_div_class($pageSource)
    {
        if (preg_match("/class=\"(info-block--.{3,20})\"/isU", $pageSource, $output_array)) {
              // my_var_dump($output_array);
            return self::$pageInfoBlockDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK_CLASS_CIAN);
        }


    }
    public static function page_info_block2_div_class($pageSource)
    {
        if (preg_match("/<article class=\"(container--.{1,10})\">/isU", $pageSource, $output_array)) {
              // my_var_dump($output_array);
            return self::$pageInfoBlock2DivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK2_CLASS_CIAN);
        }


    }

    public static function page_phone_div_class($pageSource)
    {
        if (preg_match("/class=\"(phone--.{3,20})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);
            return self::$pagePhoneDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_PHONE_CLASS_CIAN);
        }


    }

    public static function page_description_div_class($pageSource)
    {
        if (preg_match("/class=\"(description-text--.{3,20})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);
            return self::$pageDescriptionDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_DESCRIPTION_CLASS_CIAN);
        }


    }

    public static function page_person_title_div_class($pageSource)
    {
        if (preg_match("/<h2 class=\"(title--.{2,10})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);
            return self::$pagePersonTitleDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_PERSON_TITLE_CLASS_CIAN);
        }


    }

    public static function page_address_div_class($pageSource)
    {

        if (preg_match("/class=\"(address--.{3,10})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);

            return self::$pageAddressDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_CLASS_CIAN);


        }


    }

    public static function page_address_item_div_class($pageSource)
    {
        if (preg_match("/class=\".+(address-item--.{3,10})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);

            return self::$pageAddressItemDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN);

        }


    }

    public static function page_photorama_div_class($pageSource)
    {

        if (preg_match("/(fotorama__nav__frame--thumb)/isU", $pageSource, $output_array)) {
             //  my_var_dump($output_array);

            return self::$pagePhotoRamaDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_PHOTORAMA_CLASS_CIAN);


        }


    }

    public static function page_link_coords_div_class($pageSource)
    {

        if (preg_match("/class=\"(link--.{3,7}) href=.{1,30}map/isU", $pageSource, $output_array)) {
           //   my_var_dump($output_array);

            return self::$pageLinkCoordsDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGE_DIV_LINK_COORDS_CLASS_CIAN);

        }


    }


    public static function totalCount_div_class($pageSource)
    {

        if (preg_match("/class=\"(.{3,20}-totalOffers--.{3,20})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);

            return self::$totalCountDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TOTAL_COUNT_DIV_CLASS_CIAN);
        }


    }

    public static function paginationList_div_class($pageSource)
    {

        if (preg_match("/class=\"(.{3,20}-list-item--.{3,20})\"/isU", $pageSource, $output_array)) {
            //   my_var_dump($output_array);

            return self::$paginationListDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_PAGINATION_LIS_DIV_CLASS_CIAN);


        }


    }


    public static function getAvailablePages(\phpQueryObject $pq)
    {
        $pagination_list_div_class = Cian::$paginationListDivClass;

        if ($pq_pagination_list = $pq->find("." . $pagination_list_div_class)) {
            $arr_pages = array();
            foreach ($pq_pagination_list as $page) {
                $page_num = intval(pq($page)->text());
                if (preg_match("/.\d+/", $page_num)) {
                    //  echo "<br>page_num=" . $page_num;
                    // array_push($page_num ,$arr_pages);
                    $arr_pages[] = $page_num;

                }
            }
            // my_var_dump($arr_pages);

        };

        return $arr_pages;
    }

    public static function tableContainer_div_class($pageSource)
    {

        if (preg_match("/class=\"(.{3,20}-main-container--.{3,20})\"/isU", $pageSource, $output_array)) {
            // my_var_dump($output_array);

            return self::$tableContainerDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_CLASS_CIAN);


        }


    }

    public static function tableTitle_div_class($input_line)
    {

        if (preg_match_all("/class=\"(.{3,20}-title--.{3,20})\"/isU", $input_line, $output_array)) {
            //  echo "<br>COUNT=".count($output_array[1]);
            //  my_var_dump($output_array);
            return self::$tableTitleDivClass = $output_array[1][0];
            // return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_TITLE_CLASS_CIAN);
        }


    }

    public static function tablePrice_div_class($input_line)
    {

        if (preg_match_all("/class=\"(.{3,20}-header--.{3,20})\"/isU", $input_line, $output_array)) {
            return self::$tablePriceDivClass = $output_array[1][1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_PRICE_CLASS_CIAN);

        }


    }

    public static function tableAddress_div_class($input_line)
    {


        if (preg_match("/class=\"(.{3,20}-address-links--.{3,20})\"/isU", $input_line, $output_array)) {
            return self::$tableAddressDivClass = $output_array[1];
            // return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_ADDRESS_CLASS_CIAN);

        }


    }

    public static function tableTime_div_class($input_line)
    {

        if (preg_match("/class=\"(.{3,20}-absolute--.{3,20})\"/isU", $input_line, $output_array)) {
            return self::$tableTimeDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_TIME_CLASS_CIAN);

        }


    }

    public static function tableStarred_div_class($input_line)
    {

        if (preg_match("/class=\"(.{3,20}-button--.{3,20})\"/isU", $input_line, $output_array)) {
            return self::$tableStarredDivClass = $output_array[1];
            // return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_STARRED_CLASS_CIAN);

        }


    }

    public static function tableUrl_div_class($input_line)
    {

        if (preg_match_all("/class=\"(.{3,20}-header--.{3,20})\"/isU", $input_line, $output_array)) {
            //  my_var_dump($output_array);
            return self::$tableUrlDivClass = $output_array[1][0];
            // return $output_array[1];
        } else {
            self::throwError(AgentPro::ERROR_CANNOT_CALCULATE_TABLE_DIV_URL_CLASS_CIAN);

        }


    }


    public static function extractTableContainerData($pq_div)
    {
        //  echo "<br>" . pq($pq_div)->html();

        $id_sources = Cian::ID_SOURCE;

        $title_div_class = Cian::$tableTitleDivClass;
        //  echo "<br>\$title_div_class=" . "div." . $title_div_class;

        $title = $pq_div->find("div." . $title_div_class)->text();
        //  echo "<br>TITLE=" . $title;
        // берем div c ценой

        $price_div_class = Cian::$tablePriceDivClass;

        $price = P::ExtractNumders($pq_div->find("div." . $price_div_class)->text());

        $url_div_class = self::$tableUrlDivClass;

        $url = $pq_div->find("a." . $url_div_class)->attr('href');
        // вытаскиваем ссылку
        // вытаскиваем id
        $array_id = array_reverse(preg_split("/\//", $url));
        $id = $array_id[1];

        $starred_div_class = Cian::$tableStarredDivClass;
        if ($starred_div_class) {
            $starred = $pq_div->find("button." . $starred_div_class)->html();

            if (!empty($starred)) $starred = true; else $starred = false;

        }
        // вытаскиваем address_line и preg_split by "Великий Новгород";

        $address_div = Cian::$tableAddressDivClass;

        $address_line = $pq_div->find("div." . $address_div)->text();
        // echo "<br> \$address_line " . $address_line;
        //  echo "<br> \$module " .\Yii::$app->params['module']->region_rus;
        $address_line = preg_split("/" . \Yii::$app->params['module']->region_rus . ",/", $address_line);
        $address = trim($address_line[1]);

        $time_div_class = Cian::$tableTimeDivClass;
        $date = $pq_div->find("div." . $time_div_class)->text();
        //  echo "<br> date as String=" . $date;
        $date_start = ParsingExtractionMethods::Date_to_unix($date, 'cian');
        //  echo "<br> date_start" . $date_start;

        return [
            'id' => $id,
            'url' => $url,
            'price' => abs($price),
            'title' => $title,
            'address_line' => $address,
            'starred' => $starred,
            'id_sources' => $id_sources,
            'date_start' => $date_start

        ];


    }

    public function extractPageData($pq, $url)
    {
        // достаем параметр $rooms_count
        // info('I','alert');
        $title = $pq->find("." . Cian::$pageTitleDivClass)->text();

        if (preg_match("/\d/", $title, $numbers)) {
            // echo "<br> парсим квартиру";
            $this->rooms_count = $numbers[0];
        };
        if (preg_match("/свободной/", $title, $numbers)) {
            // echo "<br> парсим комнату";
            $this->rooms_count = 10;
        };
        if (preg_match("/Комната/", $title, $numbers)) {
            echo "<br> парсим комнату";
            $this->rooms_count = 30;
        };
        if (preg_match("/Студия/", $title, $numbers)) {
            $this->rooms_count = 20;
        };
        //  echo "<br> ROOMS_COUNT = " . $this->rooms_count;
        // вычистяем количество елементов полного адреса (последние 2 отвечают за улицу и дом)
        $count = count($pq->find("a." . Cian::$pageAddressItemDivClass));
        //берем блок с адресами
        $street = $pq->find("." . Cian::$pageAddressDivClass)->find("a:eq(" . ($count - 2) . ")")->text();
        $house = $pq->find("." . Cian::$pageAddressDivClass)->find("a:eq(" . ($count - 1) . ")")->text();
        $this->address = $street . ", " . $house;


        $gallery_list = $pq->find("div." . Cian::$pagePhotoRamaDivClass);
        $images = [];
        foreach ($gallery_list as $item) {

            preg_match("/src=\".+\.jpg/", phpQuery::pq($item), $array_of_images);

            $image = preg_replace("/src=\"/", "", $array_of_images[0]);
            //   echo "<br> <img src=\"".$image."\">";
            array_push($images, $image);
        }
        //  my_var_dump($images);
        $data = $pq->find("a.".Cian::$pageLinkCoordsDivClass)->attr('href');
        preg_match("/center=(.+)&/", $data, $output_array);
        $data = preg_split("/%2C/", $output_array[1]);
        $this->coords_x = round($data[0], 5);
        $this->coords_y = round($data[1], 5);

        $this->images = serialize($images);
        //  echo " <br>" . $this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find("h2.".Cian::$pagePersonTitleDivClass)->text();
        // echo "<br> person= " . $div_person;
        $div_phone = $pq->find("a.".Cian::$pagePhoneDivClass)->text();
        preg_match("/\d+\s\d{3}\s\d{3}-\d{2}-\d{2}/", $div_phone, $output_array);

        $div_phone = preg_replace("/\D+/", "", $output_array[0]);
        $this->phone1 = preg_replace("/\A7/", "8", $div_phone);
        //echo "<br> phone= " . $this->phone1;
        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find("p.".Cian::$pageDescriptionDivClass)->text();

        //  echo "<br> div_descriprion= " . $div_descriprion[0];
        $this->description = $div_descriprion;
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find(".".Cian::$pageInfoBlockDivClass)->text());
        //  echo "<br> object_descr_props= " . $object_descr_props;

        // вырезаем блок с этажами
        preg_match_all("/Этаж\d+из\d+/", $object_descr_props, $floors);
        //  my_var_dump($floors);
        $floors = preg_split("/из/", $floors[0][0]);
        $this->floor = preg_replace("/\D+/", "", $floors[0]);
        $this->floorcount = preg_replace("/\D+/", "", $floors[1]);

        // достаем параметр $grossarea
        preg_match("/Общая(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $grossarea);
        $this->grossarea = round(floatval($grossarea[1]), 1);


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
        $object_descr_props2 = preg_replace('/\s+/', '', $pq->find(".".Cian::$pageInfoBlock2DivClass)->text());
        // echo "<br> object_descr_props= " . $object_descr_props;
        preg_match("/Панельный|Кирпичный|Деревянный|Монолитный|Кирпично-Монолитный|Сталинский/", $object_descr_props2, $house_type);

        $this->house_type = preg_match_house_type($house_type[0]);

        // достаем параметр $price
        preg_match("/.\"offerPrice\":(.\d+)/", $pq->html(), $output_array);

        $this->price = $output_array[1];
        //  echo "price = ".$this->price;

        if ($this->rooms_count == 30) {
            // достаем параметр $grossarea
            preg_match("/Площадькомнаты(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props2, $grossarea);
            // my_var_dump($grossarea);
            $this->grossarea = round(floatval($grossarea[1]), 1);


        }


    }


}