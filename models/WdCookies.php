<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wd_cookies".
 *
 * @property integer $id
 * @property string $ip_port
 * @property string $id_server
 * @property string $body
 * @property integer $time
 */
class WdCookies extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wd_cookies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip_port', 'id_server', 'body', 'time'], 'required'],
            [['body'], 'string'],
            [['time'], 'integer'],
            [['ip_port'], 'string', 'max' => 30],
            [['id_server'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip_port' => 'Ip Port',
            'id_server' => 'Id Server',
            'body' => 'Body',
            'time' => 'Time',
        ];
    }

    public static function ValidateCookie($cookie) {
        if ((self::validateCookieName($cookie['name'])) AND (self::validateCookieValue($cookie['value']))) return true;
    }

    public static function validateCookieName($name)
    {
        if ($name === null || $name === '') {
           return false;
        }

        if (mb_strpos($name, ';') !== false) {
           return false;
        }
        return true;
    }

    /**
     * @param string $value
     */
    public static function validateCookieValue($value)
    {
        if ($value === null) {
            return false;
        } else {
            return true;
        }
    }
}
