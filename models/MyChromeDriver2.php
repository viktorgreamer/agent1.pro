<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.09.2017
 * Time: 7:06
 */

namespace app\components;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Cookie;
use Facebook;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use \phpQuery;
use Facebook\WebDriver\WebDriverCapabilities;
use Facebook\WebDriver\Remote\WebDriverCommand;
use Facebook\WebDriver\Remote\DriverCommand;

/**
 * This is the model class for table "Parsing_Configuration".
 *

 */
class MyChromeDriver extends RemoteWebDriver
{
    public $config;
    public $param;
    public $ps;
    public $PageSource;
    static $ip;
    static $connection_timeout = 50000;
    static $timeout_in_second = 20;
    static $interval_in_millisecond = 2000;
    static $request_timeout_in_ms = 20000;
    const ACTION_CONTINUE = 1;

    //КОММАНДЫ БРАУЗЕРУ

    const GET = 10;

    const CLICK = 20;

    const KEYBOARDS = 30;

    const WAIT = 40;
    const PAUSE = 45;
    const RAND_PAUSE = 46;

    const SENDKEYS = 50;

    const PS = 60;

    const VISIBILITY = 70;

    const JAVASCRIPT = 80;


    /**
     * Opening the chromedriver
     */
    /**/


    public static function Open($proxy = '')
    {
        // $proxy = '';
        $host = 'http://localhost:9515/'; // this is the default
        $capabilities = DesiredCapabilities::chrome($proxy);
        $driver = self::create($host, $capabilities, self::$connection_timeout, self::$request_timeout_in_ms);
        $driver->manage()->window()->maximize();
        \Yii::$app->params['driver'] = $driver;
        return $driver;
    }

    public static function getMyIp()
    {
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);
        $ip = $pq->find('.ip')->find('big')->text();
        return $ip;
    }

    /*
     *this method is waiting for an element to click and if it exists click to it.
     *  */

    protected function WaitAndClick($name, $selector_type)
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
    function processing1($params)
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

    protected function decodingSteps($steps)
    {


        switch ($steps[0]) {
            case 'sendkeys':
                echo "<br> sendkeys";
                $WebDriverBy = $this->getSelectors($steps);
                if ($this->isPreg_matchable()) {
                    if ($WebDriverBy instanceof WebDriverBy) {
                        $this->findElement($WebDriverBy)->sendKeys($steps[2]);
                    } else  info('error sendkeys', 'danger');
                } else return false;
                return true;
            case 'sendkeys1':
                echo "<h2> sendkeys1</h2>";
                $WebDriverBy = $this->getSelectors($steps);

                if ($WebDriverBy instanceof WebDriverBy) {
                    $this->findElement($WebDriverBy)->sendKeys($steps[2]);
                } else  info('error sendkeys', 'danger');

                return true;
            case 'click1': // клик без проверки видимости
                echo "<h2> click1</h2>";
                $WebDriverBy = $this->getSelectors($steps);
                if ($WebDriverBy instanceof WebDriverBy) {
                    $this->findElement($WebDriverBy)->click();
                }
                return true;

            case 'click':
                echo "<h2> click</h2>";
                $WebDriverBy = $this->getSelectors($steps);
                if ($WebDriverBy instanceof WebDriverBy) {
                    if ($this->isPreg_matchable()) {
                        $this->findElement($WebDriverBy)->click();
                        return true;
                    } else return false;
                }


            case 'pause':
                echo "<h2> pause</h2>";
                if ($steps[1] == 'rand') {
                    sleep(rand((int)$steps[2], (int)$steps[3]));
                } else {
                    sleep((int)$steps[1]);
                }
                return true;
            case 'get':
                echo "<h2> get</h2>";
                $this->get($steps[1]);

                return true;
            case 'wait':
                echo "<h2> wait</h2>";
                $WebDriverBy = $this->getSelectors($steps);
                if ($WebDriverBy instanceof WebDriverBy) {
                    $element = $this->findElement($WebDriverBy);
                    $this->wait(self::$timeout_in_second, self::$interval_in_millisecond)
                        ->until(WebDriverExpectedCondition::visibilityOf($element));

                }
                return true;
            case 'ps':
                $this->getPs();
                return true;
            case 'refresh':
                $this->navigate()->refresh();
                return true;
            case 'back':
                echo "<h2> back</h2>";
                $this->navigate()->back();
                return true;


        }
    }

    protected function isVisible($steps)
    {
        $pq = phpQuery::newDocument($this->getPageSource());
        if ($pq->find($steps[1])->html()) {
            echo span("ЕЛЕМЕНТ ВИДИМЫЙ");
            return true;
        } else {

            echo span("ЕЛЕМЕНТ  НЕ ВИДИМЫЙ ");
            return false;

        }
    }

    protected function isPreg_matchable()
    {
        if (preg_match("/preg_match{(.+)}/", $this->param, $output_array)) {
            $pattern = "/" . $output_array[1] . "/";
            echo "<br>params = " . $this->param;
            echo "<br>pattern = " . $pattern;
            if (preg_match($pattern, $this->getPageSource())) {
                echo span("ЕЛЕМЕНТ ВИДИМЫЙ");
                return true;
            } else {

                echo span("ЕЛЕМЕНТ  НЕ ВИДИМЫЙ ", 'danger');
                return false;

            }
        };

    }

    protected function getSelectors($steps)
    {
        info("selectors:" . $steps[1]);
        if (preg_match("/^\//", $steps[1], $output_array)) {
            //  info("xpath:".$steps[1]);
            return WebDriverBy::xpath($steps[1]);
        } elseif (preg_match("/^./", $steps[1], $output_array)) {
            info("selectors is CLASS:" . $steps[1]);
            return WebDriverBy::className(preg_replace("/\./", "", $steps[1]));
        } elseif (preg_match("/^#/", $steps[1], $output_array)) {
            return WebDriverBy::id(preg_replace("/#/", "", $steps[1]));
        } else {
            // info($steps[1]);
            $this->quit();
        }

    }


    public
    function processing($params)
    {
//
        if (!empty($params)) {
            //  info('пробегаемся по всем записям массива параметров');
            foreach ($params as $param) {
                $this->param = $param;
                // echo "params ".$param;
                $steps = preg_split("/-->/", $param);
                // my_var_dump($steps);
                if (!$this->decodingSteps($steps)) {
                    echo "<br> searching for error code";

                    preg_match("/error(\d)/", $param, $output_array);
                    $exiting_param = $output_array[1];
                    $this->decodingSteps($params[$exiting_param]);

                    preg_match("/errorcode(\d)/", $param, $output_array);
                    echo "<br> return error code " . $output_array[1];
                    return $output_array[1];
                    break;
                }

            }


        }
        return true;
    }

    public
    function getPs()
    {
        $this->ps = $this->getPageSource();

    }


}