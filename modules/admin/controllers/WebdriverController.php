<?php

namespace app\modules\admin\controllers;

use app\models\AgentPro;
use app\models\ChromeDriver\MyChromeDriver;
use app\models\Control;
use app\models\ControlParsing;
use app\models\Notifications;
use app\models\Sessions;
use app\models\WdCookies;
use Facebook\WebDriver\Cookie;
use Yii;
use yii\helpers\ArrayHelper;


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
        $driver->switchTo()->alert()->sendKeys('Test'); // enter text
        $driver->switchTo()->alert()->accept();
        info("IP=" . $driver->ip);

        // $driver->get('https://yandex.ru/internet/');

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
        echo "<br>" . $driver->getCurrentURL();

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
            ->where(['id_server' => Yii::$app->params['server']])
            ->andWhere(['ip_port' => $wd_cookes->ip_port])
            ->one();
        if ($found_cookie) {
            $cookies_from_json = json_decode($found_cookie->body, true);
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

    public function actionErrorControl()
    {
      /*  $interval = 90;
        $brokenControls = ControlParsing::find()
            ->select(['id','type','date_start','ids','ids_sources'])
            ->where(['in', 'status', [ControlParsing::BROKEN]])
            ->andWhere([">", 'date_start', (time() - $interval * 60)])
            // ->groupBy('type')
            ->asArray()
            ->all();

        my_var_dump($brokenControls);
        foreach (Control::mapTypesControls() as $key => $control) {
            if ($array = array_filter($brokenControls, function ($item) use ($key) {
                return $item['type'] == $key;
            })) {
                if (count($array) > 5) {
                    $message = " CONTROL ".$control." IS BROKEN ".count($array)." РАЗ ЗА ПОСЛЕДНИЕ ".$interval." МИНУТ";
                    info($message);
                    if ($ids_broken = ArrayHelper::getColumn($array,'id')) {
                        $ids = [];
                        foreach ($ids_broken as $item) {

                            array_push($ids,intval($item));
                        }

                    }


                    my_var_dump($ids_broken = array_values ($ids_broken));
                    ControlParsing::updateAll(['status' => ControlParsing::FULL_BROKEN],['in','id',$ids]);
                   // AgentPro::throwError();

                }
              }


        }

        info("COUNT = " . count($brokenControls));*/


        $Broken_Controls = ControlParsing::find()
            ->select('ids_sources')
            ->distinct()->where(['in', 'status', [1, 4]])
            ->andWhere(['in', 'type', [Control::P_NEW, Control::P_SYNC]])
            // ->andwhere(['module_id' => $this->id])
            ->column();


        my_var_dump($Broken_Controls);
        if ($Broken_Controls) {
            $total = '';
            foreach ($Broken_Controls as $broken_Control) {
                $total .= ",".$broken_Control;
            }
            $total = explode(",", $total);
            $total = array_values(array_unique($total));
            $total =  array_diff($total, array('',0,null));

        }


        my_var_dump($total);

        $Broken_Controls = ControlParsing::find()
            ->select('ids_sources')
            ->distinct()->where(['in', 'status', [1, 4]])
            ->andWhere(['in', 'type', [Control::P_APHONES]])
            // ->andwhere(['module_id' => $this->id])
            ->column();


        my_var_dump($Broken_Controls);
        if ($Broken_Controls) {
            $total = '';
            foreach ($Broken_Controls as $broken_Control) {
                $total .= ",".$broken_Control;
            }
            $total = explode(",", $total);
            $total = array_values(array_unique($total));
            $total =  array_diff($total, array('',0,null));

        }


        my_var_dump($total);

        ControlParsing::deleteAll(['status' => 4]);

    }


}
