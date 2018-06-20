<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parsingwikimapia".
 *
 * @property integer $id
 * @property string $city
 * @property string $source
 * @property string $link
 * @property integer $is_parsed
 * @property string $coords_x
 * @property string $coords_y
 * @property string $address
 * @property integer $year
 */
class Parsingwikimapia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parsingwikimapia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['link'], 'required'],
            [['is_parsed', 'year'], 'integer'],
            [['city'], 'string', 'max' => 125],
            [['source'], 'string', 'max' => 50],
            [['link'], 'string', 'max' => 255],
            [['coords_x', 'coords_y'], 'string', 'max' => 10],
            [['address'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'City',
            'source' => 'Source',
            'link' => 'Link',
            'is_parsed' => 'Is Parsed',
            'coords_x' => 'Coords X',
            'coords_y' => 'Coords Y',
            'address' => 'Address',
            'year' => 'Year',
        ];
    }
}
