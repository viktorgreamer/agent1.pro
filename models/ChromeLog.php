<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chromedriver".
 *
 * @property integer $id
 * @property string $session_id
 * @property string $model
 */
class ChromeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chromedriver';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('cloud');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['session_id', 'model'], 'required'],
            [['model'], 'string'],
            [['session_id'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'model' => 'Model',
        ];
    }
}
