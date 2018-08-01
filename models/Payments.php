<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property integer $id
 * @property integer $time
 * @property integer $user_id
 * @property integer $value
 */
class Payments extends \yii\db\ActiveRecord
{

    const ONE_TIME1 = 'onetime1';
    const ONE_TIME2 = 'onetime2';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments';
    }
    public static function mapPlans()
    {
        return [
            self::ONE_TIME1 => "РАЗОВЫЙ-1",
            self::ONE_TIME2 => "РАЗОВЫЙ-2"
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time', 'user_id', 'value'], 'required'],
            [['id', 'time', 'user_id', 'value'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => 'Time',
            'user_id' => 'User ID',
            'value' => 'Value',
        ];
    }
}
