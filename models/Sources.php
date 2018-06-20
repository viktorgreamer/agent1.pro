<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "app_sources".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 */
class Sources extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_sources';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['link'], 'string'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    public static function getMap() {
        return ArrayHelper::map(self::find()->all(),'id','name');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'link' => 'Link',
        ];
    }
}
