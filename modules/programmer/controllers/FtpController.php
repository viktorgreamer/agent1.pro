<?php

namespace app\modules\programmer\controllers;

use app\models\AgentPro;
use yii2mod\ftp\FtpClient;

class FtpController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionAlias()
    {

         return $this->render('index');
    }

    public function actionConnect()
    {
        $directory = '/./domains/mirs.pro/public_html/web/errors/';

        $ftp_server = '141.8.195.92';
        $ftp_user_name = 'a0086640';
        $ftp_user_pass = 'ucbueptuke';

        $file = 'new_filfe.html';
        $filebody = "asfasdasdasdasda
        sdasda
        sdasda
        sd
        asdasd
        asJSNJDNSJNDJSNJDNSJNDJSNDJN";
        file_put_contents("ftp://".$ftp_user_name.":".$ftp_user_pass."@".$ftp_server.$directory."/".$file, $filebody);

//        file_put_contents($file,$filebody);
//
//        $fp = fopen($file, 'r');
//        fwrite($fp,$file);
//
//
//
//
//        $conn_id = ftp_connect($ftp_server);
//
//// вход с именем пользователя и паролем
//        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
//
//// проверка соединения
//        if ((!$conn_id) || (!$login_result)) {
//            echo "Не удалось установить соединение с FTP-сервером!";
//            echo "Попытка подключения к серверу $ftp_server была произведена под именем $ftp_user_name";
//            exit;
//        } else {
//            echo "Установлено соединение с FTP сервером $ftp_server под именем $ftp_user_name";
//        }
//        $dirs = ftp_nlist ( $conn_id , './domains/mirs.pro/public_html/web/errors/');
//
//        my_var_dump($dirs);
//
//        ftp_chdir ( $conn_id , $directory );
//
//        // попытка загрузки файла
//        if (ftp_fput($conn_id, $file, $fp, FTP_ASCII)) {
//            echo "Файл $file успешно загружен\n";
//        } else {
//            echo "При загрузке $file произошла проблема\n";
//        }
//
//// закрываем соединение и дескриптор файла
//        ftp_close($conn_id);
//        chmod($file, 777);
//
//        unlink(  $file);


        return $this->render('index');
    }

}
