<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.09.2017
 * Time: 7:06
 */

namespace app\models\ChromeDriver;

use app\models\AgentPro;
use app\models\Errors;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Cookie;
use Facebook;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use phpQuery;
use Facebook\WebDriver\WebDriverCapabilities;
use app\models\ChromeDriver\MyHttpCommandExecutor;
use Facebook\WebDriver\Remote\WebDriverCommand;
use Facebook\WebDriver\Remote\DriverCommand;
use app\models\Proxy;
use yii\db\Expression;
use app\models\Sessions;
use Yii;
use app\models\WdCookies;

/**
 * This is the model class for table "Parsing_Configuration".
 *

 */
class MyChromeDriver extends RemoteWebDriver
{
    public $config;
    public $pq;
    public $PageSource;
    public $ip;

    static $connection_timeout = 50000;
    static $timeout_in_second = 10;
    static $interval_in_millisecond = 1000;
    static $request_timeout_in_ms = 100000;

    const CHROME_HOST = 'http://localhost:9515';
    const SESSION_LOST_TIMEOUT = 100;

    const ERROR_LIMIT = 5;
    const ALL_BUSY = 2;

    const NO_PROXY = 1;
    const CURRENT_PROXY = 2;


    /**
     * Opening the chromedriver
     */
    /**/


    public static function Open($proxy = false)
    {
        $freesession = self::getFreeSession();
        if ($freesession === self::ERROR_LIMIT) {
            info(" RETURNING ERROR LIMIT ");
            return self::ERROR_LIMIT;
        } elseif ($freesession) {
            $driver = self::createBySessionID($freesession, self::CHROME_HOST);
            $session = Sessions::find()->where(['id_session' => $freesession])->one();
            $session->status = Sessions::ACTIVE;
            $session->save();
            if (!$session->ip) {
                $driver->getMyIp();
                $session->ip = $driver->ip;
            } else {
                $driver->ip = $session->ip;

            }
            \Yii::$app->params['driver'] = $driver;
            \Yii::$app->params['ip'] = $driver->ip;
            info(" USING SESSION " . $driver->sessionID . " IP = " . $driver->ip);
            return $driver;
        } else {
            return self::OpenNew($proxy);

        }

    }

    public static function OpenNew($proxy = false)
    {

        if ($proxy == self::CURRENT_PROXY) {
            $proxy = Proxy::find()->where(['status' => Proxy::STATUS_ACTIVE])->orderBy(new Expression('rand()'))->one();
            $proxy->updateTime();
            info("USING MANUAL PROXY=" . $proxy->fulladdress);
            $capabilities = self::getProxyCapabilities($proxy->fulladdress);
            $ip = $proxy->ip;
        } elseif (($proxy == self::NO_PROXY) OR ($proxy === false)) {
            $proxy = false;
            $response = file_get_contents('https://api.ipify.org?format=json');
            // info("RESPONSE  = ".$response);
            $ip = json_decode($response, true);
            //  my_var_dump($ip);
            $ip = $ip['ip'];
            $capabilities = DesiredCapabilities::chrome();
        } elseif (filter_var(preg_replace("/:.+/", "", $proxy), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $capabilities = self::getProxyCapabilities($proxy);

            $ip = preg_replace("/:.+/", "", $proxy);
        } else {
            AgentPro::throwError(Errors::findOne(ERROR_FORMAT_WEBDRIVER_PROXY));
        }

        $driver = self::create(self::CHROME_HOST, $capabilities, self::$connection_timeout, self::$request_timeout_in_ms);
        $driver->ip = $ip;
        $driver->start_get('https://2ip.ru/');
        $driver->loadCookies();

        Sessions::create($driver->sessionID, $driver->ip);
        $driver->manage()->window()->maximize();
        \Yii::$app->params['driver'] = $driver;
        \Yii::$app->params['ip'] = $driver->ip;

        return $driver;
    }

    public static function getProxyCapabilities($fulladdress)
    {
        info(" FULL ADDRESS = ".$fulladdress);
        return new DesiredCapabilities(
            [
                Facebook\WebDriver\Remote\WebDriverCapabilityType::BROWSER_NAME => Facebook\WebDriver\Remote\WebDriverBrowserType::CHROME,
                Facebook\WebDriver\Remote\WebDriverCapabilityType::PLATFORM => 'ANY',
                Facebook\WebDriver\Remote\WebDriverCapabilityType::PROXY => [
                    'proxyType' => 'manual',
                    'httpProxy' => $fulladdress,
                    'sslProxy' => $fulladdress,

                ]
            ]);
    }

    public
    function getWithCookies($url)
    {
        Sessions::updateSession($this->sessionID, $url);

        parent::get($url);
        $this->updateCookie();

    }

    // this function uses only then new driver starts for uploading the cookies
    public
    function start_get($url)
    {

        parent::get($url);

    }

    public function updateCookie()
    {
        $cookies = $this->manage()->getCookies();
        if ($cookies) {
            $JSON = [];
            foreach ($cookies as $cookie) {
                $existed_cookie = $cookie->toArray();
                //   my_var_dump($existed_cookie);
                array_push($JSON, $existed_cookie);
            }
        }

        if (!$this->ip) $this->getMyIp();


        $wd_cookies = WdCookies::find()->where(['ip_port' => $this->ip])->andWhere(['id_server' => Yii::$app->params['server']])->one();
        if ($wd_cookies) {
            info("UPDATING EXISTED COOKIES", SUCCESS);
            $wd_cookies->body = json_encode($JSON);
            $wd_cookies->time = time();
            if (!$wd_cookies->save()) my_var_dump($wd_cookies->errors);
        } else {
            info("CREATING NEW COOKIES", DANGER);
            $wd_cookies = new WdCookies();
            $wd_cookies->id_server = Yii::$app->params['server'];
            $wd_cookies->body = json_encode($JSON);
            $wd_cookies->time = time();

            $wd_cookies->ip_port = $this->ip;
            if (!$wd_cookies->save()) my_var_dump($wd_cookies->errors);
        }

    }

    public function loadCookies()
    {
        $found_cookie = WdCookies::find()
            ->where(['id_server' => Yii::$app->params['server']])
            ->andWhere(['ip_port' => $this->ip])
            ->one();
        if ($found_cookie->body) {

            $cookies_from_json = json_decode($found_cookie->body, true);
            if ($cookies_from_json) {
                info("USING EXISTED COOKIES", SUCCESS);
                foreach ($cookies_from_json as $cookie) {
                    if (WdCookies::ValidateCookie($cookie)) {
                        $added_cookie = Cookie::createFromArray($cookie);
                        $this->manage()->addCookie($added_cookie);
                    } else {
                        info("COOKIES NOT VALID", DANGER);
                    }
                    // my_var_dump($cookie);


                }
            } else  info("NO EXISTED COOKIES (((", DANGER);

        } else info("NO EXISTED COOKIES (((", DANGER);


    }


    public
    static function getFreeSession()
    {
        $sessions = self::getAllSessions(self::CHROME_HOST);
        $ids_session = [];
        if ($sessions) {
            foreach ($sessions as $session) {
                $ids_session[] = $session['id'];
                if (!Sessions::find()->where(['id_session' => $session['id']])->one()) {
                    $new_session = new Sessions();
                    $new_session->id_server = Yii::$app->params['server'];
                    $new_session->status = Sessions::ACTIVE;
                    $new_session->id_session = $session['id'];
                    $new_session->datetime_check = time();
                    if (!$new_session->save()) my_var_dump($new_session->getErrors());

                    info("CREATING NEW SESSIONS FROM EXISTED");
                }
            }
        }
        // my_var_dump($ids_session);


        $has_deleted_session = Sessions::updateAll(['status' => Sessions::LOST], ['AND',
            ['not in', 'id_session', $ids_session],
            ['<', 'datetime_check', (time() - 60 * 2)],
            ['id_server' => Yii::$app->params['server']]
        ]);
        $has_deleted_session = Sessions::deleteAll(['AND',
            ['not in', 'id_session', $ids_session],
            ['status' => Sessions::LOST],
            ['<', 'datetime_check', (time() - 60 * 10 *60)],
            ['id_server' => Yii::$app->params['server']]
        ]);

        //  my_var_dump($has_deleted_session);

        if (!$ids_session) info("NO SESSION", 'danger');


        else {

            $free_sessions = Sessions::find()->where(['in', 'id_session', $ids_session])
                ->andWhere(['<', 'datetime_check', (time() - self::SESSION_LOST_TIMEOUT)])->orderBy('datetime_check')->all();
            foreach ($free_sessions as $key => $web_session) {
                if ($key == 0) {
                    $freesession = $web_session->id_session;
                    $web_session->status = Sessions::ACTIVE;
                    $web_session->save();

                } else {
                    $web_session->status = Sessions::FREE;
                    $web_session->save();
                }
                info("SESSION " . $web_session->id_session . " IS FREE FROM " . \Yii::$app->formatter->asRelativeTime($web_session->datetime_check), 'success');
            }
            $not_free_sessions = Sessions::find()->where(['in', 'id_session', $ids_session])
                ->andWhere(['>', 'datetime_check', (time() - self::SESSION_LOST_TIMEOUT)])
                ->andWhere(['<>', 'status', Sessions::LOST])->all();
            foreach ($not_free_sessions as $web_session) {
                info(" SESSION " . $web_session->id_session . " IS NOT FREE", 'danger');
            }

            if ($freesession) {
                return $freesession;
            } else {
                if (count($not_free_sessions) > 5) {
                    info(" CANNOT START CHROME DRIVER BECAUSE 5 SESSIONS ARE RUNNING", DANGER);
                    return self::ERROR_LIMIT;
                }

                return false;
            }

        }


    }

    public
    function getMyIp()
    {
        $this->start_get('https://api.ipify.org?format=json');
        $response = $this->getPageSource();
        $ip = json_decode($response, true);
        //  my_var_dump($ip);
        info("GOT IP = " . $ip['ip']);
        $this->ip = $ip['ip'];
    }

    /*
     *this method is waiting for an element to click and if it exists click to it.
     *  */

    protected
    function WaitAndClick($name, $selector_type)
    {
        if ($selector_type == 'class') {
            $pq = $this->getPq();
            // var_dump( $pq->find(".".$name)->elements);
            if (!empty($pq->find("." . $name)->elements)) {
                //   info(" элемент с классом ( " . $name . " ) существует", 'success');
                $element = $this->findElement(WebDriverBy::className($name));

                if ($this->wait(self::$timeout_in_second, self::$interval_in_millisecond)->until(
                    WebDriverExpectedCondition::visibilityOf($element))
                ) $this->findElement(WebDriverBy::className($name))->click();

            }
            //   else {  info(" элемента с классом ( " . $name . " ) не существует", 'danger');  }

        }
    }


    protected
    function not_exist($name, $selector_type)
    {
        if ($selector_type == 'class') {
            $pq = $this->getPq();
            // var_dump( $pq->find(".".$name)->elements);
            if (empty($pq->find("." . $name)->elements)) {

                //   info(" элемента с классом ( " . $name . " ) не существует", 'danger');
                return true;
            } else {
                // info(" элемент с классом ( " . $name . " ) существует", 'success');
                return false;
            }

        }

    }

    protected
    function WaitForVisibility($name, $selector_type)
    {

        //  info(" проверка существования элемента с классом ( " . $name . " )");
        if ($selector_type == 'class') {
            $element = $this->findElement(WebDriverBy::className($name));

            if ($this->wait(self::$timeout_in_second, self::$interval_in_millisecond)->until(
                WebDriverExpectedCondition::visibilityOf($element))
            ) {
                // info(" элемент с классом ( " . $name . " ) появился", 'success');
                return true;
            }
        } else {
            // info(" элемент с классом ( " . $name . " ) не непоявился", 'danger');
            return false;
        }


    }

    protected
    function sleep($duration)
    {
        // info(" просто пауза");
        sleep($duration);
    }

    protected
    function keyboards($buttons)
    {
        foreach ($buttons as $button) {
            // info("нажимаем на кнопку" . $button);
            if ($button == 'ESCAPE') $button = WebDriverKeys::ESCAPE;
            $this->getKeyboard()->sendKeys($button);
            if ($button == 'ARROW_DOWN') $button = WebDriverKeys::ARROW_DOWN;
            $this->getKeyboard()->sendKeys($button);
        }

    }

    protected
    function sendkeys($params)
    {
        if ($params['selector_type'] == 'xpath') {
            $this->findElement(WebDriverBy::xpath($params['xpath']))->sendKeys($params['keys']);
        }

    }

    protected
    function exception($params)
    {
        if ($params['type'] == 'URL') {
            //   info("обрабатываем URL на pattern= " . $params['pattern']);
            //  echo "<br>" . $this->getCurrentURL();
            if (preg_match($params['pattern'], $this->getCurrentURL(), $output_array)) {
                //   info("есть совпадение", 'success');
                return true;
            }// else  info(" Нет совпадения", 'danger');
        }
        if ($params['type'] == 'TEXT') {
            //  info("обрабатываем TEXT на pattern= " . $params['pattern']);
            //  echo "<br>" . $this->getCurrentURL();
            if (preg_match($params['pattern'], $this->getPq(), $output_array)) {
                //  info("есть совпадение", 'success');
                return true;
            }// else  info(" Нет совпадения", 'danger');
        }

    }

    /*
    *this method for SequentialProcessing.
    *  */
    public
    function SequentialProcessing($params)
    {
//
        if (!empty($params)) {
            //  info('пробегаемся по всем записям массива параметров');
            foreach ($params as $param) {
                // info($param['name'], 'danger');
                if ($param['type'] == 'click') {
                    // info('берем ресурс страницы');
                    //  info("кликаем по элементу с классом (." . $param['name'] . ")");
                    $this->WaitAndClick($param['name'], $param['selector_type']);
                }
                if ($param['type'] == 'exception') {
                    // info('обрабатываем исключение');
                    $params_exception = $param['params'];
                    if ($this->exception($params_exception)) {
                        //   info('сработало исключение');
                        if ($params_exception['success'] == "EXIT") return $params_exception['return'];
                    };
                }

                if ($param['type'] == 'keyboard') {
                    $this->keyboards($param['buttons']);
                }
                if ($param['type'] == 'not_exist') {
                    if ($this->not_exist($param['name'], $param['selector_type'])) {
                        //   info('сработало not_exist');
                        if ($param['success'] == "EXIT") return $param['return'];
                    };


                }
                if ($param['type'] == 'wait') {

                    if ($param['visibility']) {
                        $this->WaitForVisibility($param['name'], $param['selector_type']);
                    }
                    if ($param['pause']) {
                        $this->sleep($param['duration']);
                    }

                }
                if ($param['type'] == 'return') {
                    //  info('возвращаем ресурс страницы');
                    //  echo $this->getPageSource();
                    if ($param['PQ']) return $this->getPq();

                }
                if ($param['type'] == 'sendkeys') {
                    $this->sendkeys($param);
                }
                // echo "<hr>";
                // info('I');
            }


        }
    }

    public
    function getPq()
    {
        return phpQuery::newDocument($this->getPageSource());

    }


}