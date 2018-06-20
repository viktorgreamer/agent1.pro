<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.05.2017
 * Time: 22:04
 */

namespace app\controllers;


use app\models\Addresses;
use app\models\ChromeDriver\MyChromeDriver;
use app\models\Control;
use app\models\Parsing;
use app\models\ParsingConfiguration;
use app\models\ParsingExtractionMethods;
use app\models\Synchronization;
use app\models\Tags;
use yii\web\Controller;
use phpQuery;
use app\models\Sale;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use Yii;
use app\models\ParsingSourceModels\Cian;

class ParsingController extends Controller
{

    public function actionTestChromeDriver()
    {
        info(" testing chrome driver", 'success');
        $driver = MyChromeDriver::Open();
        $driver->get('https://m.avito.ru/velikiy_novgorod/kvartiry/1-k_kvartira_37_m_110_et._1195837926');
        $params = Parsing::sourceSequential()[31];
        // sleep(2);
        // $driver->get('https://realty.yandex.ru/offer/6159590783228987137/');
        //  $driver->get('http://velnovgorod.irr.ru/real-estate/apartments-sale/secondary/1-komn-kvartira-bol-shaya-sankt-peterburgskaya-ul-advert676396069.html');
        //  $driver->get('http://velnovgorod.irr.ru/real-estate/apartments-sale/secondary/1-komn-kvartira-nehinskaya-ul-34-advert661873058.html');
        //   $driver->get('https://velnovgorod.irr.ru/real-estate/apartments-sale/secondary/prodam-1-komn-kvartiru-obschaya-ploschad-37-3-kv-m-advert671311170.html');
        // возвращаем объект phpQuery страницы
// $driver->get('https://www.avito.ru/velikiy_novgorod/kvartiry/3-k_kvartira_75.6_m_25_et._1013999146');
//        $params = [
//            0 => ['type' => 'click', 'selector_type' => 'class', 'name' => 'js-item-phone-number'],
//            1 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'js-item-phone-popup-content' ],
//            2 => ['type' => 'keyboard', 'buttons' => [ 0 => 'ESCAPE']],
//            3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(5,7)],
//            4 => ['type' => 'click', 'selector_type' => 'class','name' => 'seller-info-items-link']
//        ];
        $response = $driver->SequentialProcessing($params);
        sleep(3);
        if ($response == 'DELETED') info('ELEMENT_DELETED', 'danger');
        elseif ($response == 'DISABLED') info('ELEMENT_DISABLED', 'danger');
        else echo $response;

        $driver->quit();
        return $this->render('index');
    }

    public function actionChromeWindowsHandler()
    {
        info(" testing chrome driver", 'success');
        $driver = MyChromeDriver::Open();
        sleep(3);
        info($driver->getSessionID());
        my_var_dump($driver->manage()->getCookies());
        $driver->get('https://yandex.ru/internet/');
        my_var_dump($driver->manage()->getCookies());
        echo $driver->getPageSource();
        $driver->quit();
        return $this->render('index');
    }

    public function actionTestCache()
    {
        info(" testing caching", 'success');
        if (empty(Yii::$app->cache->get('tags'))) {
            info('устанавливаем cache', 'alert');
            Yii::$app->cache->set('tags', Tags::find()->asArray()->all());
        } else {
            info('cache установлен', 'success');
            // my_var_dump(Yii::$app->cache->get('tags'));
            $tags = [3, 7, 9];
            echo Tags::render($tags);
        }
        return $this->render('index');
    }


    public function actionCurl()
    {
        $page = 35;
        $start_url = "https://realty.yandex.ru/velikiy_novgorod/kupit/kvartira/dvuhkomnatnaya/";
        $str_page = "?page=" . $page;

        $url = $start_url . "" . $str_page;
        echo $url;

        $ch = curl_init($url);
//Установка опций
        $uagent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";
        curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        //  curl_setopt($ch, CURLOPT_REFERER, 'https://realty.yandex.ru/');
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//выполнение (результат отобразится на экран)
        echo "<pre>";
        $html = curl_exec($ch);
        $info = curl_getinfo($ch);
        var_dump($info);
        echo "</pre>";
//Закрытие соединения
        curl_close($ch);
        echo "<br>";
        if ($info['http_code'] == 302) echo " траница не найдена";
        if ($info['http_code'] == 200) echo " траница найдена";
        // echo $html;

        //$file = file_get_contents($html);
        $pq = phpQuery::newDocument($html);

        $str1 = $pq->find('.offer-card__main-feature-title')->text();
        preg_match('/[1,2][9,0]\d\d/', $str1, $output_array);
        //  preg_match("/\d\d\d\d/", $str1, $output_array);
        echo $output_array[0];
        // $str2 = $pq->find('#topAdv1')->text();
        //echo $str1;
    }


    public function actionYandexParserReset()
    {
        $parsing_objects = Parsing::find()
            ->andwhere(['source' => 'yandex-control'])
            ->one();
        $parsing_objects->year = 0;
        $parsing_objects->save();


    }


    public function actionCurlPost()
    {
        $post = [
            'dest' => 'http://dom.mingkh.ru ',
            'current' => 1,
            'rowCount' => -1,
            'searchPhrase' => '',
            'region_url' => 'novgorodskaya-oblast',
            'city_url' => 'velikiy-novgorod'

        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://dom.mingkh.ru/api/houses');
        // curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"current":1}{"rowCount":10}{"searchPhrase":""}{"region_url":"novgorodskaya-oblast"}{"city_url":"velikiy-novgorod"}');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0');
        $data = curl_exec($ch);
        curl_close($ch);
        echo $data;
        return $this->render('index');

    }

    public function actionFillTheOriginalDate()
    {

        $sales = Sale::find()
            // ->where(['rooms_count' => 1])
            ->andwhere(['id_sources' => 3])
            ->andwhere(['original_date' => 0])
            ->limit(10)
            ->all();
        echo "count cost" . Sale::find()
                // ->where(['rooms_count' => 1])
                ->andwhere(['id_sources' => 3])
                ->andwhere(['original_date' => 0])
                ->count();
        $proxy = '';
        foreach ($sales as $sale) {
            echo $sale->url;
            $id = array_pop(explode("_", $sale->url));

            $response = curl_response($sale->url);
            $pq = phpQuery::newDocument($response);
            $str1 = $pq->find('.js-show-stat')->text();
            echo "<h1>" . $str1 . "</h1>";
            preg_match("/\d+/", $str1, $output_array);
            echo " <b>" . $output_array[0] . "</b>";
            $views = $output_array[0];

            dlPage3('https://www.avito.ru/img/be0a491f326ef3b16d18fa45dd4ed7.gif', $proxy, $sale->url);
            $ended2 = dlPage4("https://www.avito.ru/items/stat/$id?step=0", $proxy, $sale->url);
            // echo $ended2;
            $pq = phpQuery::newDocument(gzdecode($ended2));
            echo "<pre>";
            // var_dump($pq);
            echo "</pre>";
            $date_of_start = str_replace('Дата подачи объявления: ', '', $pq->find('.item-stats__date')->text());
            echo $date_of_start;
            echo date_to_unix($date_of_start);
            echo "</br>";
            sleep(1);
            $views_per_day = $output_array[0] / (time() - date_to_unix($date_of_start)) * 60 * 60 * 24;
            echo "<h1>" . round($views_per_day) . "</h1>";
            $sale->set_original_date(date_to_unix($date_of_start));
            $sale->set_views($views);
        }


    }

    public function actionChecker()
    {

        $sales = Sale::find()
            // делаем выборку всех где время проверки меньше суток
            ->where(['<', 'date_of_check', time() - 24 * 60 * 60])
            ->andWhere(['in', 'id_sources', [1]])
            ->andWhere(['disactive' => 0])
            ->limit(10)
            ->all();
        $lost = Sale::find()
            // делаем выборку всех где время проверки меньше суток
            ->where(['<', 'date_of_check', time() - 24 * 60 * 60])
            ->andWhere(['in', 'id_sources', [1]])
            ->andWhere(['disactive' => 0])
            ->count();
        echo " lost=" . $lost;
        foreach ($sales as $sale) {
            sleep(1);
            $new_parsing_check = New Parsing();
            echo "<br>";
            echo " <a href=" . $sale->url . "> link </a>";
            $response = $new_parsing_check->check($sale);
            if ($response) {
                echo "Обявление еще активно";
                if ($response != $sale->price) {
                    echo $sale->price . " цена изменилась, стала " . $response;
                    $sale->price = $response;

                    echo " успешно изменили цену";

                }
                echo " цена не изменилась ";

                // обновляем время последней проверки объявления

                echo " date_of_check was updated";
                $sale->date_of_check = time();
                $sale->save();

            } else {
                echo " item was deleted";
                $sale->disactive = 1;
                $sale->save();
            } // если пришло что объявления нет

        }
        return $this->render('index');

    }


    public function actionCian()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию пяти часовой давности
        $ParsingConfiguration = ParsingConfiguration::find()
            ->where(['like', 'start_link', 'cian'])
            ->andwhere(['<', 'last_timestamp', time() - 5 * 60 * 60])
            ->orderBy('last_timestamp')
            ->one();
        if (!$ParsingConfiguration) {
            echo " пока нечего парсить";
            return $this->render('index');
        }
        //require_once('vendor/autoload.php');
// start Firefox with 5 second timeout
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);
// navigate to 'http://www.seleniumhq.org/'
        echo " обрабатываем link" . $ParsingConfiguration->start_link;
        $driver->get($ParsingConfiguration->start_link);
// adding cookie
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        $cookies = $driver->manage()->getCookies();


        $html = $driver->getPageSource();
        $pq = phpQuery::newDocument($html);
        $div_items = $pq->find('div.item');
        $total_count = $pq->find('div.totalOffers--yZcBn')->text();
        preg_match("/\d+/", $total_count, $output_array);
        $total_count = $output_array[0];
        $count_pages = floor($total_count / 25) + 1;
        // временно ставим на период тестов
        // $count_pages = 2;
        echo " всего вариантов" . $total_count . " и страниц" . $count_pages;;

        for ($i = 1; $i <= $count_pages; $i++) {

            $url = "https://novgorod.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&p=" . $i . "&region=4694&" . $ParsingConfiguration->suffix;
            $driver->get($url);
            // ждем пока загрузится контейнер с вариантыми
            $element = $driver->findElement(WebDriverBy::className('offer-container--38nzf'));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );

            $html = $driver->getPageSource();
            $pq = phpQuery::newDocument($html);
            // div всех предложений
            $div_items = $pq->find('div.offer-container--38nzf');

            foreach ($div_items as $div_item) {
                // заходим в div конкретного предложения
                $pq_div = pq($div_item);
                // берем title =
                $title = $pq_div->find('div.header--NemOm')->text();
                //  echo "<br> title=" .$title ;
                // берем div c ценой
                $div_prices = $pq_div->find('div.header--2lxlC')->text();

                // забираем цифры
                preg_match("/\d+,\d+|\d+/", $div_prices, $output_array);
                // echo " <br> пришло ".$output_array[0];
                $prices = floatval(str_replace(",", ".", $output_array[0]));
                //echo " <br> поменяли запятую на точку ".$prices;
                if (preg_match("/тыс/", $div_prices, $output_array1)) {
                    //  echo " <br> нашли слово тыс ";

                    $prices = $prices * 1000;
                }
                if (preg_match("/млн/", $div_prices, $output_array1)) {
                    //echo " <br> нашли слово млн ";
                    $prices = $prices * 1000000;
                }

                // echo "<br> price=" . $prices;
                $link = $pq_div->find('a.headerLink--1HdU8')->attr('href');
                // вытаскиваем ссылку
                //  echo "<br> link =" . $link;
                // вытаскиваем id
                $array_id = array_reverse(preg_split("/\//", $pq_div->find('a.headerLink--1HdU8')->attr('href')));
                $id = $array_id[1];
                //  echo "<br> id =" .$id;
                // вытаскиваем address_line и preg_split by "Великий Новгород";
                $address_line = $pq_div->find('div.address-path--12tl2')->text();
                $address_line = preg_split("/Великий Новгород,/", $address_line);
                $address = $address_line[1];
                // echo "<br> address =" . $address;
                // поиск что данный id уже есть в базе
                $active_item = Synchronization::find()->where(['id_in_source' => $id])->one();
                if ($active_item) {
                    // делаем update
                    if ($active_item->price != $prices) {
                        echo "<br> произошло изменение цены";
                        $active_item->price = $prices;
                        // значит произошел update цены
                        $active_item->is_active = 2;
                    }
                    if ($active_item->address != $address) {
                        echo "<br> произошло изменение адреса";
                        $active_item->address = $address;
                        // значит произошел update адреса
                        $active_item->is_active = 2;
                    }
                    if ($active_item->is_active == 2) $active_item->save();
                    else {
                        echo "<br> объект остался прежним";
                        // значит ничего не изменилось
                        $active_item->is_active = 3;
                        $active_item->save();

                    };

                } else {
                    // создаем новый объект
                    echo "<br> появился новый объект";
                    $active_item = New Synchronization();
                    $active_item->title = $title;
                    $active_item->address = $address;
                    $active_item->id_source = 5;
                    $active_item->id_in_source = $id;
                    $active_item->price = $prices;
                    $active_item->link = $link;
                    // значит new
                    $active_item->is_active = 1;
                    $active_item->save();
                }


                $n++;
                echo "<hr>";
            }


            sleep(2);

        }
        $current_time = time();
        echo " <h1> удалось сверить " . $n . " ссылок, за " . ($current_time - $time_start) . " секунд</h1>";
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);

        $ip = $pq->find('.ip')->find('big')->text();
        $ParsingConfiguration->last_timestamp = time();
        $ParsingConfiguration->last_ip = $ip;
        $ParsingConfiguration->save();

        // submit() does not work in Selenium 3 because of bug https://github.com/SeleniumHQ/selenium/issues/3398
// wait at most 10 seconds until at least one result is shown


// close the Firefox
        $driver->quit();
        //  return $this->render('index');
    }

    public function actionAvito()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию пяти часовой давности
        $ParsingConfiguration = ParsingConfiguration::find()
            ->where(['like', 'start_link', 'avito'])
            ->andwhere(['<', 'last_timestamp', time() - 5 * 60 * 60])
            ->orderBy('last_timestamp')
            ->one();

        if (!$ParsingConfiguration) {
            echo " пока нечего парсить";
            return $this->render('index');
        }
        if ($ParsingConfiguration->success_stop == 9999) $ParsingConfiguration->success_stop = 1;

        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);

        echo " обрабатываем link" . $ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix;
        $driver->get($ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix);
// addi
// adding cookie
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        $cookies = $driver->manage()->getCookies();

        $element = $driver->findElement(WebDriverBy::className('catalog-main'));
        $driver->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOf($element)
        );
        $html = $driver->getPageSource();
        $pq = phpQuery::newDocument($html);

        $total_count = $pq->find('span.breadcrumbs-link-count')->text();
        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / 50) + 1;
        echo "<br> pages=" . $pages;
        // $pages = 2;
        $max_count_pages = 5;
        $count_pages = 0;
        $exit = false;

        for ($i = ($ParsingConfiguration->success_stop + 1); $i <= $pages; $i++) {
            if ($i != 1) {
                $url = $ParsingConfiguration->start_link . "?p=" . $i . "&" . $ParsingConfiguration->suffix;
                echo "<br> парсим страницу" . $url;
                $driver->get($url);
            }


            // ждем пока загрузится кнопка пагинации
            $element = $driver->findElement(WebDriverBy::className('catalog-main'));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            $html = $driver->getPageSource();
            $pq = phpQuery::newDocument($html);
            $div_items = $pq->find('div.item');

            foreach ($div_items as $div_item) {
                $pq_div = pq($div_item);
                $title = $pq_div->find('a.item-description-title-link')->text();
                // echo "<br> title=" . $title ;
                $prices = $pq_div->find('div.popup-prices')->attr('data-prices');
                // вырезаем шаблон
                preg_match("/\"RUB\":\d+,\"USD/", $prices, $output_array);
                // забираем цифры
                preg_match("/\d+/", $output_array[0], $output_array);
                $prices = $output_array[0];
                // echo "<br> price=" . $prices;
                $link = "https://www.avito.ru" . $pq_div->find('a.item-description-title-link')->attr('href');
                // echo "<br> link =" .$link;

                $id = array_pop(preg_split("/_/", $pq_div->find('a.item-description-title-link')->attr('href')));;
                // echo "<br> id =" . $id;
                $address = $pq_div->find('p.address')->text();
                //echo "<br> address =" . $address;

                $n++;
                // на период теста выходим


                // поиск что данный id уже есть в базе
                $active_item = Synchronization::find()->where(['id_in_source' => $id])->one();
                if ($active_item) {
                    // делаем update
                    if ($active_item->price != $prices) {
                        echo "<br> произошло изменение цены";
                        $active_item->price = $prices;
                        // значит произошел update цены
                        $active_item->is_active = 2;
                    }
                    if ($active_item->address != $address) {
                        echo "<br> произошло изменение адреса";
                        $active_item->address = $address;
                        // значит произошел update адреса
                        $active_item->is_active = 2;
                    }
                    if ($active_item->is_active == 2) $active_item->save();
                    else {
                        echo "<br> объект остался прежним";
                        // значит ничего не изменилось
                        $active_item->is_active = 3;
                        $active_item->save();

                    };

                } else {
                    // создаем новый объект
                    echo "<br> появился новый объект";
                    $active_item = New Synchronization();
                    $active_item->title = $title;
                    $active_item->address = $address;
                    $active_item->id_source = 3;
                    $active_item->id_in_source = $id;
                    $active_item->price = $prices;
                    $active_item->link = $link;
                    // значит new
                    $active_item->is_active = 1;
                    $active_item->save();
                }


                echo "<hr>";
            }


            sleep(2);
            $count_pages++;
            $ParsingConfiguration->UpdateSuccessStop($i);
            if ($count_pages > $max_count_pages) {
                $exit = true;
                break;
            }

        }
        $current_time = time();
        echo " <h1> удалось сверить " . $n . " ссылок, за " . ($current_time - $time_start) . " секунд</h1>";

        if (!$exit) $ParsingConfiguration->FinalStop();
        $driver->quit();

    }

    public function actionIrr()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию пяти часовой давности
        $ParsingConfiguration = ParsingConfiguration::find()
            ->where(['like', 'start_link', 'irr'])
            ->andwhere(['<', 'last_timestamp', time() - 5 * 60 * 60])
            ->orderBy('last_timestamp')
            ->one();

        if (!$ParsingConfiguration) {
            echo " пока нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);

        echo " обрабатываем link" . $ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix;
        $driver->get($ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix);
// addi
// adding cookie
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        $cookies = $driver->manage()->getCookies();

        $element = $driver->findElement(WebDriverBy::className('js-productBlock'));
        $driver->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOf($element)
        );
        $html = $driver->getPageSource();
        $pq = phpQuery::newDocument($html);

        $total_count = $pq->find('div.listingStats')->text();
        preg_match("/из \d+ предложений/", $total_count, $output_array1);
        preg_match("/\d+/", $output_array1[0], $output_array);
        $total_count = $output_array[0];
        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / 30) + 1;
        echo "<br> pages=" . $pages;
        // $pages = 2;


        for ($i = 1; $i <= $pages; $i++) {
            if ($i == 1) continue;
            else $url = $ParsingConfiguration->start_link . "page" . $i . "/" . $ParsingConfiguration->suffix;
            echo "<br> парсим страницу" . $url;
            $driver->get($url);

            // ждем пока загрузится кнопка пагинации
            $element = $driver->findElement(WebDriverBy::className('js-productBlock'));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            $html = $driver->getPageSource();
            $pq = phpQuery::newDocument($html);
            $div_items = $pq->find('div.js-productBlock');

            foreach ($div_items as $div_item) {
                $pq_div = pq($div_item);
                $title = $pq_div->find('div.js-productListingProductName')->text();
                echo "<br> title=" . $title;
                $prices = preg_replace("/\D+/", "", $pq_div->find('div.listing__itemPrice')->text());
                echo "<br> price=" . $prices;
                $link = $pq_div->find('a')->attr('href');
                echo "<br> link =" . $link;
                preg_match("/advert\d+.html/", $link, $output_array1);
                preg_match("/\d+/", $output_array1[0], $output_array);
                $id = $output_array[0];
                echo "<br> id =" . $id;
                $address = $pq_div->find('div.listing__itemParameter_subTitleBold')->text();
                echo "<br> address =" . $address;

                $n++;
                // на период теста выходим


                // поиск что данный id уже есть в базе
                $active_item = Synchronization::find()->where(['id_in_source' => $id])->one();
                if ($active_item) {
                    // делаем update
                    if ($active_item->price != $prices) {
                        echo "<br> произошло изменение цены";
                        $active_item->price = $prices;
                        // значит произошел update цены
                        $active_item->is_active = 2;
                    }
                    if ($active_item->address != $address) {
                        echo "<br> произошло изменение адреса";
                        $active_item->address = $address;
                        // значит произошел update адреса
                        $active_item->is_active = 2;
                    }
                    if ($active_item->is_active == 2) $active_item->save();
                    else {
                        echo "<br> объект остался прежним";
                        // значит ничего не изменилось
                        $active_item->is_active = 3;
                        $active_item->save();

                    };

                } else {
                    // создаем новый объект
                    echo "<br> появился новый объект";
                    $active_item = New Synchronization();
                    $active_item->title = $title;
                    $active_item->address = $address;
                    $active_item->id_source = 1;
                    $active_item->id_in_source = $id;
                    $active_item->price = $prices;
                    $active_item->link = $link;
                    // значит new
                    $active_item->is_active = 1;
                    $active_item->save();
                }

                echo "<hr>";
            }


            sleep(2);


        }
        $current_time = time();
        echo " <h1> удалось сверить " . $n . " ссылок, за " . ($current_time - $time_start) . " секунд</h1>";
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);

        $ip = $pq->find('.ip')->find('big')->text();
        $ParsingConfiguration->last_timestamp = time();
        $ParsingConfiguration->last_ip = $ip;
        $ParsingConfiguration->save();

        $driver->quit();

    }

    public function actionYandex()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию пяти часовой давности
        $ParsingConfiguration = ParsingConfiguration::find()
            ->where(['like', 'start_link', 'yandex'])
            ->andwhere(['<', 'last_timestamp', time() - 5 * 60 * 60])
            ->orderBy('last_timestamp')
            ->one();

        if (!$ParsingConfiguration) {
            echo " пока нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);

        echo " обрабатываем link" . $ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix;
        $driver->get($ParsingConfiguration->start_link . "?" . $ParsingConfiguration->suffix);
// addi
// adding cookie
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        $cookies = $driver->manage()->getCookies();

        $element = $driver->findElement(WebDriverBy::className('search-results__serp_type_offers'));
        $driver->wait(10, 1000)->until(
            WebDriverExpectedCondition::visibilityOf($element)
        );
        $html = $driver->getPageSource();
        $pq = phpQuery::newDocument($html);

        $total_count = $pq->find('div.search-results-head__offers')->text();
        preg_match("/\d+/", $total_count, $output_array);
        $total_count = $output_array[0];
        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / 20) + 1;
        echo "<br> pages=" . $pages;
        //  $pages = 2;


        for ($i = 1; $i <= $pages; $i++) {
            if ($i != 1) {
                $url = $ParsingConfiguration->start_link . "?page=" . $i;
                echo "<br> парсим страницу" . $url;
                $driver->get($url);
            }

            // ждем пока загрузится кнопка пагинации
            $element = $driver->findElement(WebDriverBy::className('search-results__serp_type_offers'));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            $html = $driver->getPageSource();
            $pq = phpQuery::newDocument($html);
            $div_items = $pq->find('div.serp-item');

            foreach ($div_items as $div_item) {
                $pq_div = pq($div_item);
                $title = $pq_div->find('span.serp-item__head-link')->text();
                echo "<br> title=" . $title;
                $div_prices = $pq_div->find('div.offer-price')->text();
                // забираем цифры
                preg_match("/\d+,\d+|\d+/", $div_prices, $output_array);
                // echo " <br> пришло ".$output_array[0];
                $prices = floatval(str_replace(",", ".", $output_array[0]));
                //echo " <br> поменяли запятую на точку ".$prices;

                if (preg_match("/млн/", $div_prices, $output_array1)) {
                    //echo " <br> нашли слово млн ";
                    $prices = $prices * 1000000;
                }
                echo "<br> price=" . $prices;
                $link = "https://realty.yandex.ru" . $pq_div->find('a')->attr('href');
                echo "<br> link =" . $link;
                preg_match("/\d+/", $link, $output_array);
                $id = $output_array[0];
                echo "<br> id =" . $id;
                $address = $pq_div->find('h4.serp-item__address')->text();
                echo "<br> address =" . $address;

                $n++;
                // на период теста выходим


                // поиск что данный id уже есть в базе
                $active_item = Synchronization::find()->where(['id_in_source' => $id])->one();
                if ($active_item) {
                    // делаем update
                    if ($active_item->price != $prices) {
                        echo "<br> произошло изменение цены";
                        $active_item->price = $prices;
                        // значит произошел update цены
                        $active_item->is_active = 2;
                    }
                    if ($active_item->address != $address) {
                        echo "<br> произошло изменение адреса";
                        $active_item->address = $address;
                        // значит произошел update адреса
                        $active_item->is_active = 2;
                    }
                    if ($active_item->is_active == 2) $active_item->save();
                    else {
                        echo "<br> объект остался прежним";
                        // значит ничего не изменилось
                        $active_item->is_active = 3;
                        $active_item->save();

                    };

                } else {
                    // создаем новый объект
                    echo "<br> появился новый объект";
                    $active_item = New Synchronization();
                    $active_item->title = $title;
                    $active_item->address = $address;
                    $active_item->id_source = 2;
                    $active_item->id_in_source = $id;
                    $active_item->price = $prices;
                    $active_item->link = $link;
                    // значит new
                    $active_item->is_active = 1;
                    $active_item->save();
                }

                echo "<hr>";
            }


            sleep(2);


        }
        $current_time = time();
        echo " <h1> удалось сверить " . $n . " ссылок, за " . ($current_time - $time_start) . " секунд</h1>";
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);

        $ip = $pq->find('.ip')->find('big')->text();
        $ParsingConfiguration->last_timestamp = time();
        $ParsingConfiguration->last_ip = $ip;
        $ParsingConfiguration->save();

        $driver->quit();

    }

    public function actionMain()
    {
        \Yii::$app->cloud->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $config = ParsingConfiguration::GetLast();
        //$config = ParsingConfiguration::GetLastDev(1, 'avito');
        // my_var_dump($Config);
        if (!$config) {
            echo " пока нечего парсить";
            return $this->render('index');
            // отмечаем пропавшие ссылки и пропавшие больше чем на сутки ставим как удаленные
            Synchronization::CheckLostLinks();
            Synchronization::CheckDiedLinks();
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000, 50000);
        $setting = ParsingConfiguration::LoadSettings($config);
        echo "<br> start link = <a href='" . $config->start_link . "'> " . $config->start_link . " </a>";
        echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
        // открываем ссылку ( если нет открывается то выходим)
        $response = $driver->get($config->start_link);
        if ($response === false) {
            echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
            $driver->quit();
        }
        // распарсиваем ресурс стартовой страницы
        $pq = phpQuery::newDocument($response->getPageSource());
        $total_count = ParsingExtractionMethods::extractNumbersFromString($setting['total_count']['pattern'], $pq->find($setting['total_count']['div'])->text());
        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / $setting['items_per_page']) + 1;
        echo "<br> pages=" . $pages;
        // пока так !
        if (true) {


            echo "<br> start_page_number=" . $config->success_stop;
            // добавляем cookie
            $driver->manage()->deleteAllCookies();
            $cookie = new Cookie('cookie_name', 'cookie_value');
            $driver->manage()->addCookie($cookie);
            // $cookies = $driver->manage()->getCookies();

            // ждем пока загрузится элемент div с табличными данными
            $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            $CountCheckedPage = 0;
            $ItemsCounter = 0;

            // пробегаемся по страницам для сбора, короткой информации
            for ($i = $config->success_stop; $i <= $pages; $i++) {
                // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                if ($i != 1) {
                    // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                    $url = ParsingConfiguration::RuleOfUrl($config, $i);
                    echo "<br> парсим страницу" . $url;
                    $driver->get($url);
                    // ждем пока загрузится элемент div с табличными данными
                    $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
                    $driver->wait(10, 1000)->until(
                        WebDriverExpectedCondition::visibilityOf($element)
                    );
                    // распарсиваем полученный ресурс страницы
                    $pq = phpQuery::newDocument($driver->getPageSource());
                } else echo "<br> парсим страницу" . $config->start_link;

                // берем контейнер с вариантами
                $div_items = $pq->find($setting['container']);

                echo " <br> на странице " . count($div_items) . " вариантов";
                // прогоняем его
                foreach ($div_items as $div_item) {
                    $ItemsCounter++;
                    $pq_div = pq($div_item);
                    // распарсиваем mini_container
                    $response = ParsingExtractionMethods::ExtractTablesData($config, $pq_div);

                    // делаем синхронизацию полученного варианта ( new, update)
                    $SynchResponse = Synchronization::TODO($response);
                    echo "<hr>";
                }

                // ждем на странице какое-то время имитируя пользователя
                sleep(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD);
                // update счетчика обработанных страниц
                $CountCheckedPage++;
                // если мы обработали страниц больше лимита то выходим
                if ($CountCheckedPage >= ParsingConfiguration::PAGES_LIMIT) break;
                // делаем update текущей конфигурации исходя из страниц на которых мы остановились
                $config->UpdateAndSave($i, $pages);
            }
            $current_time = time();
            echo " <h3> удалось сверить " . $ItemsCounter . " объектов на " . $CountCheckedPage . " страницах, за " . ($current_time - $time_start) . " секунд</h3>";


        }
        $driver->quit();
        return $this->render('index');

    }

    public function actionMainNew()
    {
        \Yii::$app->cloud->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $configs = ParsingConfiguration::GetAllForNew();
        //$config = ParsingConfiguration::GetLastDev(1, 'avito');
        // my_var_dump($Config);

        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000, 50000);
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);

        foreach ($configs as $config) {
            $config = ParsingConfiguration::findOne($config->id);
            $setting = ParsingConfiguration::LoadSettings($config);
            $last_checked_ids = [];
            echo "<br> start link = <a href='" . $config->start_link . "'> " . $config->start_link . " </a>";
            echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
// открываем ссылку ( если нет открывается то выходим)
            $response = $driver->get($config->start_link);
            if ($response === false) {
                echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
                $driver->quit();
            }
            // распарсиваем ресурс стартовой страницы
            $pq = phpQuery::newDocument($response->getPageSource());
            $total_count = ParsingExtractionMethods::extractNumbersFromString($setting['total_count']['pattern'], $pq->find($setting['total_count']['div'])->text());
            echo "<br> total_count=" . $total_count;
            $pages = floor($total_count / $setting['items_per_page']) + 1;
            echo "<br> pages=" . $pages;


            echo "<br> start_page_number=" . $config->success_stop;
            // добавляем cookie
            // $cookies = $driver->manage()->getCookies();

            // ждем пока загрузится элемент div с табличными данными
            $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            $CountCheckedPage = 0;
            $ItemsCounter = 0;
            $new = 0;
            $current_last_checked_ids = unserialize($config->last_checked_ids);
            // my_var_dump($current_last_checked_ids);
            // пробегаемся по страницам для сбора, короткой информации
            for ($i = 1; $i <= $pages; $i++) {
                // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                if ($i != 1) {
                    // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                    $url = ParsingConfiguration::RuleOfUrl($config, $i);
                    echo "<br> парсим страницу" . $url;
                    $driver->get($url);
                    // ждем пока загрузится элемент div с табличными данными
                    $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
                    $driver->wait(10, 1000)->until(
                        WebDriverExpectedCondition::visibilityOf($element)
                    );
                    // распарсиваем полученный ресурс страницы
                    $pq = phpQuery::newDocument($driver->getPageSource());
                } else echo "<br> парсим страницу" . $config->start_link;

                // берем контейнер с вариантами
                $div_items = $pq->find($setting['container']);
                // прогоняем его
                $n = 10;
                $finish = false;
                foreach ($div_items as $div_item) {
                    $ItemsCounter++;
                    $pq_div = pq($div_item);
                    // распарсиваем mini_container
                    $response = ParsingExtractionMethods::ExtractTablesData($config, $pq_div);
                    // если объект не является выделенным, то анализираем его
                    if (!$response['starred']) {
                        //  echo " <h3>WAS</h3>" . implode(",", $last_checked_ids);
                        array_push($last_checked_ids, $response['id']);
                        //  echo " <h3>NOW</h3>" . implode(",", $last_checked_ids);
                        if ($config->last_checked_ids != '') {
                            if (in_array($response['id'], $current_last_checked_ids)) {

                                echo " <br> <h5>Достигли последнего проверенного варианта!</h5>" . $response['url'];
                                $finish = true;
                            } else {
                                if (Synchronization::TODO($response) == 'new') {
                                    $new++;
                                    // echo " <br> <h5>Появился новый вариант </h5>" . $response['url'];
                                };

                            }
                            // если сработало совпадение с поледними проверенными вариантыми то отсчитываем счетчик назад
                            if ($finish) $n--;

                        }
                    }
                    if ($n == 0) break;

                    //  echo "<hr>";
                }

                // ждем на странице какое-то время имитируя пользователя
                sleep(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD);
                // update счетчика обработанных страниц
                if ($finish) break;
                // если мы обработали страниц больше лимита то выходим
                if ($CountCheckedPage >= 2) break;
                $CountCheckedPage++;

            }
            echo " <h3>Новых вариантов</h3>" . $new;
            echo " <h3>WAS 1</h3>" . implode(",", unserialize($config->last_checked_ids));
            $config->last_checked_ids = serialize(array_slice($last_checked_ids, 0, 10));
            echo " <h3>NOW 1</h3>" . implode(",", unserialize($config->last_checked_ids));
            $current_time = time();
            echo " <h3> удалось сверить " . $ItemsCounter . " объектов на " . $CountCheckedPage . " страницах, за " . ($current_time - $time_start) . " секунд</h3>";
            // делаем update текущей конфигурации исходя из страниц на которых мы остановились
            $config->save();


        }
        $driver->quit();
        return $this->render('index');

    }


    public function actionMainTest()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $url = 'http://velnovgorod.irr.ru/real-estate/apartments-sale/one-rooms/page36/';
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);

        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы
        $driver->get($url);

        // echo $driver->getPageSource();
        $pq = phpQuery::newDocument($driver->getPageSource());


        $pq_div = $pq->find('div.js-productGrid')->eq(0);
        $pq_div = phpQuery::newDocument($pq_div);
        $pq_divs = $pq_div->find('.js-productBlock');
        foreach ($pq_divs as $pq_div) {
            $title = $pq_div->find('div.js-productListingProductName')->text();
            $price = preg_replace("/\D+/", "", $pq_div->find('div.listing__itemPrice')->text());
            $url = $pq_div->find('a')->attr('href');
            preg_match("/advert\d+.html/", $url, $output_array1);
            preg_match("/\d+/", $output_array1[0], $output_array);
            $id = $output_array[0];
            $starred = $pq_div->find('div.js-servicesIcons')->html();

            if (!empty($starred)) $starred = true; else $starred = false;
            $address = $pq_div->find('div.listing__itemParameter_subTitleBold')->text();

            $date = $pq_div->find('div.updateProduct')->html();

            $date = preg_grep("/$/", explode("\n", $date));
            //    my_var_dump($date);
            echo "<br> date as String" . $date[7];
            $date_start = ParsingExtractionMethods::Date_to_unix($date[7]);
            echo "<br> date_start" . $date_start;
        }


        //  $driver->quit();
        return $this->render('index');

    }

    public function actionDetailedSync()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $sales = Synchronization::find()
            ->where(['in', 'disactive', [3]])
            ->andWhere(['in', 'id_sources', [1, 2, 3, 5]])
            ->orderBy('id_sources, date_of_check')
            ->limit(10)->all();
        $count = Synchronization::find()
                ->where(['in', 'disactive', [3]])
                ->andWhere(['in', 'id_sources', [1, 2, 3, 5]])
                ->count() - count($sales);
        echo " <h3>" . $count . "</h3>";
        // my_var_dump($Config);
        if (!$sales) {
            echo " нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);
        //  $driver->get('https://irr.ru/');
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы
        foreach ($sales as $sale) {
            // открываем ссылку ( если нет открывается то выходим)
            $response = $driver->get($sale->url);
            if ($response === false) {
                echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
                $driver->quit();
                die;
            }
            // распарсиваем ресурс  страницы
            $pq = phpQuery::newDocument($response->getPageSource());

            $parsing = ParsingExtractionMethods::ExtractPageData($pq, $driver->getCurrentURL(), $sale->id_sources);
            if ($parsing) $parsing->UpdateSale($sale);
            $sale->date_of_check = time();
            // синхронизация с sale history
            //  $sale->SaleHistorySynchronization();
            $sale->save();
//            echo "<br>".$parsing->address;
            //  sleep(rand(1,1.5));

        }


        $driver->quit();
        return $this->render('index');

    }

    public function actionDetailedSyncUrl()
    {
        $url = "https://realty.yandex.ru/offer/229104191815263489/";
        $id_sources = ParsingExtractionMethods::getSourceIdFromUrl($url);
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);
        //  $driver->get('https://irr.ru/');
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы
        // открываем ссылку ( если нет открывается то выходим)
        $response = $driver->get($url);
        if ($response === false) {
            echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
            $driver->quit();
            die;
        }
        if ($id_sources == 1) {
            if (!preg_match("/.+?from=.\d+/", $response->getCurrentURL(), $output_array)) {
                if (!preg_match("/Объявление.+снято.+с.+публикации/", $driver->findElement(WebDriverBy::className('siteBody'))->getText(), $output_array)) {
                    if (preg_match("/…Читать дальше\z/", $driver->findElement(WebDriverBy::className('productPage__descriptionText'))->getText(), $output_array))
                        $driver->findElement(WebDriverBy::className('productPage__readMore'))->click();;
                    // $element = $driver->findElement(WebDriverBy::className('productPage__readMore'))->isDisplayed();
                    $driver->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['irr']))->click();
                    // ждем пока загрузится элемент div с табличными данными
                    $element = $driver->findElement(WebDriverBy::className('productPage__feedbackColumn'));
                    $driver->wait(10, 1000)->until(
                        WebDriverExpectedCondition::visibilityOf($element)
                    );


                } else {
                    $break = true;
                    echo "<br>" . $driver->findElement(WebDriverBy::className('productPage__unactiveBlock'))->getText();
                }
            } else {
                $break = true;
                echo "<br> Объявление удалено вообще";


            }

        }
        if ($id_sources == 2) {
            // ждем пока загрузится элемент div с табличными данными
            $element = $driver->findElement(WebDriverBy::className('content'));
            $driver->wait(10, 1000)->until(
                WebDriverExpectedCondition::visibilityOf($element)
            );
            if (!preg_match("/Объявление.{1,3}устарело/", $driver->findElement(WebDriverBy::className('content'))->getText(), $output_array)) {
                $driver->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['yandex']))->click();

            } else {
                $break = true;
                echo "<br>" . $driver->findElement(WebDriverBy::className('offer-card__inactive'))->getText();
            }


        }
        if ($id_sources == 3) {
            $pq = phpQuery::newDocument($driver->getPageSource());
            if (!empty($pq->find('div.item-view-content')->text())) {
                // ждем пока загрузится элемент div с табличными данными
                $element = $driver->findElement(WebDriverBy::className('item-view-content'));
                $driver->wait(10, 1000)->until(
                    WebDriverExpectedCondition::visibilityOf($element)
                );
            } else {
                $sale->disactive = 1;
                $break = true;
            }


        }
        if ($id_sources == 5) {
            if (!preg_match("/Эта.+страница.+не.+находится.+на.+ЦИАН/", $driver->getPageSource())) {

                // ждем пока загрузится элемент div с табличными данными
                $element = $driver->findElement(WebDriverBy::className('offer_container'));
                $driver->wait(10, 1000)->until(
                    WebDriverExpectedCondition::visibilityOf($element)
                );

            } else {
                $break = true;
                echo "<br> Объявление удалено вообще";

            }


        }

        // распарсиваем ресурс  страницы
        $pq = phpQuery::newDocument($response->getPageSource());

        $parsing = ParsingExtractionMethods::ExtractPageData($pq, $driver->getCurrentURL(), $id_sources);
        // if ($parsing) $parsing->UpdateSale($sale);
        echo "<br>" . $parsing->address;
        //  sleep(rand(1,1.5));


        $driver->quit();
        return $this->render('index');

    }

    public function actionParsingNew()
    {
        \Yii::$app->cloud->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        $time_start = time();
        // Parsing::setTablePrefix('Velikiy_novgorod');
        Synchronization::setTablePrefix('Velikiy_novgorod');

        // берем новые объекты
        $New_Objects = Synchronization::find()
            ->where(['in', 'disactive', [3, 4]])
            ->andWhere(['in', 'id_sources', [1, 2, 3, 5]])
            ->orderBy(['id_sources' => SORT_ASC, 'date_of_check' => SORT_DESC])
            ->limit(20)->all();
        Synchronization::Counts([1, 2, 3, 4, 5], 3);
        // my_var_dump($Config);
        if (!$New_Objects) {
            echo " нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000, ParsingConfiguration::MAX_WAITING_DRIVER_TIMEOUT);
        //  $driver->get('https://irr.ru/');
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы

        foreach ($New_Objects as $sale) {
            $break = false;
            // открываем ссылку ( если нет открывается то выходим)
            $response = $driver->get($sale->url);
            if ($response === false) {
                echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
                $driver->quit();
                die;
            }
            if ($sale->id_sources == 1) {
                if (!preg_match("/.+?from=.\d+/", $response->getCurrentURL(), $output_array)) {
                    if (!preg_match("/Объявление.+снято.+с.+публикации/", $driver->findElement(WebDriverBy::className('siteBody'))->getText(), $output_array)) {
                        if (preg_match("/…Читать дальше\z/", $driver->findElement(WebDriverBy::className('productPage__descriptionText'))->getText(), $output_array))
                            $driver->findElement(WebDriverBy::className('productPage__readMore'))->click();;
                        // $element = $driver->findElement(WebDriverBy::className('productPage__readMore'))->isDisplayed();
                        $driver->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['irr']))->click();
                        // ждем пока загрузится элемент div с табличными данными
                        $element = $driver->findElement(WebDriverBy::className('productPage__feedbackColumn'));
                        $driver->wait(10, 1000)->until(
                            WebDriverExpectedCondition::visibilityOf($element)
                        );


                    } else {
                        $break = true;
                        echo "<br>" . $driver->findElement(WebDriverBy::className('productPage__unactiveBlock'))->getText();
                    }
                } else {
                    $break = true;
                    echo "<br> Объявление удалено вообще";


                }

            }
            if ($sale->id_sources == 2) {
                // ждем пока загрузится элемент div с табличными данными
                $element = $driver->findElement(WebDriverBy::className('content'));
                $driver->wait(10, 1000)->until(
                    WebDriverExpectedCondition::visibilityOf($element)
                );
                if (!preg_match("/Объявление.{1,3}устарело/", $driver->findElement(WebDriverBy::className('content'))->getText(), $output_array)) {
                    $driver->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['yandex']))->click();

                } else {
                    $break = true;
                    echo "<br>" . $driver->findElement(WebDriverBy::className('offer-card__inactive'))->getText();
                }


            }
            if ($sale->id_sources == 3) {
                $pq = phpQuery::newDocument($driver->getPageSource());
                if (!empty($pq->find('div.item-view-content')->text())) {
                    // ждем пока загрузится элемент div с табличными данными
                    $element = $driver->findElement(WebDriverBy::className('item-view-content'));
                    $driver->wait(10, 1000)->until(
                        WebDriverExpectedCondition::visibilityOf($element)
                    );
                } else {
                    $sale->disactive = 1;
                    $break = true;
                }


            }
            if ($sale->id_sources == 5) {
                if (!preg_match("/Эта.+страница.+не.+находится.+на.+ЦИАН/", $driver->getPageSource())) {

                    // ждем пока загрузится элемент div с табличными данными
                    $element = $driver->findElement(WebDriverBy::className('offer_container'));
                    $driver->wait(10, 1000)->until(
                        WebDriverExpectedCondition::visibilityOf($element)
                    );

                } else {
                    $break = true;
                    echo "<br> Объявление удалено вообще";

                }


            }

            //  $result = $driver->findElement(WebDriverBy::className('productPage__descriptionText'))->getText();
            if (!$break) {
                $pq = phpQuery::newDocument($driver->getPageSource());

                sleep(1);
                $parsing = ParsingExtractionMethods::ExtractPageData($pq, $driver->getCurrentURL(), $sale->id_sources);
                if ($parsing) $parsing->UpdateSale($sale);


            } else {
                if ($sale->disactive == 3) {
                    // если оно только что пригло и уже устарело то удалаем его т.к. всеравно нет телефона и цены

                    if (!Synchronization::findOne($sale->id)->delete()) echo "<br> не удалось удалить";
                    else  echo "<br> удалили <a href='" . $sale->url . "' > " . $sale->id_in_source . "</a>";

                } else {
                    $sale->disactive = 1;

                }
            }

            //  if ($parsing) $parsing->UpdateSale($sale);
            // $sale->date_of_check = time();
            // синхронизация с sale history
            // $sale->SaleHistorySynchronization();
            // $sale->save();
//            echo "<br>".$parsing->address;
            //  sleep(rand(1,1.5));
            $sale->date_of_check = time();
            $sale->save();
        }


        $driver->quit();
        return $this->render('index');

    }

    public function actionParsingAvitoPhones()
    {
        $time_start = time();
        //  Parsing::setTablePrefix('Velikiy_novgorod');
        Synchronization::setTablePrefix('Velikiy_novgorod');

        // берем последнюю необработанную конфигурацию
        $New_Objects = Synchronization::find()
            ->where(['phone1' => null])
            ->andwhere(['id_sources' => 3])
            ->limit(20)->all();
        $count = Synchronization::find()
            ->where(['phone1' => null])
            ->andwhere(['id_sources' => 3])
            ->count();
        echo " <h3>" . $count . "</h3>";
        // my_var_dump($Config);
        if (!$New_Objects) {
            echo " нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000, 50000);
        //  $driver->get('https://irr.ru/');
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы
        foreach ($New_Objects as $sale) {
// открываем ссылку ( если нет открывается то выходим)

            $url = $sale->url;
            $url = preg_replace("/www.avito/", "m.avito", $url);
            echo "<br>" . $url;
            $response = $driver->get($url);
            if ($response === false) {
                echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
                $driver->quit();
                die;
            }
            if (!preg_match("/Сохранить.+поиск/", $driver->getPageSource())) {

                $element = $driver->findElement(WebDriverBy::className('js-action-show-number'));
                $driver->wait(10, 1000)->until(
                    WebDriverExpectedCondition::visibilityOf($element)
                );
                $driver->findElement(WebDriverBy::className('js-action-show-number'))->click();

            } else {
                $break = true;
                echo "<br> Объявление удалено вообще";

            }
            if (!$break) {

                sleep(1);
                $sale->phone1 = ParsingExtractionMethods::ExtractPhoneFromMAvito($driver->getPageSource());

            } else {
                if (!$sale->phone1) {
                    // если оно только что пригло и уже устарело то удалаем его т.к. всеравно нет телефона и цены

                    if (!Synchronization::findOne($sale->id)->delete()) echo "<br> не удалось удалить";
                    else  echo "<br> удалили <a href='" . $sale->url . "' > " . $sale->id_in_source . "</a>";

                } else {
                    $sale->disactive = 1;

                }
            }

            sleep(2);
            if (!$sale->save()) my_var_dump($sale->getErrors());


        }


        $driver->quit();
        return $this->render('index');

    }

    public function actionDetailedSync2()
    {
        $time_start = time();
        // берем последнюю необработанную конфигурацию
        $sales = Sale::find()
            ->where(['in', 'disactive', [3]])
            ->andWhere(['in', 'id_sources', [1, 2, 5]])
            ->orderBy('id_sources')
            ->limit(50)->all();
        // my_var_dump($Config);
        if (!$sales) {
            echo " нечего парсить";
            return $this->render('index');
        }
        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);
        $driver->get('https://www.kolmovo.ru/');
        $driver->manage()->deleteAllCookies();
        $cookie = new Cookie('cookie_name', 'cookie_value');
        $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы
        foreach ($sales as $sale) {
            $pq = phpQuery::newDocument($driver->get($sale->url)->getPageSource());
            $driver->findElement(WebDriverBy::className(Parsing::ARRAY_OF_PHONE_CLASS_BUTTON['irr']))->click();
            sleep(2);
            $parsing = ParsingExtractionMethods::ExtractPageData($pq, $driver->getCurrentURL(), $sale->id_sources);
            // if ($parsing) $parsing->UpdateSale($sale);
            $sale->date_of_check = time();
            // синхронизация с sale history
            // $sale->SaleHistorySynchronization();
            // if (!$sale->save()) my_var_dump($sale->getErrors());
//            echo "<br>".$parsing->address;
            //  sleep(rand(1,1.5));

        }


        $driver->quit();
        return $this->render('index');

    }

    public
    function actionBet()
    {

        $host = 'http://localhost:9515'; // this is the default
        $capabilities = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create($host, $capabilities, 10000);

        $driver->get('http://www.scoreboard.com/ru/match/ghana-congo-2018/dnf83s8i/');
        $PageSource = $driver->getPageSource();
        $pq = phpQuery::newDocument($PageSource);
        $table = $pq->find('#summary-content')->find('tr.odd')->html();
        my_var_dump($table);
//       foreach ($table as $item) {
//           echo "<br>".$item;
//       }
        //echo $PageSource;
        // $driver->quit();
    }

    public function actionSetUpdated()
    {
        foreach (Synchronization::find()->where(['is_active' => 2])->andwhere(['id_source' => 5])->limit(6000)->all() as $item) {
            $sale = Sale::find()->where(['id_in_source' => $item->id_in_source])->one();
            if ($sale) {
                echo "<br>" . $sale->id;
                $sale->disactive = 3;
                $sale->save();
                $count++;
            } else {
                echo "<br> this id " . $item->id_in_source . " not found";
                echo "<br> this id " . $item->link . " not found";
            }
        }
        echo "<hr> сделали " . $count . " обновлений";
    }

    public function actionDetailedCian()
    {
        $module = Control::findOne(1);

        info("Детальный парсинг новой страницы циан");
        $url = 'https://novgorod.cian.ru/sale/flat/161698913/';
        // $url = 'https://spb.cian.ru/sale/flat/163194152/';
        //  $url = 'https://spb.cian.ru/sale/flat/144447030/';
        $source_id = ParsingExtractionMethods::getSourceIdFromUrl($url);
        info("id_sources =" . $source_id);
        $driver = MyChromeDriver::Open();
        $driver->get($url);
        // делаем последовательность действий чтобы выудить информацию о странице (нажать,подождать и прочее );
        $driver->SequentialProcessing(Parsing::sourceSequential()[$source_id]);
        // echo $driver->getPageSource();

        $parsing = ParsingExtractionMethods::ExtractPageData(phpQuery::newDocument($driver->getPageSource()), $driver->getCurrentURL(), $source_id);
        // my_var_dump($parsing);
        $sale = new Sale();
        if ($parsing) $parsing->UpdateSale($sale);
        echo "<br>" . $sale->renderLong_title() . " " . $sale->phone1;
        $driver->quit();
        return $this->render('@app/views/console/processing');
    }

    public function actionParsingHouses()
    {

        $module = Control::findOne(2);
        echo $prefix = $module->region;
        Addresses::setTablePrefix($prefix);
        info("Парсинг домов и улиц");
        $url = 'http://dom.mingkh.ru/pskovskaya-oblast/pskov/';
        // $url = 'https://spb.cian.ru/sale/flat/163194152/';
        //  $url = 'https://spb.cian.ru/sale/flat/144447030/';
        $driver = MyChromeDriver::Open();
        $driver->get($url);
        sleep(2);
        $driver->findElement(WebDriverBy::xpath('//*[@id="grid-data-header"]/div/div/div[2]/div[1]/button'))->click();
        sleep(2);
        $driver->findElement(WebDriverBy::xpath('//*[@id="grid-data-header"]/div/div/div[2]/div[1]/ul/li[4]/a'))->click();
        sleep(2);
        $pq = phpQuery::newDocument($driver->getPageSource());
        $pq2 = phpQuery::pq($pq->find("table:eq(1)"));
        foreach ($pq2->find('tr') as $item) {
            $new_pq = phpQuery::pq($item);
           if ($new_pq->find("td:eq(1)")->text()) {
               $address = new Addresses();
               $address->address = $new_pq->find("td:eq(1)")->text();
               $address->url = "http://dom.mingkh.ru".$new_pq->find("a")->attr('href');
               if (!preg_match('/Не заполнено/',$new_pq->find("td:eq(3)")->text())) $address->year = $new_pq->find("td:eq(3)")->text();
               if (!preg_match('/Не заполнено/',$new_pq->find("td:eq(4)")->text())) $address->floorcount = $new_pq->find("td:eq(4)")->text();
               echo "<br> Адрес: ".$new_pq->find("td:eq(1)")->text();
               echo "<br> год: ".$new_pq->find("td:eq(3)")->text();
               echo "<br> этажность: ".$new_pq->find("td:eq(4)")->text();
               echo "<br> ссылка: ".$new_pq->find("a")->attr('href');
               if (!$address->save()) my_var_dump($address->getErrors());
           }

        }
        $driver->quit();

        return $this->render('@app/views/console/processing');
    }


}