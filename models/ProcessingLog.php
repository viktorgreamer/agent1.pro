<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Velikiy_Novgorod_processing_log".
 *
 * @property integer $id
 * @property integer $time
 * @property integer $type
 * @property integer $sale_id
 * @property integer $was
 * @property integer $now
 */
class ProcessingLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Velikiy_Novgorod_processing_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['time', 'type','sale_id'], 'required'],
            [['time', 'type', 'was', 'now'], 'integer'],
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
            'type' => 'Type',
            'was' => 'Was',
            'now' => 'Now',
        ];
    }
}
