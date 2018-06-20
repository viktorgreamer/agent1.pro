<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.08.2017
 * Time: 11:54
 */

namespace app\models;


use yii\base\Model;

class RenderSalefilters extends Model
{
    public static function RoomsCount($rooms_count)
    {
        $rooms_count_output = Methods::convertToString($rooms_count);
        $i = 0;
        foreach ($rooms_count as $item) {
            if ($item <= 5) $flats[] = $item;

        }
        $count_flats = count($flats);
        foreach ($rooms_count as $item) {
            $i++;
            $pattern = "/$item/";
            // если квартиры
            if ($item <= 5) {
                if ($i == $count_flats) $rooms_count_output = preg_replace($pattern, $item . "к.кв.", $rooms_count_output);
                else $rooms_count = preg_replace($pattern, "$item", $rooms_count);
            } else {
                // если не квартиры
                if ($item == 20) $rooms_count_output = preg_replace($pattern, "Студию", $rooms_count_output);
                if ($item == 30) $rooms_count_output = preg_replace($pattern, "Комнату", $rooms_count_output);
            }


        }

        return $rooms_count_output;
    }

    public static function PricesCoridor($prefix = '',$salefilter) {
        $body = $prefix;
        if ($salefilter->price_down) $body .= "от ".($salefilter->price_down/1000)."т.р.";
        if ($salefilter->price_up) $body .= "до ".($salefilter->price_up/1000)."т.р.";
        return $body;
        
    }
    public static function SquareCoridor($prefix = '', $salefilter) {
        $body = $prefix;
        if ($salefilter->grossarea_down) $body .= "от ".$salefilter->grossarea_down."м2";
        if ($salefilter->grossarea_up) $body .= "до ".$salefilter->grossarea_up."м2";
        return $body;
        
    }
    public static function YearsCoridor($prefix = '', $salefilter) {
        $body = $prefix;
        if ($salefilter->year_down) $body .= "от ".$salefilter->year_down."г.п.";
        if ($salefilter->year_up) $body .= "до ".$salefilter->year_up." г.п.";
        if ($prefix) return $body;
        
    }
    public static function FloorsCoridor($prefix = '', $salefilter) {
        $body = $prefix;
        if ($salefilter->floor_down) $body .= "от ".$salefilter->floor_down."эт.";
        if ($salefilter->floor_up) $body .= "до ".$salefilter->floor_up."эт.";
        if ($prefix) return $body;

    }
    public static function FloorCountsCoridor($prefix = '', $salefilter) {
        $body = $prefix;
        if ($salefilter->floorcount_down) $body .= "от ".$salefilter->floorcount_down."эт.";
        if ($salefilter->floorcount_up) $body .= "до ".$salefilter->floorcount_up."эт.";
        return $body;

    }
    
    public static function HouseType($prefix, $salefilter) {
        $body = $prefix;
        switch ($salefilter->house_type) {
            case 2; {

               $body .= "кирп.";
            }
            case 1; {

               $body .= "пан.";
            }


            case 3; {

               $body .= "Монолит.";
            }
            case 4; {

               $body .= "Блочный";
            }
            case 5; {

               $body .= "Дер.";
            }


            default: {

               $body .= 'Любой';
            }


        }
        if ($prefix) return $body;
    }
    public static function Type($salefilter) {

        switch ($salefilter->type) {
            case 1; {

               return "<i class=\"fa fa-user\" aria-hidden=\"true\"></i>";
            }



            case 2; {

                return  "<i class=\"fa fa-share\" aria-hidden=\"true\"></i>";
            }
            case 3; {

                return  "<i class=\"fa fa-table\" aria-hidden=\"true\"></i>";
            }
            default; {

                return  "<i class=\"fa fa-question\" aria-hidden=\"true\"></i>";
            }



        }

    }


}