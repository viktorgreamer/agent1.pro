<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 16.05.2018
 * Time: 10:45
 */

namespace app\models;


class Notifications
{
    const VIKTOR_ID = "907609";
  //  const VIKTOR_ID = "vasilyev_1983";
    const SYSTEM_MESSAGE = 1;

    const  VK_TOKEN = '39d97c8dead8d9d14b6083101d65cf017807e74c5020fb4b775414f14be0a54d08ab0e199a3ac213f330c';


    const VK_API_VERSION = '5.50'; //Используемая версия API
    const VK_API_ENDPOINT = "https://api.vk.com/method/";


    const COLOR_NEGATIVE = 'negative';
    const COLOR_POSITIVE = 'positive';
    const COLOR_DEFAULT = 'default';
    const COLOR_PRIMARY = 'primary';
    const CMD_ID = 'ID';
    const CMD_NEXT = 'NEXT';
    const CMD_TYPING = 'TYPING';

    public static function getBtn($label, $color, $payload = '') {
        return [
            'action' => [
                'type' => 'text',
                "payload" => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'label' => $label
            ],
            'color' => $color
        ];
    }






//Функция для вызова произвольного метода API
    public static function _vkApi_call($method, $params = array())
    {
        $params['access_token'] = self::VK_TOKEN;
        $params['v'] = self::VK_API_VERSION;
        $url = self::VK_API_ENDPOINT . $method . '?' . http_build_query($params);
       // echo $url;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($json, true);
        return $response['response'];


    }

//Функция для вызова messages.send
    public static function vkApi_messagesSend($peer_id, $message, $keybords = [], $attachments = array())
    {
        return self::_vkApi_call('messages.send', array(
            'peer_id' => $peer_id,
            'message' => $message,
            'keyboard' => json_encode($keybords, JSON_UNESCAPED_UNICODE),
            'attachment' => implode(',', $attachments)
        ));
    }

    public static function VKMessage($message,$toId = 0)
    {
      if ($toId) Notifications::vkApi_messagesSend($toId, $message);
      else Notifications::vkApi_messagesSend(self::VIKTOR_ID, $message);
    }

    public static function Mail($message = '',$toEmail = 'an.viktory@gmail.com') {
        if ($message) {
            \Yii::$app->mailer->compose()
                ->setTo($toEmail)
                ->setFrom(['info@mirs.pro' => 'mirs.pro'])
                ->setSubject("NEW ITEM")
                ->setTextBody($message)
                ->send();
        }

    }




}