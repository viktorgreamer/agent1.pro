<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\AgentPro;
use app\models\ControlParsing;
use app\models\Errors;
use app\models\Notifications;
use app\models\Renders;
use yii\console\Controller;
use app\models\Control;
use yii;
use app\models\ChromeDriver\MyChromeDriver;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($server = 1)
    {
        Yii::$app->params['server'] = $server;
        Notifications::VKMessage("using server " . $server);

        $driver = MyChromeDriver::Open();
        $driver->get("https://www.yandex.ru/");

        sleep(5);

        $driver->get("https://www.google.com/");

        echo "using server " . $server . "\n";
    }

    public function actionMain()
    {


        Control::mainScript();

    }

    public function actionNote()
    {
        $array = [1, 2, 3, 4, 5, 6, 7, 8];

        foreach ($array as $item) {
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '30865909');
            sleep(0.2);
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '4925790');
            sleep(0.3);
            Notifications::VKMessage(substr(md5(rand()), 0, 10));
            sleep(0.4);
        }
        sleep(5);
        $array = [1, 2, 3, 4, 5, 6, 7, 8];

        foreach ($array as $item) {
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '30865909');
            sleep(0.2);
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '4925790');
            sleep(0.3);
            Notifications::VKMessage(substr(md5(rand()), 0, 10));
            sleep(0.4);
        }

        sleep(5);
        $array = [1, 2, 3, 4, 5, 6, 7, 8];

        foreach ($array as $item) {
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '30865909');
            sleep(0.2);
            Notifications::VKMessage(substr(md5(rand()), 0, 10), '4925790');
            sleep(0.3);
            Notifications::VKMessage(substr(md5(rand()), 0, 10));
            sleep(0.4);
        }

    }

    public function actionMainCloud($server = 'default')
    {


        Control::mainScriptCloud();

    }

    public function actionServerName()
    {
        AgentPro::getActive();


    }


}
