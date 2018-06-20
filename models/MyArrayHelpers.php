<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13.10.2017
 * Time: 0:37
 */

namespace app\models;


use yii\helpers\ArrayHelper;

class MyArrayHelpers
{
    public static function DeleteFromArray($needle, $array, $all = true)
    {
        if (!$all) {
            $key = array_search($needle, $array);
            if ($key) unset($array[$key]);
            return $array;
        }

        foreach (array_keys($array, $needle) as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    public static function AddOrDelete($array, $item)
    {
        if (!empty($array)) {

            if (in_array($item, $array)) {
              //  my_var_dump($item);
              //  echo " deleting " . $item . " ";
                $array = Self::DeleteFromArray($item, $array);

            } else {
               // echo " adding " . $item . " ";
                array_push($array, $item);
            }
        } else {
            $array = [];
            array_push($array, $item);
          //  echo " adding " . $item . " ";
           // my_var_dump($item);
        }


        return $array;

    }

    public static function RemoveOver(array $array, $field,$percent_up = 20,$percent_down = 20) {
       // роверяем есть ли что-то вообще в массиве
        $count = count($array);

        // если есть то высчитываем среднее значение выбранной колонки
        if ($count) $average = array_sum(ArrayHelper::getColumn($array,$field))/$count;
        $up = $average*(100+$percent_up)/100;
        $down = $average*(100-$percent_down)/100;
        $new_array = [];
        // прогоняем массив  если отклонение в пределах нормы то добавляем в новый массив
        foreach ( $array as $item) {
           // info(" field->".$field." value=".$item[$field]);

            if (($item[$field] > $down) and ($item[$field] < $up)) {
              //  info("входит",'alert');
                array_push($new_array,$item);
            }

        }
        return $new_array;
    }

}