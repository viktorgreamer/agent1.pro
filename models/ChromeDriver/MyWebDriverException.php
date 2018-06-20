<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 06.12.2017
 * Time: 18:05
 */

namespace app\models\ChromeDriver;
use Facebook\WebDriver\Exception\WebDriverException;


class MyWebDriverException extends WebDriverException
{

    public static function throwException($status_code, $message, $results)
    {
        switch ($status_code) {
            case 1:
                throw new IndexOutOfBoundsException($message, $results);
            case 2:
                throw new NoCollectionException($message, $results);
            case 3:
                throw new NoStringException($message, $results);
            case 4:
                throw new NoStringLengthException($message, $results);
            case 5:
                throw new NoStringWrapperException($message, $results);
            case 6:
                throw new NoSuchDriverException($message, $results);
            case 7:
            {
                echo "NoSuchElementException";
                 throw new NoSuchElementException($message, $results);
                return false;
                break;
            }

            case 8:
                throw new NoSuchFrameException($message, $results);
            case 9:
                throw new UnknownCommandException($message, $results);
            case 10:
                throw new StaleElementReferenceException($message, $results);
            case 11:
                throw new ElementNotVisibleException($message, $results);
            case 12:
                throw new InvalidElementStateException($message, $results);
            case 13:
                throw new UnknownServerException($message, $results);
            case 14:
                throw new ExpectedException($message, $results);
            case 15:
                throw new ElementNotSelectableException($message, $results);
            case 16:
                throw new NoSuchDocumentException($message, $results);
            case 17:
                throw new UnexpectedJavascriptException($message, $results);
            case 18:
                throw new NoScriptResultException($message, $results);
            case 19:
                throw new XPathLookupException($message, $results);
            case 20:
                throw new NoSuchCollectionException($message, $results);
            case 21:
                throw new TimeOutException($message, $results);
            case 22:
                throw new NullPointerException($message, $results);
            case 23:
                throw new NoSuchWindowException($message, $results);
            case 24:
                throw new InvalidCookieDomainException($message, $results);
            case 25:
                throw new UnableToSetCookieException($message, $results);
            case 26:
                throw new UnexpectedAlertOpenException($message, $results);
            case 27:
                throw new NoAlertOpenException($message, $results);
            case 28:
                throw new ScriptTimeoutException($message, $results);
            case 29:
                throw new InvalidCoordinatesException($message, $results);
            case 30:
                throw new IMENotAvailableException($message, $results);
            case 31:
                throw new IMEEngineActivationFailedException($message, $results);
            case 32:
                throw new InvalidSelectorException($message, $results);
            case 33:
                throw new SessionNotCreatedException($message, $results);
            case 34:
                throw new MoveTargetOutOfBoundsException($message, $results);
            default:
                throw new UnrecognizedExceptionException($message, $results);
        }
    }

}