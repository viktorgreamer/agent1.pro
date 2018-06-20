<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2017
 * Time: 17:10
 */
namespace app\models\ChromeDriver;

use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\NoSuchElementException;

use Facebook\WebDriver\WebDriverWait;

class MyWebDriverWait extends WebDriverWait
{
    public function until($func_or_ec, $message = '')
    {
        $end = microtime(true) + $this->timeout;
        $last_exception = null;

        while ($end > microtime(true)) {
            try {
                if ($func_or_ec instanceof WebDriverExpectedCondition) {
                    $ret_val = call_user_func($func_or_ec->getApply(), $this->driver);
                } else {
                    $ret_val = call_user_func($func_or_ec, $this->driver);
                }
                if ($ret_val) {
                    return $ret_val;
                }
            } catch (NoSuchElementException $e) {
                $last_exception = $e;
            }
            usleep($this->interval * 1000);
        }

        if ($last_exception) {

            throw $last_exception;
        }
        throw new TimeOutException($message);


    }
}
