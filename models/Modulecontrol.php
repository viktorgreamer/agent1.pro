<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "modulecontrol".
 *
 * @property integer $id
 * @property string $name
 * @property string $list_of_cities
 * @property string $last_parsing
 */
class Modulecontrol extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modulecontrol';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'list_of_cities', 'last_parsing'], 'required'],
            [['list_of_cities'], 'string'],
            [['last_parsing'], 'safe'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название регоина',
            'list_of_cities' => 'Список городов через ,',
            'last_parsing' => 'время последнее порсинга',
        ];
    }

    public function change_status($status) {
        $module = Modulecontrol::findOne($this->id);
        $module->status = $status;
       if ($module->save()) return true;


    }
}
