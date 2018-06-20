<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.09.2017
 * Time: 7:06
 */

namespace app\models;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Cookie;
use Facebook;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use phpQuery;

/**
 * This is the model class for table "Parsing_Configuration".
 *

 */
class MyChromeDriver extends RemoteWebDriver
{
    public $config;
    static $ip;
    static $connection_timeout = 50000;

    /**
     * Opening the chromedriver
     */
    public static function Open()
    {
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        return Self::create($host, $capabilities, self::$connection_timeout);
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
    public function waitClick($params)
    {

        if (is_array($params)) {
            $PageSource = $this->getPageSource();
          //  echo $PageSource;
            echo $params[0]['name'];
            $pq = phpQuery::newDocument($PageSource);
          my_var_dump($pq->find('.js-productPage__readMore'));
//            if (phpQuery::newDocument($PageSource)->find($params[0]['name'])->text() != '') {
//                if (preg_match("/…Читать дальше\z/", $this->findElement(WebDriverBy::className('productPage__descriptionText'))->getText(), $output_array))
//                    $this->findElement(WebDriverBy::className('productPage__readMore'))->click();;
//                // $element = $driver->findElement(WebDriverBy::className('productPage__readMore'))->isDisplayed();
//
//            }
//            // если есть кнопка показать телефон то кликаем по ней
//            if (preg_match("/Показать.+телефон/", $this->findElement(WebDriverBy::className('siteBody'))->getText(), $out)) {
//                $this->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['irr']))->click();
//                // ждем пока загрузится элемент div с табличными данными
//                $element = $this->findElement(WebDriverBy::className('productPage__infoColumns_visible'));
//                $this->wait(10, 1000)->until(
//                    WebDriverExpectedCondition::visibilityOf($element)
//                );
//            }
        }
    }


    public function waitContainer()
    {
        $Config_Setting = Self::getConfigurationSetting($this->config);
        $element = $this->findElement(WebDriverBy::className('search-results__serp_type_offers'));
        $this->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOf($element)
        );

    }

}