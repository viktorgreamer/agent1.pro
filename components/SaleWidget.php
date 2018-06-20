<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 08.05.2017
 * Time: 18:46
 */

namespace app\components;

use app\models\Addresses;
use app\models\Agents;
use app\models\RealTags;
use app\models\Tags;
use yii\base\Widget;
use app\models\Sale;
use yii;


class SaleWidget extends Widget
{
    public $sale;
    public $id;
    public $address;
    public $areas;
    public $floors;
    public $phone;
    public $phone2;
    public $house_type;
    public $description;
    public $days_ago;
    public $rooms_count;
    public $photos;
    public $count_of_ads;
    public $sale_stat;
    public $price;
    public $title;
    public $admin_title;
    public $original_address;
    public $id_address;
    public $year;
    public $url;
    public $status;
    public $title_to_copy;
    public $tags;
    public $tags_button;
    public $tags_modal;
    public $TagRegisterJs;
    public $balloontitle;
    public $source_url;


    public function run()
    {

        return $this->render('one-sale-table',
            [
                'sale' => $this->sale]);
    }

    public function RenderTitle()
    {
        $string = '';
        $sale = $this->sale;
        $address = Addresses::findOne($sale->id_address);
        if ($sale->rooms_count == 30) $string = "Комн.";
        elseif ($sale->rooms_count == 20) $string = "Студия";
        else  $string = $sale->rooms_count . "к.кв., ";
        if ($address) $string .= $address->address . ", ";

        $string .= $sale->floor . "/" . $sale->floorcount . "" . $this->RenderHouseType($sale);
        if ($sale->grossarea) $area_string = $sale->grossarea; else $area_string = '0';
        if ($sale->living_area != 0) $area_string .= "/" . $sale->living_area;
        if ($sale->kitchen_area != 0) $area_string .= "/" . $sale->kitchen_area;
        $area_string .= "м2";
        $string .= $area_string;
        $string .= "<br><strong>" . $sale->price . " </strong>";
        $string .= "<br><a href=\"" . $sale->url . "\" target=\"_blank\">ссылка</a>";
        return $string;


    }

    public function RenderHouseType($sale)
    {

        switch ($sale->house_type) {
            case 2; {

                return "кирп.";
            }
            case 1; {

                return "пан.";
            }


            case 3; {

                return "Монолит.";
            }
            case 4; {

                return "Блочный";
            }
            case 5; {

                return "Дер.";
            }


            default: {

                return '';
            }


        }
    }

    public function RenderArea($sale)
    {

        $area_string = $sale->grossarea;
        if ($sale->living_area != 0) $area_string .= "/" . $sale->living_area;
        if ($sale->kitchen_area != 0) $area_string .= "/" . $sale->kitchen_area;
        $area_string .= "м2";
        return $area_string;
    }

    public function Load($sale)
    {
        //  $sale = Sale::findOne($sale->id);
        $address = Addresses::findOne($sale->id_address);
// address
//rooms_count
        if ($sale->rooms_count == 30) $this->rooms_count = "Комн."; elseif ($sale->rooms_count == 20) $this->rooms_count = "Студия";
        else   $this->rooms_count = $sale->rooms_count . "к.кв.";
        $this->original_address = $sale->address;
        $this->id_address = $sale->id_address;
        if ($sale->id_address != 0) $this->address = $address->address;
        else  $this->address = $sale->address;
        $area_string = $sale->grossarea;
        if ($sale->living_area != 0) $area_string .= "/" . $sale->living_area;
        if ($sale->kitchen_area != 0) $area_string .= "/" . $sale->kitchen_area;
        $area_string .= "м2";
// areas
        $this->areas = $area_string;
// floors
        $this->floors = $sale->floor . "/" . $sale->floorcount . " эт.";
// house_type
        switch ($sale->house_type) {
            case 2; {

                $this->house_type = "кирп.";
            }
            case 1; {

                $this->house_type = "пан.";
            }


            case 3; {

                $this->house_type = "Монолит.";
            }
            case 4; {

                $this->house_type = "Блочный";
            }
            case 5; {

                $this->house_type = "Дер.";
            }


            default: {

                $this->house_type = '';
            }


        }

// phone
        if ($sale->phone1) {
            $this->phone = $this->phoneToString($sale->phone1);
            if ($sale->phone2) $this->phone .= "<br>" . $this->phoneToString($sale->phone2);
        } else $this->phone = 'no phone';

        $agent = Agents::find()
            ->where(['phone' => $sale->phone1])
            ->one();
        // id
        $this->id = $sale->id;

        $this->count_of_ads = $agent->count_ads;
        // description
        $this->description = $sale->description;
        // days_ago
        $this->days_ago = " <p class='text-primary'>" . round(((time() - $sale->date_start) / 86400), 0) . " дн. назад </p>";
        // Photos
        if (!empty($sale->images)) {
            $list_of_images = unserialize($sale->images);
            $id = $sale->id;
            $this->photos = "<img src=" . $list_of_images[0] . " width=\"100\"  height=\"100\" style='position: absolute; clip: rect(0, 90px, 90px, 0);'>";

        } else {
            $id = $sale->id;
            $this->photos = "<img src=\"nophoto.png\" width=\"100\" 
                                                         height=\"100\" style='position: absolute; clip: rect(0, 90px, 90px, 0);'>";
        }
        // имя ресурса и ссылка на него
        $name_source = 'nothing';
        if ($sale->id_sources == 3) $name_source = 'avito.ru';
        if ($sale->id_sources == 2) $name_source = 'yandex.ru';
        if ($sale->id_sources == 1) $name_source = 'irr.ru';
        if ($sale->id_sources == 5) $name_source = 'cian.ru';
        if ($sale->id_sources == 4) $name_source = 'youla.io';

        $this->url = "<a href=" . $sale->url . " target=_blank>" . $name_source . "</a>";

// price
if ($sale->price < 0.85 *$sale->average_price_same) $prefix = '<i class="fa fa-bolt animated flash infinite" aria-hidden="true"></i>';
        $this->price = $prefix." ".number_format($sale->price, 0, ".", ".") . "руб.";
        $this->year = $sale->year;
        if ($sale->disactive == 0) $this->status = 'активно';
        if ($sale->disactive == 1) $this->status = 'удалено';
        if ($sale->disactive == 2) $this->status = 'истекло';

// формируем статистику
        $this->sale_stat = "<i class=\"fa fa-map-marker\" aria-hidden=\"true\"></i> " . number_format($sale->average_price_same, 0, ".", ".") . " (" . $sale->average_price_same_count . ")";
        $this->sale_stat .= "<br><i class=\"fa fa-building-o\" aria-hidden=\"true\"></i> ".number_format($sale->average_price, 0, ".", ".") . " (" . $sale->average_price_count . ")";
        $this->sale_stat .= "<br><i class=\"fa fa-thumb-tack\" aria-hidden=\"true\"></i> " . number_format($sale->average_price_address, 0, ".", ".") . " (" . $sale->average_price_address_count . ")";

        $this->title = $this->rooms_count . "<strong> " . $this->address . "</strong> , " . $this->floors . ", " . $this->house_type . " , " . $this->areas;

        $session = Yii::$app->session;
        $phone = $session->get('phone');
        $phone = explode(",", $phone);

            $tags_sale = RealTags::find()
                ->where(['sale_id' => $sale->id])
                ->asArray()
                ->all();
            $tags = [];
            foreach ($tags_sale as $tag) {
                array_push($tags, $tag['id']);
            }
            $tags_sale = implode(",", $tags);
            $tags_id_address = Addresses::findOne($sale->id_address);
            $tags = $tags_sale.",".$tags_id_address->tags_id;
            //  echo $tags;
            //  echo $tags_id_address->tags_id;
            //  my_var_dump($tags_sale);
            //  my_var_dump(explode(",",$tags_id_address->tags_id));
            if (!empty($tags)) $tags = explode(",",$tags);
            //  my_var_dump($tags);
            $tags = Tags::find()->select(['name','color','type'])->where(['in','id',$tags])->orderBy('type,color')->asArray()->all();
            $grouped_tags = array_group_by($tags,'type');
            //  my_var_dump($tags);
        $tags_render = '';
            foreach ($grouped_tags as $key=>$tags) {

                foreach ($tags as $tag) {
                    $tags_render .= $tag['name'].",";
                }

            }


        $this->title_to_copy = $this->rooms_count . " " . $this->address . " , " . $this->floors . ", " . $this->house_type . " , " . $this->areas . " цена: " . $this->price . ","
            . $tags_render . " телефон " . $phone[0];

        $this->admin_title = $this->url . " id=" . $this->id . ", " . $this->rooms_count . " " . $this->original_address . ", " . $this->id_address . "(" . $address->address . ") , "
            . $this->floors . ", " . $this->house_type . " , " . $this->areas . ", " . $this->year . " status=" . $this->status;
// кнопка добавления tags
        $this->TagRegisterJs = "
        $('#AddTags$this->id').on('shown.bs.modal', function () {
         $('#myInput').focus()
        });";
//        // доступные tags по квартире
//        $Realtags = explode(',', RealTags::find()
//            ->where(['sale_id' => $this->id])
//            ->one()->tags_id);
//        $this->tags = '';
//        foreach ($Realtags as $realtag) {
//            $tag = Tags::findOne($realtag->tag_id);
//            if ($tag) $this->tags .= "<span class=\"badge badge-" . $tag->color . "\">#" . $tag->name . "</span>";
//        }
//        // теги по адресу
//        $Realtags = explode(',', $address->tags_id);
//
//        foreach ($Realtags as $realtag) {
//            $tag = Tags::findOne($realtag);
//            if ($tag) $this->tags .= "<span class=\"badge badge-" . $tag->color . "\">#" . $tag->name . "</span>";
//        }
        $tags_sale = RealTags::find()
            ->where(['sale_id' => $sale->id])
            ->asArray()
            ->all();
        $tags = [];
        foreach ($tags_sale as $tag) {
            array_push($tags, $tag['id']);
        }
        $tags_sale = implode(",", $tags);
//        echo "<br> tags_sale".$tags_sale;
//        echo "<br> tags_address".$address->tags_id;
//        echo "<br> summary ".$tags_sale.",".$address->tags_id;

        $array_tags_id = explode(',', $tags_sale . "," . $address->tags_id);
        $tags = Tags::find()->select('id,type')->where(['in', 'id', $array_tags_id])->andWhere(['parent' => 0])->orderBy('type')->asArray()->all();
        // my_var_dump($tags);
        $grouped_tags = array_group_by($tags, 'type');
        // my_var_dump($grouped_tags);
        if ($grouped_tags) {
            foreach ($grouped_tags as $tags) {
                if ($tags) {
                    foreach ($tags as $tag) {
                        $tag = Tags::findOne($tag['id']);
                        $str_tags .= "<a href='#' data-toggle='tooltip' title='" . $tag->komment . "'><span class='badge badge-" . $tag->color . "'>#" . $tag->name . "</span></a>";
                    }

                    $str_tags .= "<br>";
                }

            }
        }

        $this->tags = $str_tags;


    }

    public
    function phoneToString($phone)
    {
        return substr($phone, 0, 1) . "-" . substr($phone, 1, 3) . "-" . substr($phone, 4, 3) . "-" . substr($phone, 7, 2) . "-" . substr($phone, 9, 2);

    }

}


