<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%test_parsing_years_wikimapia}}".
 *
 * @property integer $id
 * @property integer $is_house
 * @property string $link
 * @property string $street
 * @property integer $year
 */
class ParsingYears extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%test_parsing_years_wikimapia}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_house', 'link', 'street', 'year'], 'required'],
            [['is_house', 'year'], 'integer'],
            [['link'], 'string', 'max' => 100],
            [['street'], 'string', 'max' => 150],
            [['link'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_house' => 'Is House',
            'link' => 'Link',
            'street' => 'Street',
            'year' => 'Year',
        ];
    }
}
