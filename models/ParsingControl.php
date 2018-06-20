<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Parsingcontrol".
 *
 * @property integer $id
 * @property integer $date_start
 * @property integer $date_finish
 * @property string $type
 * @property integer $id_sources
 * @property integer $status
 * @property integer $config_id
 * @property string $error
 * @property string $ids_sources
 * @property string $ids
 */
class ParsingControl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parsingcontrol';
    }
    const STATUS_ACTIVE = 1;

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('cloud');
    }

    public static function CreateError($message)
    {
        $new = new ParsingControl();
        $new->date_start = time();
        $new->error = $message;
        $new->status = 5;
        $new ->save();

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_start', 'date_finish', 'id_sources', 'status', 'config_id'], 'integer'],
            [['type'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'type' => 'Type',
            'id_sources' => 'Id Sources',
            'status' => 'Status',
            'config_id' => 'Config ID',
        ];
    }
}
