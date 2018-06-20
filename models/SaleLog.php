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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Velikiy_Novgorod_sale_log';
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
