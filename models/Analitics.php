<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 08.03.2017
 * Time: 16:09
 */

namespace app\models;
use yii\base\Model;


class Analitics extends Model
{
public $S_max;
public $S_min;
public $floorcount;
public $rooms_count;
public $house_type;


    public function rules()
    {
        return [

            ['S_min', 'integer', 'min' => 1, 'max' => 200],
            ['S_max', 'integer', 'min' => 1, 'max' => 200],
            ['floorcount', 'integer', 'min' => 0, 'max' => 30],
            ['house_type', 'integer', 'min' => 0, 'max' => 5],
            ['rooms_count', 'in', 'range' => [1, 2, 3,4,5,30]],
            [['rooms_count', 'house_type', 'S_max','S_min','floorcount'], 'safe'],

        ];

    }

    public function attributeLabels()
    {
        return [
            'S_max' => 'S_max',
            'S_min' => 'S_min',
            'floorcount' => 'Этажность',
            'rooms_count' => 'rooms_count',
            'house_type' => 'house_type'

        ];
    }





}