<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.03.2018
 * Time: 15:28
 */

namespace app\models;

use app\models\MyArrayHelpers;


class Methods
{

    public static function isInList($needle,$list) {
        if (in_array($needle,Methods::convertToArrayWithBorders($list))) return true; else return false;
    }

    // метод для удаления элемента из списка с границами ",";
    public static function removeFromList($needle, $haystack)
    {

        return Methods::convertToStringWithBorders(MyArrayHelpers::DeleteFromArray($needle, Methods::convertToArrayWithBorders($haystack)));

    }

    public static function addToList($needle, $haystack)
    {
        $array = Methods::convertToArrayWithBorders($haystack);
        array_push($array, $needle);
        return Methods::convertToStringWithBorders($array);

    }

    public static function convertToStringWithBorders(array $arr)
    {
        if (!empty($arr)) return "," . implode(",", $arr) . ",";
        else return '';
    }

    public static function convertToArrayWithBorders($string)
    {
        $string = preg_replace("/^,|,$/", "", $string);
        if ($string) {
            $response = explode(",",$string);
            if (is_array($response)) return $response;
            elseif (is_int($response)) return [$response];
        } else return [];
    }

    public static function convertToString(array $arr)
    {
        if (!empty($arr)) return implode(",", $arr);
        else return '';
    }

    public static function convertToArray($string)
    {
        if ($string) {
            $response = explode(",", $string);
            if (is_array($response)) return $response;
            elseif (is_int($response)) return [$response];
        } else return [];
    }


}