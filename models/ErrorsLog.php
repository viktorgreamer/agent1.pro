<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "app_errors_log".
 *
 * @property integer $id
 * @property integer $id_error
 * @property integer $time
 */
class ErrorsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_errors_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_error', 'time'], 'required'],
            [['id_error', 'time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_error' => 'Id Error',
            'time' => 'Time',
        ];
    }
}
