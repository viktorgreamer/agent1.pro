<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proxy".
 *
 * @property integer $id
 * @property string $ip
 * @property string $port
 * @property string $fulladdress
 * @property string $login
 * @property string $password
 */
class Proxy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const STATUS_DISACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_NO_INTERNET = 2;

    public static function renderStatus($status = 1)
    {
        $array = [
            Proxy::STATUS_DISACTIVE => 'DISACTIVE',
            Proxy::STATUS_ACTIVE => 'ACTIVE',
            Proxy::STATUS_NO_INTERNET => 'NO_INTERNET',
        ];
        return $array[$status];

    }

    public function updateTime()
    {
        $this->time = time();
        $this->save();
    }

    public static function tableName()
    {
        return 'proxy';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip', 'port', 'fulladdress'], 'required'],
            [['ip'], 'unique'],
            [['ip'], 'string', 'max' => 20],
            [['add_time'], 'integer'],
            [['port'], 'string', 'max' => 10],
            [['fulladdress'], 'string', 'max' => 30],
            [['login', 'password'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'port' => 'Port',
            'fulladdress' => 'Fulladdress',
            'login' => 'Login',
            'password' => 'Password',
        ];
    }
}
