<?php

namespace app\modules\admin\controllers;

use app\models\AgentPro;
use app\models\ChromeDriver\MyChromeDriver;
use app\models\Sessions;
use app\models\WdCookies;
use Facebook\WebDriver\Cookie;
use Yii;


class WebdriverController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTestIp()
    {
        AgentPro::getActive();
        $driver = MyChromeDriver::OpenNew("194.242.125.25:8000");
        info("IP=".$driver->ip);

        $driver->get('https://yandex.ru/internet/');

        return $this->render('index');
    }

    public function actionCookies()
    {
       $agentpro = AgentPro::getActive();
        // echo MyChromeDriver::getFreeSession();
        $driver = MyChromeDriver::createBySessionID('f11f0bf14c5b179c80b146110e983825', MyChromeDriver::CHROME_HOST);
$session = Sessions::find()->where(['id_session' => 'f11f0bf14c5b179c80b146110e983825'])->one();
        //        $driver->get('https://yandex.ru/internet/');
//
//        $driver->get('https://yiiframework.ru/forum/');
//
//
//        $driver->get('https://www.avito.ru/moskva?verifyUserLocation=1');
        $cookies = $driver->manage()->getCookies();
        echo "<br>".$driver->getCurrentURL();

        echo "<div class='row'><div class='col-lg-6'>";
        info(" COOKIE 1 ");
        info(" COUNT " . count($cookies));


        if ($cookies) {
            $JSON = [];
            foreach ($cookies as $cookie) {
                $existed_cookie = $cookie->toArray();
                my_var_dump($existed_cookie);
                array_push($JSON, $existed_cookie);

                //   if ($existed_cookie['expiry'])   echo "<br>". $existed_cookie['expiry']."->>" .Yii::$app->formatter->asDuration($existed_cookie['expiry'] - time());
            }
        }

        echo "</div><div class='col-lg-6'>";
        $wd_cookes = new WdCookies();
        $wd_cookes->id_server = Yii::$app->params['server'];
        $wd_cookes->body = json_encode($JSON);
        $wd_cookes->time = time();
        $wd_cookes->ip_port = 'JKJKJKJ';
       if (!$wd_cookes->save()) my_var_dump($wd_cookes->errors);

        $found_cookie = WdCookies::find()
            ->where(['id_server' =>  Yii::$app->params['server']])
            ->andWhere(['ip_port' => $wd_cookes->ip_port])
            ->one();
        if ($found_cookie) {
            $cookies_from_json = json_decode($found_cookie->body,true);
        } else {
            info("NOT FOUND COOKIES");
            die();
        }

//        file_put_contents("cookie.json", json_encode($JSON));
//        $cookies_from_json = json_decode(file_get_contents("cookie.json"), true);
//        //  my_var_dump($cookies_from_json);

        $driver2 = MyChromeDriver::createBySessionID('5982651524c3b0d4a4480a21dda607dc', MyChromeDriver::CHROME_HOST);
        $driver2->manage()->deleteAllCookies();
        foreach ($cookies_from_json as $cookie) {
           //  my_var_dump($cookie);
            $driver2->manage()->addCookie(Cookie::createFromArray($cookie));

        }

        info(" COOKIE 2");
        //  my_var_dump($driver2->manage()->getCookies());
        $cookies2 = $driver2->manage()->getCookies();
        info(" COUNT " . count($cookies2));

        if ($cookies2) {
            foreach ($cookies2 as $cookie2) {
                $existed_cookie = $cookie2->toArray();
                my_var_dump($existed_cookie);

                //  if ($existed_cookie['expiry']) echo "<br>" . $existed_cookie['expiry'] . "->>" . Yii::$app->formatter->asDuration($existed_cookie['expiry'] - time());
            }
        }
        echo "</div>";

        $driver2->get('https://www.avito.ru/profile');
//

        return $this->render('index');
    }


}
