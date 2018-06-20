<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 22.04.2018
 * Time: 16:06
 */

namespace app\utils;


class P
{
    const PAGE_NOT_FOUND = 0;
    const PAGE_EXISTS = 1;

    public static function SearchRussianWords($string)
    {
        $pattern = '/([а-я]+)/ui';
        // находим все русские слова
        preg_match_all($pattern, $string, $matches);
        // если что-то нашлось

      // D::dump($matches);

       return $matches[1];


    }

    public static function ExtractNumders($input)
    {
        return preg_replace("/\D+/", '', $input);
    }


}