<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Velikiy_Novgorod_sale_log".
 *
 * @property integer $id
 * @property integer $sale_id
 * @property integer $date
 * @property integer $type
 * @property string $was
 * @property string $now
 */
class SaleLog extends \yii\db\ActiveRecord
{

    const PRICE_CHANGED = 4;
    const ADDRESS_CHANGED = 5;
    const DATESTART_UPDATED = 7;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Velikiy_Novgorod_sale_log';
    }

    public static function mapTypes()
    {
        return [
            self::PRICE_CHANGED => "PRICE_CHANGED",
            self::ADDRESS_CHANGED => "ADDRESS_CHANGED",
            self::DATESTART_UPDATED => "DATESTART_UPDATED",
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sale_id', 'type'], 'integer'],
            [['was', 'now'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sale_id' => 'Sale ID',
            'date' => 'Date',
            'type' => 'Type',
            'was' => 'Was',
            'now' => 'Now',
        ];
    }
}
