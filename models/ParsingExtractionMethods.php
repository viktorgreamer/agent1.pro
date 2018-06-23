<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 31.08.2017
 * Time: 11:54
 */

namespace app\models;


use app\utils\P;
use yii\base\Model;
use app\models\ParsingModels\Parsing;

class ParsingExtractionMethods extends Model
{
    public static function getOriginalIdFromUrl($url)
    {
        if (preg_match('/irr/', $url, $output_array)) {
            preg_match("/advert\d+.html/", $url, $output_array1);
            preg_match("/\d+/", $output_array1[0], $output_array);
            return $output_array[0];
        } elseif (preg_match('/yandex/', $url, $output_array)) {

            preg_match("/offer\/\d+/", $url, $output_array);
            preg_match("/\d+/", $output_array[0], $output_array1);

            return $output_array1[0];
        } elseif (preg_match('/cian/', $url, $output_array)) {

            preg_match("/\d+/", $url, $output_array1);

            return $output_array1[0];
        } elseif (preg_match('/avito/', $url, $output_array)) {

            return array_pop(preg_split("/_/", $url));


        } elseif (preg_match('/youla/', $url, $output_array)) {

            preg_match("/-[a-z0-9]{24}/", $url, $output_array);
            return preg_replace("/-/", "", $output_array[0]);


        } else return false;
    }

    public static function getSourceIdFromUrl($url)
    {
        if (preg_match('/irr/', $url, $output_array)) {

            return 1;
        } elseif (preg_match('/yandex/', $url, $output_array)) {


            return 2;
        } elseif (preg_match('/cian/', $url, $output_array)) {


            return 5;
        } elseif (preg_match('/avito/', $url, $output_array)) {

            return 3;


        } elseif (preg_match('/youla/', $url, $output_array)) {


            return 4;


        } else return 0;
    }

    public static function extractNumbersFromString($pattern, $string)
    {
        if ($pattern == '') {
            $totalCount = preg_replace("/\D+/", "", $string);
            if (is_numeric($totalCount)) return $totalCount;
            else return false;
        } else {
            echo "<br> \$pattern" . $pattern;
            echo "<br> \$string" . $string;
            preg_match($pattern, $string, $output_array1);
            preg_match("/\d+/", $output_array1[0], $output_array);
            if (is_numeric($output_array[0])) return preg_replace("/\D+/", "", $output_array[0]);
            else return false;
        }


    }


    public static function ExtractTablesData($config, $pq_div)
    {

        switch ($config->id_sources) {
            case 1:
                {
                    $id_sources = 1;

                    $title = $pq_div->find('div.js-productListingProductName')->text();
                    $price = preg_replace("/\D+/", "", $pq_div->find('div.listing__itemPrice')->text());
                    $url = $pq_div->find('a')->attr('href');
                    preg_match("/advert\d+.html/", $url, $output_array1);
                    preg_match("/\d+/", $output_array1[0], $output_array);
                    $id = $output_array[0];
                    $starred = $pq_div->find('div.js-servicesIcons')->html();

                    if (!empty($starred)) $starred = true; else $starred = false;
                    $address = trim($pq_div->find('div.listing__itemParameter_subTitleBold')->text());

                    $date = $pq_div->find('div.updateProduct')->html();

                    $date = preg_grep("/$/", explode("\n", $date));
                    //    my_var_dump($date);
                    //  echo "<br> date as String".$date[7];
                    $date_start = ParsingExtractionMethods::Date_to_unix($date[7]);
                    //  echo "<br> date_start".$date_start;

                    break;
                }
            case 2:
                {
                    $id_sources = 2;
                    $title = $pq_div->find('span.serp-item__head-link')->text();
                    $div_prices = $pq_div->find('div.offer-price')->text();
                    // echo "<br>".$div_prices;
                    // забираем цифры
                    preg_match("/\d+,\d+|\d+/", $div_prices, $output_array);
                    // my_var_dump($output_array);
                    $price = floatval(str_replace(",", ".", $output_array[0]));
                    //  echo " <br> поменяли запятую на точку ".$price;

                    if (preg_match("/млн/", $div_prices, $output_array1)) {
                        //echo " <br> нашли слово млн ";
                        $price = $price * 1000000;
                    } else {
                        $price = $price * 1000;
                    }
                    //  echo "<br>".$price;
                    $url = "https://realty.yandex.ru" . $pq_div->find('a')->attr('href');
                    preg_match("/\d+/", $url, $output_array);
                    $id = $output_array[0];
                    $starred = $pq_div->find('div.offer-label')->html();

                    if (!empty($starred)) $starred = true; else $starred = false;
                    $date = $pq_div->find('div.serp-item__publish-date')->text();
                    //  echo "<br> date as String".$date;
                    $date_start = ParsingExtractionMethods::Date_to_unix($date);
                    //  echo "<br> date_start".$date_start;

                    $address = trim($pq_div->find('h4.serp-item__address')->text());
                    break;
                }
            case 3:
                {
                    $id_sources = 3;
                    $title = $pq_div->find('a.item-description-title-link')->text();
                    $prices = $pq_div->find('div.popup-prices')->attr('data-prices');
                    // вырезаем шаблон
                    preg_match("/\"RUB\":\d+,\"USD/", $prices, $output_array);
                    // забираем цифры
                    preg_match("/\d+/", $output_array[0], $output_array);
                    $url = "https://www.avito.ru" . $pq_div->find('a.item-description-title-link')->attr('href');
                    $price = $output_array[0];
                    $address = trim(preg_replace("/📢📲/u", "", trim($pq_div->find('p.address')->text())));
                    $date = $pq_div->find('div.c-2')->text();
                    echo "<br> date as String" . $date;
                    $date_start = ParsingExtractionMethods::Date_to_unix($date);
                    //  echo "<br> date_start".$date_start;

                    $id = array_pop(preg_split("/_/", $pq_div->find('a.item-description-title-link')->attr('href')));;


                    break;
                }
            case 5:
                {
                    $title_div_class = Cian::tableTitle_div_class($pq_div->html());
                    $id_sources = 5;
                    $title = $pq_div->find("div." . $title_div_class)->text();
                    // берем div c ценой

                    $price_div_class = Cian::tablePrice_div_class($pq_div->html());

                    $price = P::ExtractNumders($pq_div->find("div." . $price_div_class)->text());

                    $url = $pq_div->find("div." . $title_div_class)->attr('href');
                    // вытаскиваем ссылку
                    // вытаскиваем id
                    $array_id = array_reverse(preg_split("/\//", $url));
                    $id = $array_id[1];

                    $starred_div_class = Cian::tableStarred_div_class($pq_div->html());
                    if ($starred_div_class) {
                        $starred = $pq_div->find("button.".$starred_div_class)->html();

                        if (!empty($starred)) $starred = true; else $starred = false;

                    }
                    // вытаскиваем address_line и preg_split by "Великий Новгород";

                    $address_div = Cian::tableAddress_div_class($pq_div->html());

                    $address_line = $pq_div->find("div." . $address_div)->text();
                    echo "<br> \$address_line " . $address_line;
                    $address_line = preg_split("/" . \Yii::$app->params['module']->region_rus . ",/", $address_line);
                    $address = trim($address_line[1]);

                    $time_div_class = Cian::tableTime_div_class($pq_div->html());
                    $date = $pq_div->find("div." . $time_div_class)->text();
                    //  echo "<br> date as String".$date;
                    $date_start = ParsingExtractionMethods::Date_to_unix($date, 'cian');
                    //  echo "<br> date_start".$date_start;


                    break;
                }


        }
//        echo "<br> id =" . $id;
//        echo "<br> url =" . $url;
//        echo "<br> price=" . $price;
//        echo "<br> title=" . $title;
//        echo "<br> address =" . $address;
//        echo "<br> date as String".$date;
//        echo "<br> date_start".$date_start;


        return [
            'id' => $id,
            'url' => $url,
            'price' => round($price),
            'title' => $title,
            'address_line' => $address,
            'starred' => $starred,
            'id_sources' => $id_sources,
            'date_start' => $date_start

        ];
    }

    public static function ExtractPageData($pq, $url, $id_sources)
    {
        switch ($id_sources) {
            case 1:
                {


                    $parsing = New Parsing();
                    $parsing->id_sources = 1;
                    $parsing->IrrPage($pq, $url);;


                    break;
                }
            case 2:
                {

                    $parsing = New Parsing();
                    $parsing->id_sources = 2;

                    $parsing->YandexPage($pq, $url);


                    break;
                }
            case 3:
                {

                    $parsing = New Parsing();
                    $parsing->id_sources = 3;
                    $parsing->AvitoPage($pq, $url);


                    break;
                }
            case 5:
                {

                    $parsing = New Parsing();
                    $parsing->id_sources = 5;

                    $parsing->CianPage($pq, $url);


                    break;
                }


        }
        //   echo "<br> phone1 = ".$parsing->phone1;
        //  echo "<br> id_in_source = ".$parsing->id_in_source;
        //  echo "<br> price = ".$parsing->price;
        return $parsing;

    }

    public static function ExtractPhoneFromMAvito($sourse)
    {
        $pq = \phpQuery::newDocument($sourse);

        $phone_span = $pq->find('span.button-text')->text();
       // info($phone_span);

        $output_array =  preg_replace("/\D+/", "", $phone_span);
        my_var_dump($output_array);

        $phone = preg_replace("/\D+/", "", $output_array);
        $phone = preg_replace("/\A7/", "8", $phone);
        info("I have got the Phone from avito =" . $phone ." BY CLICKING METHODS",SUCCESS);
        return $phone;
    }

    public static function TimeStampFromListView($id_sourses, $string)
    {
    }

    public static function Date_to_unix($string_date, $source = '')
    {
        $array_of_month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        // TODO вопрос по поводу ноября!!!!
        if ($source == 'cian') $array_of_month_rus = ['янв', 'фев', 'мар', 'апр', 'мая', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];
        //  my_var_dump($array_of_month_rus);
        $array_of_month_eng = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $string_date = mb_strtolower($string_date);
        $year = date("Y");
        //  echo " <br>" . $string_date;
        date_default_timezone_set('Europe/Moscow');
        if (preg_match("/сегодня/", $string_date, $output_array)) {
            $month = date("m");
            $day = date("d");
            $year = date("Y");
        } elseif (preg_match("/вчера/", $string_date, $output_array)) {

            $month = date("m", time() - 86400);
            $day = date("d", time() - 86400);
            $year = date("Y", time() - 86400);
        } else {
            foreach ($array_of_month_rus as $key => $month_rus) {
                $pattern = $month_rus;
                // echo "<br> ищу ".$pattern." в ".$string_date;
                // echo "   ".$pattern;
                if (mb_stripos($string_date, trim($pattern), 0, 'UTF-8') != false) {
                    $month = $key + 1;
                    break;
                };
            }
            $pattern = "/(\d\d|\d).+" . $pattern . "/";
            //  echo "<br>".$pattern;
            preg_match($pattern, $string_date, $output_array);
            preg_match("/\d\d|\d/", $output_array[0], $output_array);
            $day = $output_array[0];
            // вдруг пришел год то в диапазоне от 2010 до 2019,  то пишем его
            if (preg_match("/201\d/", $string_date, $output_array)) $year = $output_array[0];

        }


        // распарсиваем час и минуты
        if (preg_match("/\d\d:\d\d/", $string_date, $output_array)) {
            $time = preg_split("/:/", $output_array[0]);
            $hours = $time[0];
            $minute = $time[1];
        } else {
            $hours = 12;
            $minute = 00;
        }


        //  echo " точная дата подачи " . $day . "." . $month . "." . $year . " в " . $hours . " часов " . $minute . " минут";
        $unix_date = mktime($hours, $minute, 0, $month, $day, $year);
        //  echo "<br> и того время в  unix = " . $unix_date;
        //   echo "<br> и того время в  date =  " . date("d.m.y H:i:s", $unix_date);
        // данная строка на случай если объявление подано 30декабря а сегодня 15 января следуюущего года, а написано было бы что объялвние было поданобы в будущем что не правильно уменьшаем на один год
        if ($unix_date > time()) $unix_date = mktime($hours, $minute, 0, $month, $day, $year - 1);
        return $unix_date;


    }

    public static function findPhone($text = '')
    {
        if (preg_match("/(\d{6,10}|\+7\d{6,10})/", $text, $output_array)) {
            return $output_array[1];
        };


    }
}