<?php

namespace app\models\ParsingModels;


use app\models\Geocodetion;
use Yii;
use phpQuery;
use app\models\Selectors;
use app\models\ErrorsLog;
use app\utils\P;
use app\models\ParsingExtractionMethods;

/*
* @property integer $id
* @property integer $original_date
* @property integer $count_of_views
* @property integer $date_start
* @property integer $rooms_count
* @property string $title
* @property integer $price
* @property string $phone1
* @property string $city
* @property string $address
* @property integer $house_type
* @property double $coords_x
* @property double $coords_y
* @property integer $id_address
* @property integer $year
* @property string $locality
* @property string $description
* @property integer $floor
* @property integer $floorcount
* @property integer $id_sources
* @property integer $grossarea
* @property integer $kitchen_area
* @property integer $living_area
* @property string $images
* @property string $url
* @property integer $status_unique_phone
* @property integer $load_analized
* @property integer $status_unique_date
* @property integer $status_blacklist2
* @property string $person
* @property string $id_irr_duplicate
* @property integer $geocodated
* @property integer $processed
* @property integer $broken
* @property integer $average_price
* @property integer $average_price_count
* @property integer $average_price_address
* @property integer $average_price_address_count
* @property integer $average_price_same
* @property integer $average_price_same_count
* @property integer $radius
* @property string $tags
* @property string $id_in_source;
 * @property integer $date_of_check
* @property integer $disactive
*/

class Parsing extends ParsingSync
{
    public $disactive;
    public $address;
    public $is_balcon;

    public $rooms_count;
    public $coords_x;
    public $coords_y;
    public $images;
    public $phone1;
    public $phone2;
    public $description;
    public $floor;
    public $floorcount;
    public $grossarea;
    public $living_area;
    public $living_area_if_rooms;
    public $kitchen_area;
    public $year;
    public $house_type;
    public $person;

    public function rules()
    {
        return [
            [['house_type'], 'in', 'range' => [1, 2, 3, 4, 5]],
            [['year'], 'integer', 'max' => 2030],
            [['address'], 'string', 'max' => 200],
            [['title'], 'string', 'max' => 200],
            [['url'], 'string', 'min' => 10],
            [['floor', 'floorcount'], 'integer', 'min' => 1, 'max' => 100],
            [['kitchen_area', 'living_area', 'grossarea'], 'double', 'max' => 1000],


        ];
    }


    /**
     * @inheritdoc
     */
    const ARRAY_OF_PHONE_CLASS_BUTTON = [
        'irr' => 'js-productContactInfo',
        'yandex' => 'phones__button',
        'avito' => 'js-action-show-number'

    ];

    const YANDEX_ID_SOURCE = 2;

    const floatPattern = '(\d{1,3}.\d{1,2}|\d{1,3})';

    public function extractTableData($id_sources, $pq_div)
    {

        {

            switch ($id_sources) {
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

                        $selectors = Selectors::getSelectors(Selectors::TYPE_TABLE, $id_sources);
                        // my_var_dump($selectors);
                        $this->title = $pq_div->find("a." . $selectors['YANDEX_TABLE_TITLE_DIV_CLASS'])->text();
                        $div_prices = $pq_div->find("div." . $selectors['YANDEX_TABLE_PRICE_DIV_CLASS'])->text();
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
                        $this->price = $price;

                        //  echo "<br>".$price;
                        $this->url = "https://realty.yandex.ru" . $pq_div->find('a')->attr('href');
                        preg_match("/\d+/", $this->url, $output_array);
                        $this->id = $output_array[0];
                        $starred = $pq_div->find('div.offer-label')->html();

                        if (!empty($starred)) $this->starred = true; else $this->starred = false;
                        $date = $pq_div->find("div." . $selectors['YANDEX_TABLE_PUBLISHED_DATE_DIV_CLASS'])->text();
                        //  echo "<br> date as String".$date;
                        $this->date_start = ParsingExtractionMethods::Date_to_unix($date);
                        //  echo "<br> date_start".$date_start;

                        $this->address = trim($pq_div->find("h4." . $selectors['YANDEX_TABLE_ADDRESS_DIV_CLASS'])->text());
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
                            $starred = $pq_div->find("button." . $starred_div_class)->html();

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


        }
    }

    public function extractPageData($pq, $url, $id_sources)
    {
        switch ($id_sources) {
            case 1:
                {


                    $this->IrrPage($pq, $url);;


                    break;
                }
            case 2:
                {

                    $this->YandexPage($pq, $url);


                    break;
                }
            case 3:
                {

                    $this->AvitoPage($pq, $url);


                    break;
                }
            case 5:
                {

                    $this->CianPage($pq, $url);
                    break;
                }


        }


    }

    public static function IsInAvailablePages($page, \phpQueryObject $pq, $id_source)
    {
        $selectors = Selectors::getSelectors(Selectors::TYPE_STAT, $id_source);
        $arr_pages = array();
        switch ($id_source) {
            case 1:
                {
                    $pq_pagination_list = $pq->find("li." . $selectors['IRR_STAT_PAGINATION_DIV_CLASS']);
                    if ($pq_pagination_list) {

                        foreach ($pq_pagination_list as $page_div) {
                            $page_num = pq($page_div)->text();
                            if (preg_match("/\d+/", $page_num)) {

                                // array_push($page_num ,$arr_pages);
                                $arr_pages[] = $page_num;

                            }
                        }
                 //      my_var_dump($arr_pages);

                    };

                    break;
                }
            case 2:
                {
                    $pq_pagination_list = $pq->find("a." . $selectors['YANDEX_STAT_PAGINATION_DIV_CLASS']);
                    if ($pq_pagination_list) {
                        foreach ($pq_pagination_list as $page_div) {
                            $page_num = pq($page_div)->text();
                            if (preg_match("/\d+/", $page_num)) {
                                //  echo "<br>page_num=" . $page_num;
                                // array_push($page_num ,$arr_pages);
                                $arr_pages[] = $page_num;

                            }
                        }
                    //    my_var_dump($arr_pages);

                    };

                    break;
                }
            case 3:
                {
                    $pq_pagination_list = $pq->find("a." . $selectors['AVITO_STAT_PAGINATION_DIV_CLASS']);
                    if ($pq_pagination_list) {
                        foreach ($pq_pagination_list as $page_div) {
                            $page_num = pq($page_div)->text();
                            if (preg_match("/\d+/", $page_num)) {
                                //  echo "<br>page_num=" . $page_num;
                                // array_push($page_num ,$arr_pages);
                                $arr_pages[] = $page_num;

                            }
                        }
                      //  my_var_dump($arr_pages);

                    };
                    break;
                }
            case 5:
                {
                    if ($pq_pagination_list = $pq->find("." . $selectors['CIAN_STAT_PAGINATION_DIV_CLASS'])) {
                        foreach ($pq_pagination_list as $page_div) {
                            $page_num = pq($page_div)->text();
                            if (preg_match("/\d+/", $page_num)) {
                                //  echo "<br>page_num=" . $page_num;
                                // array_push($page_num ,$arr_pages);
                                $arr_pages[] = $page_num;

                            }
                        }
                     //   my_var_dump($arr_pages);

                    };

                    break;
                }


        }
       if ($arr_pages) $max = max($arr_pages);
        if ($max) info("MAX PAGE IS " . max($arr_pages),WARNING);
        else return true;

        if ($page <= (max($arr_pages) + 1)) return true;
        else return false;

    }


    public static function sourceSequential()
    {
        return [
            1 => [ // irr.ru
                0 => ['type' => 'exception', 'params' => [ // проверка что объявление неудалено вообще
                    'type' => 'URL',
                    'pattern' => '/.+?from=.\d+/', // если вот такая ссылка то выходим и пишем что объект удален
                    'success' => 'EXIT',
                    'return' => 'DELETED'
                ]],
                1 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление более неактично и выходим
                    'pattern' => '/Объявление.+снято.+с.+публикации/',
                    'success' => 'EXIT',
                    'return' => 'DISABLED'
                ]],
                2 => ['type' => 'wait', 'pause' => true, 'duration' => rand(1, 2)],
                3 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'irrSite__wrapper'], // ждем пока появятся табличные данные
                4 => ['type' => 'keyboard', 'buttons' => [0 => 'ESCAPE']], // просто нажимаем кнопку ESCAPE если вдруг появятся всплывающие окна
                5 => ['type' => 'click', 'selector_type' => 'class', 'name' => 'productPage__readMore'], // нажимаем читать дальше
                6 => ['type' => 'keyboard', 'buttons' => [0 => 'ESCAPE']], // просто нажимаем кнопку ESCAPE если вдруг появятся всплывающие окна
                7 => ['type' => 'click', 'selector_type' => 'class', 'name' => self::ARRAY_OF_PHONE_CLASS_BUTTON['irr']], // нажимаем на кнопку показать телефон
                8 => ['type' => 'return', 'PQ' => true] // возвращаем объект phpQuery страницы

            ],
            2 => [ //yandex.ru
                0 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление более неактично и выходим
                    'pattern' => '/Объявление.{1,3}устарело/',
                    'success' => 'EXIT',
                    'return' => 'DISABLED'
                ]],
                3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(4, 7)], // ждем чтобы вести себя как обычный пользователь
                5 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'content'], // ждем пока появятся блок с контентом
                7 => ['type' => 'click', 'selector_type' => 'class', 'name' => self::ARRAY_OF_PHONE_CLASS_BUTTON['yandex']], // нажимаем на кнопку показать телефон
                8 => ['type' => 'return', 'PQ' => true]
            ],
            3 => [ //avito.ru
                3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(2, 3)], // ждем чтобы вести себя как обычный пользователь
                7 => ['type' => 'not_exist', 'selector_type' => 'class', 'name' => 'item-view-content', 'success' => 'EXIT', 'return' => 'DELETED'],
                8 => ['type' => 'return', 'PQ' => true]
            ],
            5 => [ //cian.ru
                0 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление удалено и выходим
                    'pattern' => '/страница.{1,3}не.{1,3}находится.{1,3}на.{1,3}ЦИАН/',
                    'success' => 'EXIT',
                    'return' => 'DELETED'
                ]],
                1 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление более неактично и выходим
                    'pattern' => '/Объявление.{1,3}снято.{1,3}с.{1,3}публикации/',
                    'success' => 'EXIT',
                    'return' => 'DISABLED'
                ]],
                3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(5, 8)],// ждем чтобы вести себя как обычный пользователь
                //  5 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'aside_content--Dbj64'], // ждем пока появятся блок с контентом
                //   7 => ['type' => 'click', 'selector_type' => 'class', 'name' => self::ARRAY_OF_PHONE_CLASS_BUTTON['yandex']], // нажимаем на кнопку показать телефон
                //   8 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'content'], // ждем пока появятся блок с контентом
                4 => ['type' => 'exception', 'params' => [ // проверка что объявление неудалено вообще
                    'type' => 'URL',
                    'pattern' => '/cian.ru\/captcha\/?/', // если вот такая ссылка то выходим и пишем что объект удален
                    'success' => 'EXIT',
                    'return' => 'CAPTCHA'
                ]],
                9 => ['type' => 'return', 'PQ' => true]
            ],
            31 => [ //m.avito.ru
                3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(2, 3)], // ждем чтобы вести себя как обычный пользователь
                4 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление удалено и выходим
                    'pattern' => '/Сохранить.{1,3}поиск/',
                    'success' => 'EXIT',
                    'return' => 'DELETED'
                ]],
                7 => ['type' => 'click', 'selector_type' => 'class', 'name' => self::ARRAY_OF_PHONE_CLASS_BUTTON['avito']],
            ],
//  5 => [ //cian.ru старый шаблон
//                0 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
//                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление удалено и выходим
//                    'pattern' => '/страница.{1,3}не.{1,3}находится.{1,3}на.{1,3}ЦИАН/',
//                    'success' => 'EXIT',
//                    'return' => 'DELETED'
//                ]],
//                1 => ['type' => 'exception', 'params' => [  // проверка на то объявление недеактивировано
//                    'type' => 'TEXT',                                   // если в текте встречается вот такое советание то объявление более неактично и выходим
//                    'pattern' => '/Объявление.{1,3}снято.{1,3}с.{1,3}публикации/',
//                    'success' => 'EXIT',
//                    'return' => 'DISABLED'
//                ]],
//                3 => ['type' => 'wait', 'pause' => true, 'duration' => rand(5, 8)],// ждем чтобы вести себя как обычный пользователь
//                5 => ['type' => 'wait', 'visibility' => true, 'selector_type' => 'class', 'name' => 'offer_container'], // ждем пока появятся блок с контентом
//                8 => ['type' => 'return', 'PQ' => true]
//            ],

        ];
    }

    public static function getTotalCount(\phpQueryObject $pq, $id_source)
    {

        $selectors = Selectors::getSelectors(Selectors::TYPE_STAT, $id_source);
        switch ($id_source) {
            case 1:
                {


                    $text = $pq->find("." . $selectors['AVITO_STAT_TOTALCOUNT_DIV_CLASS'])->text();
                    preg_match("/из (\d+) предложений/", $text, $output_array);
                    $totalCount = $output_array[1];

                    break;
                }
            case 2:
                {


                    $text = $pq->find("." . $selectors['YANDEX_STAT_TOTALCOUNT_DIV_CLASS'])->text();
                    preg_match("/оказать (\d+) объявлен/", $text, $output_array);
                    $totalCount = $output_array[1];
                    break;
                }
            case 3:
                {


                    $totalCount = P::ExtractNumders($pq->find("." . $selectors['AVITO_STAT_TOTALCOUNT_DIV_CLASS'])->text());


                    break;
                }
            case 5:
                {

                    $totalCount = P::ExtractNumders($pq->find("." . $selectors['CIAN_STAT_TOTAL_OFFERS_DIV_CLASS'])->text());


                    break;
                }


        }

        if ($totalCount) return $totalCount;
        else {

            return 0;
        }

    }


    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->cloud;
    }

    /**
     * @inheritdoc
     */


    public function loggedValidate($id_source)
    {
        if (!$this->validate()) {
            $error_log = new ErrorsLog();
            $error_log->id_error = ERROR_PARSING_SYNC_SOURCE_VALIDATION;
            $error_log->body = json_encode($this->errors);
            $error_log->time = time();
            $error_log->save();
        }
    }


    public function LoadParsedInfoToSale($sale, $module)
    {
        $this->region_rus = $module->region_rus;
        $this->oblast_rus = $module->oblast_rus;


        echo "<br> есть соединение";
        // переключаем модель анализа страницы
        switch ($sale->id_sources) {
            // irr
            case 1:
                $response = $this->ParsingLinkIrr($sale->url);
                break;
            // avito
            case 2:
                $response = $this->ParsingLinkYandex($sale->url);
                break;
            case 3:
                $response = $this->ParsingLinkAvito($sale->url);
                break;
            case 5:
                $response = $this->ParsingLinkCian($sale);
                break;
        }
        // обновляем параметры
        $this->UpdateSale($sale);
//

    }

    public function IsConnected()
    {
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);

        $ip = $pq->find('.ip')->find('big')->text();
        echo "<h5>" . $ip . "</h5>";
        if (empty($ip)) return false;
        if ($ip != '') return true;
    }

    public function UpdateSale($sale)
    {
        //  $session = Yii::$app->session;
        //   my_var_dump($this);
        // метод импортирования недостающих данных в модель sale, обносления ( в зависимовти от приходящих данных) или удаления
        $sale->rooms_count = $this->rooms_count;
        if (($sale->grossarea != $this->grossarea)) {
            // если пришла новая ненулевая grossarea, то обновляем ее
            if ($this->grossarea != 0) $sale->grossarea = $this->grossarea;
        }
        // если пришла ненулевая новая living_area, то обновляем ее
        if ($sale->living_area != $this->living_area) {
            if ($this->living_area != 0)
                $sale->living_area = $this->living_area;
        }
        // если пришла ненулевая новая kitchen_area, то обновляем ее
        if ($sale->kitchen_area != $this->kitchen_area) {
            if ($this->kitchen_area != 0) $sale->kitchen_area = $this->kitchen_area;
        }
        // если был этаж нулевой или неправильный, то
        if (($sale->floor == 0) or ($sale->floor > $sale->floorcount) or ($sale->floor != $this->floor)) {
            info('Обновили этаж', 'alert');
            $sale->floor = $this->floor;
        }
        // если был этаж нулевой или неправильный, то
        if (($sale->floorcount == 0) or ($sale->floorcount != $this->floorcount)) $sale->floorcount = $this->floorcount;
        //
        $sale->coords_x = $this->coords_x;
        $sale->coords_y = $this->coords_y;
        info($this->coords_x . " " . $this->coords_y);
        if ($sale->house_type == 0) $sale->house_type = $this->house_type;
        if ($this->phone1) $sale->phone1 = $this->phone1;
        if (!$sale->phone2) $sale->phone2 = $this->phone2;
        if ((($sale->price + 1 < $this->price) or ($sale->price - 1 > $this->price)) and ($this->price != '')) {
            info('Произошло существенное изменение цены во время детального парсинга', 'alert');
            info(" было " . $sale->price . " стало " . $this->price, 'alert');
            $sale->price = $this->price;
        }
        $sale->address = $this->address;
        $sale->disactive = $this->disactive;
        $sale->description = $this->description;
        $sale->person = $this->person;
        //echo " успешно передали person".$sale->person;
        $sale->images = $this->images;
        if (($this->living_area_if_rooms != 0) and ($sale->rooms_count == 30)) $sale->grossarea = $this->living_area_if_rooms;
        if (($this->disactive != 1) and ($this->address != '')) {
            // если пришел новый  адрес  и непустой
            // геокодирования и аналитики
            info("SALE_ADDRESS=".$sale->address." PARSING ADDRESS=".$this->address,DANGER);
            if (trim($sale->address) != trim($this->address)) {

                $sale->address = trim($this->address);


                $sale->load_analized = 0;
            } else  info(" ADDRESS IS THE SAME",SUCCESS);
            $sale->id_address = 0;
            $sale->geocodated = Geocodetion::READY;

        }

    }

    public function ParsingLinkIrr($link)
    {
        $this->disactive = 0;

        $pq = phpQuery::newDocument(my_curl_response($link, '', ''));
        $str = $pq->find('.productPage__characteristicsItemValue')->text();
        if (!empty($pq->find('.productPage__unactiveBlockTitle')->text())) {
            $this->disactive = 2;
            //  echo "<h2>" . $pq->find('.productPage__unactiveBlockTitle')->text() . "</h2>";
        }


        preg_match("/\d\d/", $str, $output_array);
        //достаем параметр $grossarea
        $this->grossarea = (int)$output_array[0];
        $productPage__infoColumnBlockText = $pq->find('.productPage__infoColumnBlockText')->text();
        $productPage__infoColumnBlock = $pq->find('.productPage__infoColumnBlock')->text();

        // достаем параметр $price
        $price = $pq->find('.productPage__price')->attr('content');
        // если $price == 0 то объявление удалено
        if (empty($price)) {
            // echo "<h2>объявление удалено вообще</h2>";
            $this->disactive = 1;
        }
        //  echo " price=".$price;


        // достаем параметр $street
        preg_match_all("/Улица:.+/", $productPage__infoColumnBlockText, $output_array);
        $array_street = preg_split('/Улица:\s/', $output_array[0][0]);
        $this->street = trim($array_street[1]);

        // достаем параметр $house
        preg_match_all("/Дом:.+/", $productPage__infoColumnBlockText, $output_array);
        $array_house = preg_split('/Дом:\s/', $output_array[0][0]);
        $this->house = $array_house[1];

        // генерируем параметр address
        $this->address = $this->street . ", " . $this->house;

        // достаем параметр $floorcount
        preg_match_all("/Этажей в здании:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floorcount = $numbers[0];

        // достаем параметр $rooms_count
        preg_match_all("/Комнат в квартире:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->rooms_count = $numbers[0];

        // достаем параметр $livearea
        preg_match_all("/Жилая площадь:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area = $numbers[0];

        // достаем параметр $living_area if rooms_count = 30;
        preg_match_all("/Площадь продажи:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area_if_rooms = $numbers[0];
        // достаем параметр $kitchen_area
        preg_match_all("/Площадь кухни:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->kitchen_area = $numbers[0];

        // достаем параметр $is_balcon
        preg_match_all("/Балкон|Лоджия/", $productPage__infoColumnBlockText, $output_array);
        if ((!empty($output_array[0][0]) or !empty($output_array[0][2]))) $this->is_balcon = 1;

        // достаем параметр $floorcount
        preg_match_all("/Этаж:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floor = $numbers[0];

        // достаем параметр $house_type;
        preg_match_all("/Материал стен:.+/", $productPage__infoColumnBlockText, $output_array);
        $house_type = preg_split("/Материал стен:/", $output_array[0][0]);
        $this->house_type = parsing_house_type(trim($house_type[1]));

        // достаем параметр $year;
        preg_match("/Год постройки: .+/", $productPage__infoColumnBlock, $year);
        preg_match('/[1,2][9,0]\d\d/', $year[0], $output_array_year);
        $this->year = $output_array_year[0];


    }

    public function IrrPage($pq, $url)
    {
        $this->disactive = 0;
        $str = $pq->find('.productPage__characteristicsItemValue')->text();
        if (!empty($pq->find('.productPage__unactiveBlockTitle')->text())) {
            $this->disactive = 2;
            echo "<h2>" . $pq->find('.productPage__unactiveBlockTitle')->text() . "</h2>";
        }


        preg_match("/\d\d/", $str, $output_array);
        //достаем параметр $grossarea
        $this->grossarea = (int)$output_array[0];
        $productPage__infoColumnBlockText = $pq->find('.productPage__infoColumnBlockText')->text();
        $productPage__infoColumnBlock = $pq->find('.productPage__infoColumnBlock')->text();

        // достаем параметр $price
        $price = $pq->find('.productPage__price')->attr('content');
        // если $price == 0 то объявление удалено
        if (empty($price)) {
            echo "<h2>объявление удалено вообще</h2>";
            $this->disactive = 1;
        }
        $phone = $pq->find('.y0q7tZFoqe8lzygJJ9BOP')->eq(0)->text();
        // echo $phone;
        if ($phone) {
            $this->phone1 = str_replace('+7', '8', preg_replace("/\(|\)|-|\s/", "", $phone));
            // echo "<h1> i've parsed phone!!!!!" . $this->phone1 . "</h1>";
        } else echo "<h1> ТЕЛЕФОНЫ ПЕРЕСТАЛИ ПАРСИТЬСЯ</h1>";

        $lineGallery = $pq->find('div.lineGallery')->html();

        preg_match_all("/content=\"http:\/\/mono.+.jpg/", $lineGallery, $array_of_images);
        $images = [];
        foreach ($array_of_images[0] as $image) {
            $image = str_replace("content=\"", '', $image);
            // echo "<br><img src=\"".$image."\">";
            array_push($images, $image);
        }
        $this->images = serialize($images);
        // echo " <br>".$this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find('div.productPage__infoTextBold_inline')->text();
        $data = json_decode($pq->find('.js-productPageMap')->attr('data-map-info'), true);
        $this->coords_x = round($data['lat'], 5);
        $this->coords_y = round($data['lng'], 5);


        // echo "<br> persin= " . $div_person;
        $this->person = $div_person;
        // доставаем description
        $p_descriprion = $pq->find('p.productPage__descriptionText')->text();
        $this->description = preg_replace("/ Свернуть\z/", "", $p_descriprion);
        // echo "<br> div_descriprion= " . $this->description;
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        // достаем параметр $street
        preg_match_all("/Улица:.+/", $productPage__infoColumnBlockText, $output_array);
        $array_street = preg_split('/Улица:\s/', $output_array[0][0]);
        $street = trim($array_street[1]);

        // достаем параметр $house
        preg_match_all("/Дом:.+/", $productPage__infoColumnBlockText, $output_array);
        $array_house = preg_split('/Дом:\s/', $output_array[0][0]);
        $house = $array_house[1];

        // генерируем параметр address
        $this->address = $street . ", " . $house;

        // достаем параметр $floorcount
        preg_match_all("/Этажей в здании:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floorcount = $numbers[0];

        // достаем параметр $rooms_count
        preg_match_all("/Комнат в квартире:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->rooms_count = $numbers[0];

        // достаем параметр $livearea
        preg_match_all("/Жилая площадь:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area = $numbers[0];

        // достаем параметр $living_area if rooms_count = 30;
        preg_match_all("/Площадь продажи:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area_if_rooms = $numbers[0];
        // достаем параметр $kitchen_area
        preg_match_all("/Площадь кухни:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->kitchen_area = $numbers[0];

        // достаем параметр $is_balcon
        preg_match_all("/Балкон|Лоджия/", $productPage__infoColumnBlockText, $output_array);
        if ((!empty($output_array[0][0]) or !empty($output_array[0][2]))) $this->is_balcon = 1;

        // достаем параметр $floorcount
        preg_match_all("/Этаж:.+/", $productPage__infoColumnBlockText, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floor = $numbers[0];

        // достаем параметр $house_type;
        preg_match_all("/Материал стен:.+/", $productPage__infoColumnBlockText, $output_array);
        $house_type = preg_split("/Материал стен:/", $output_array[0][0]);
        $this->house_type = parsing_house_type(trim($house_type[1]));

        // достаем параметр $year;
        preg_match("/Год постройки: .+/", $productPage__infoColumnBlock, $year);
        preg_match('/[1,2][9,0]\d\d/', $year[0], $output_array_year);
        $this->year = $output_array_year[0];


    }

    public function ParsingLinkAvito($link)
    {

        $this->disactive = 0;
        $pq = phpQuery::newDocument(my_curl_response($link, '', ''));
        // проверка что объявление еще не удалено
        if (!empty($pq->find('div.item-view-warning-content')->text())) {
            //  echo "<h2>" . $pq->find('div.item-view-warning-content')->text() . "</h2>";
            $this->disactive = 2;
        }


        //выбираем блог с параметрами
        $item_params = $pq->find('.item-params')->text();
        // echo $item_params;
        if (empty($item_params)) {
            // объявление удалено вообще
            $this->disactive = 1;
            //  echo "<h2> объявление удалено вообще</h2>";
        }

        // достаем параметр $price
        $price = preg_replace("/[^0-9]/", "", $pq->find('div.price-value > span')->text());
        // обрезаем цену
        $this->price = (int)trim(substr($price, strlen($price) / 2));

        // достаем параметр $street
        $this->address = trim($pq->find('div.item-map-location > span > span')->find('span')->attr('itemprop', 'streetAddress')->text());

        // достаем параметр $house
        preg_match_all("/Дом:.+/", $item_params, $output_array);
        $array_house = preg_split('/Дом:\s/', $output_array[0][0]);
        $this->house = $array_house[1];

        // достаем параметр $floorcount
        preg_match_all("/Этажей в доме:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floorcount = $numbers[0];

        // достаем параметр $rooms_count
        preg_match_all("/Количество комнат:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->rooms_count = $numbers[0];

        // достаем параметр $grossarea
        preg_match_all("/Общая площадь:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->grossarea = $numbers[0];

        if (preg_match('/komnaty/', $link, $output_array)) {
            $this->rooms_count = 30;
            // echo " <br> парсим комнату";
            // достаем параметр $grossarea
            preg_match_all("/Площадь комнаты:.+/", $item_params, $output_array);
            preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
            $this->grossarea = $numbers[0];


        }
        if (preg_match('/studii/', $link, $output_array)) $this->rooms_count = 20;
        // если ищем в комнатах то
        if ($this->rooms_count == 30) {


        }

        // достаем параметр $living_area
        preg_match_all("/Жилая площадь:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area = $numbers[0];
        // достаем параметр $livearea if rooms_count = 30;
        preg_match_all("/Площадь продажи:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area_if_rooms = $numbers[0];
        // достаем параметр $kitchen_area
        preg_match_all("/Площадь кухни:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->kitchen_area = $numbers[0];

        // достаем параметр $floor
        preg_match_all("/Этаж:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floor = $numbers[0];

        // достаем параметр $house_type;
        preg_match_all("/Тип дома.+/", $item_params, $output_array);
        $house_type = preg_split("/Тип дома:/", $output_array[0][0]);
        $this->house_type = parsing_house_type(trim($house_type[1]));

    }

    public function AvitoPage($pq, $url)
    {

        $this->disactive = 0;
        // проверка что объявление еще не удалено
        if (!empty($pq->find('div.item-view-warning-content')->text())) {
            //  echo "<h2>" . $pq->find('div.item-view-warning-content')->text() . "</h2>";
            $this->disactive = 2;
        }


        //выбираем блог с параметрами
        $item_params = $pq->find('.item-params')->text();
        // echo $item_params;
        if (empty($item_params)) {
            // объявление удалено вообще
            $this->disactive = 1;
            //  echo "<h2> объявление удалено вообще</h2>";
        }

        // достаем параметр $price
        $price = preg_replace("/[^0-9]/", "", $pq->find('div.price-value > span')->text());
        // обрезаем цену
        $this->price = (int)trim(substr($price, strlen($price) / 2));
        $this->coords_x = $pq->find('div.js-item-map')->attr('data-map-lat');
        $this->coords_x = $pq->find('div.js-item-map')->attr('data-map-lon');

        $this->price = (int)trim(substr($price, strlen($price) / 2));

        // достаем параметр $street
        $this->address = trim($pq->find('div.item-map-location > span')->find('span')->attr('itemprop', 'streetAddress')->text());
        echo "ADDRESS = ".$this->address;

        // достаем параметр $house
        preg_match_all("/Дом:.+/", $item_params, $output_array);
        $array_house = preg_split('/Дом:\s/', $output_array[0][0]);
        //  $this->house = $array_house[1];

        // достаем параметр $floorcount
        preg_match_all("/Этажей в доме:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floorcount = $numbers[0];

        // достаем параметр $rooms_count
        preg_match_all("/Количество комнат:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->rooms_count = $numbers[0];

        // достаем параметр $grossarea
        preg_match_all("/Общая площадь:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->grossarea = $numbers[0];

        $gallery_list = $pq->find('div.gallery-img-frame');
        $images = [];
        foreach ($gallery_list as $item) {
            preg_match("/(src=|data-url).+jpg/", phpQuery::pq($item), $array_of_images);
            // $image = str_replace('data-url="//', '' , $array_of_images[0]);
            $image = "http://" . preg_replace("/data-url=\"\/\//", "", $array_of_images[0]);
            //   echo "<br> <img src=\"".$image."\">";
            array_push($images, $image);
        }


        $this->images = serialize($images);
        //  echo " <br>".$this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find('div.seller-info-name')->eq(0)->text();

        //   echo "<br> persin= " . $div_person;
        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find('div.item-description')->text();
        // echo "<br> div_descriprion= " . $div_descriprion;
        $this->description = $div_descriprion;
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        if (preg_match('/komnaty/', $url, $output_array)) {
            $this->rooms_count = 30;
            // echo " <br> парсим комнату";
            // достаем параметр $grossarea
            preg_match_all("/Площадь комнаты:.+/", $item_params, $output_array);
            preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
            $this->grossarea = $numbers[0];


        }
        if (preg_match('/studii/', $url, $output_array)) $this->rooms_count = 20;
        // если ищем в комнатах то
        if ($this->rooms_count == 30) {


        }

        // достаем параметр $living_area
        preg_match_all("/Жилая площадь:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area = $numbers[0];
        // достаем параметр $livearea if rooms_count = 30;
        preg_match_all("/Площадь продажи:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->living_area_if_rooms = $numbers[0];
        // достаем параметр $kitchen_area
        preg_match_all("/Площадь кухни:.+/", $item_params, $output_array);
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $output_array[0][0], $numbers);
        $this->kitchen_area = $numbers[0];

        // достаем параметр $floor
        preg_match_all("/Этаж:.+/", $item_params, $output_array);
        preg_match("/\d+/", $output_array[0][0], $numbers);
        $this->floor = $numbers[0];

        // достаем параметр $house_type;
        preg_match_all("/Тип дома.+/", $item_params, $output_array);
        $house_type = preg_split("/Тип дома:/", $output_array[0][0]);
        $this->house_type = parsing_house_type(trim($house_type[1]));

    }


    public function ParsingLinkYandex($link)
    {

        $this->disactive = 0;
        $file = my_curl_response($link, '', '');
        $pq = phpQuery::newDocument($file);
        // проверка что объявление еще не удалено
        if (($pq->find('h2.offer-card__inactive-title')->text() == 'Объявление устарело')) {
            //    echo "<h2>" . $pq->find('div.item-view-warning-content')->text() . "</h2>";
            $this->disactive = 2;
        }

        // достаем параметр $street
        $address_line = $pq->find('h2.offer-card__address')->text();
        if (empty($address_line)) {
            // объявление удалено вообще
            $this->disactive = 1;
            //   echo "<h2> объявление удалено вообще</h2>";
        }
        $address_line = explode(",", $address_line);
        $this->house = array_pop($address_line);
        $this->street = array_pop($address_line);
        $this->address = $this->street . ", д. " . $this->house;
        //my_var_dump($address_line);


        // достаем параметр $rooms_count
        $this->rooms_count = $pq->find('.offer-card__feature_name_rooms-total')->find('.offer-card__feature-value')->text();
        // достаем параметр $floors
        $floors = $pq->find('.offer-card__feature_name_floors-total-apartment')->find('.offer-card__feature-value')->text();
        // echo "<br> floors= " . $floors;
        $floors = preg_split("/\sиз\s/", $floors);
        $this->floor = $floors[0];
        $this->floorcount = $floors[1];

        // достаем параметр $grossarea
        $grossarea = $pq->find('.offer-card__feature_name_total-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $grossarea, $numbers);
        $this->grossarea = $numbers[0];
        //  echo "<br> grossarea=" . $grossarea;

        // достаем параметр $live_area
        $living_area = $pq->find('.offer-card__feature_name_living-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $living_area, $numbers);
        $this->living_area = $numbers[0];

        // достаем параметр $kitchen_area
        $kitchen_area = $pq->find('.offer-card__feature_name_kitchen-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $kitchen_area, $numbers);
        $this->kitchen_area = $numbers[0];
        // echo "<br> kitchen_area=" . $kitchen_area;

        // достаем параметр $year
        $year = $pq->find('.offer-card__feature_name_building-year')->find('.offer-card__feature-value')->text();
        preg_match("/\d+/", $year, $numbers);
        $this->year = $numbers[0];

        // достаем параметр $house_type
        $house_type = $pq->find('.offer-card__feature_name_building-type')->find('.offer-card__feature-value')->text();
        // echo "<br> house_type=" . $house_type;
        $this->house_type = preg_match_house_type(trim($house_type));

        // echo "<br> house_type=" . $house_type;

        // достаем параметр $price методом выдирания из meta[name=description] беспробельного шаблона "Цена:.{5,11}руб" и  последующим удалением всех не чиловых символов в итоге получаем цену
        $price = $pq->find('meta[name=description]')->attr('content');

        $price = preg_replace('/\s+/', '', $price);
        preg_match("/Цена:.{5,13}руб/", $price, $price_string);
        $this->price = preg_replace("/\D+/", "", $price_string[0]);


        if (preg_match('/Комната/', $pq->find('div.breadcrumbs-list')->find('a')->text(), $output_array)) {
            $this->rooms_count = 30;
            // echo " <br> парсим комнату";

            // достаем параметр $grossarea
            $grossarea = $pq->find('.offer-card__feature_name_rooms-area')->find('.offer-card__feature-value')->text();
            preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $grossarea, $numbers);
            $this->grossarea = $numbers[0];
            // echo "<br> grossarea=" . $grossarea;

        }
        if (preg_match('/Студии/', $pq->find('div.breadcrumbs-list')->find('a')->text(), $output_array)) {
            $this->rooms_count = 20;
            // echo " <br> парсим студию";
        }
    }

    public function YandexPage($pq, $url)
    {

        $this->disactive = 0;
        // проверка что объявление еще не удалено
        if (($pq->find('h2.offer-card__inactive-title')->text() == 'Объявление устарело')) {
            //    echo "<h2>" . $pq->find('div.item-view-warning-content')->text() . "</h2>";
            $this->disactive = 2;
        }

        // достаем параметр $street
        $address_line = $pq->find('h2.offer-card__address')->text();
        if (empty($address_line)) {
            // объявление удалено вообще
            $this->disactive = 1;
            //   echo "<h2> объявление удалено вообще</h2>";
        }

        $gallery_list = $pq->find('div.offer-card__photos-wrapper')->find('a');
        // echo $gallery_list;
        $images = [];
        foreach ($gallery_list as $item) {
            $image = preg_replace("/\A\/\//", "", phpQuery::pq($item)->attr('href'));
            $image = "https://" . $image;
            array_push($images, $image);
        }
        //   my_var_dump($images);


        $this->images = serialize($images);
        //  echo " <br>" . $this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find('div.offer-card__author-name')->eq(0)->text();

        echo "<br> person= " . $div_person;
        $div_phone = $pq->find('span.phones__phone')->text();
        echo " <br> div_phone1 " . $div_phone;

        preg_match_all("/\d+\s\d{3}\s\d{3}-\d{2}-\d{2}/", $div_phone, $output_array);
        my_var_dump($output_array);
        $div_phone1 = preg_replace("/\D+/", "", $output_array[0][0]);
        $phone1 = preg_replace("/\A7/", "8", $div_phone1);
        if ($phone1 != '') $this->phone1 = $phone1; else $this->phone1 = 'no phone';
        echo " <br> PHONE1 =  " . $phone1;
        if (!empty($output_array[0][3])) {
            $div_phone2 = preg_replace("/\D+/", "", $output_array[0][3]);
            $phone2 = preg_replace("/\A7/", "8", $div_phone2);
            if ($phone2 != '') $this->phone2 = $phone2; else $this->phone2 = 'no phone';
            //  echo "<br> phone2= " . $this->phone2;
        }


        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find('div.offer-card__desc-text')->text();


        //  echo "<br> div_descriprion= " . $div_descriprion;
        $this->description = $div_descriprion;
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        echo " <br> address-line" . $address_line;
        $address_line = explode(",", $address_line);
        // my_var_dump($address_line);
        $house = array_pop($address_line);
        $street = array_pop($address_line);
        $this->address = $street . ", д. " . $house;
        $data = json_decode($pq->find('#offer-map')->attr('data-bem'), true);
        $this->coords_x = round($data['offer-map']['placemarks'][0]['lat'], 5);
        $this->coords_y = round($data['offer-map']['placemarks'][0]['lon'], 5);


        // достаем параметр $rooms_count
        $this->rooms_count = $pq->find('.offer-card__feature_name_rooms-total')->find('.offer-card__feature-value')->text();
        // достаем параметр $floors
        $floors = $pq->find('.offer-card__feature_name_floors-total-apartment')->find('.offer-card__feature-value')->text();
        // echo "<br> floors= " . $floors;
        $floors = preg_split("/\sиз\s/", $floors);
        $this->floor = $floors[0];
        $this->floorcount = $floors[1];

        // достаем параметр $grossarea
        $grossarea = $pq->find('.offer-card__feature_name_total-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $grossarea, $numbers);
        $this->grossarea = preg_replace("/,/", ".", $numbers[0]);
        //  echo "<br> grossarea=" . $grossarea;

        // достаем параметр $live_area
        $living_area = $pq->find('.offer-card__feature_name_living-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $living_area, $numbers);
        $this->living_area = preg_replace("/,/", ".", $numbers[0]);


        // достаем параметр $kitchen_area
        $kitchen_area = $pq->find('.offer-card__feature_name_kitchen-area')->find('.offer-card__feature-value')->text();
        preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $kitchen_area, $numbers);
        $this->kitchen_area = preg_replace("/,/", ".", $numbers[0]);
        // echo "<br> kitchen_area=" . $kitchen_area;

        // достаем параметр $year
        $year = $pq->find('.offer-card__feature_name_building-year')->find('.offer-card__feature-value')->text();
        preg_match("/\d+/", $year, $numbers);
        $this->year = $numbers[0];

        // достаем параметр $house_type
        $house_type = $pq->find('.offer-card__feature_name_building-type')->find('.offer-card__feature-value')->text();
        // echo "<br> house_type=" . $house_type;
        $this->house_type = preg_match_house_type(trim($house_type));

        // echo "<br> house_type=" . $house_type;

//        // достаем параметр $price методом выдирания из meta[name=description] беспробельного шаблона "Цена:.{5,11}руб" и  последующим удалением всех не чиловых символов в итоге получаем цену
//        $price = $pq->find('meta[name=description]')->attr('content');
//
//        $price = preg_replace('/\s+/', '', $price);
//        preg_match("/Цена:.{5,13}руб/", $price, $price_string);
        // $this->price = preg_replace("/\D+/", "", $price_string[0]);

        $div_prices = $pq->find('h3.offer-price')->text();
        // забираем цифры
        preg_match("/\d+,\d+|\d+/", $div_prices, $output_array);
        $price = floatval(str_replace(",", ".", $output_array[0]));
        //echo " <br> поменяли запятую на точку ".$prices;

        if (preg_match("/млн/", $div_prices, $output_array1)) {
            //echo " <br> нашли слово млн ";
            $price = $price * 1000000;
        } else  $this->price = $price * 1000;


        if (preg_match('/Комната/', $pq->find('div.breadcrumbs-list')->find('a')->text(), $output_array)) {
            $this->rooms_count = 30;
            // echo " <br> парсим комнату";

            // достаем параметр $grossarea
            $grossarea = $pq->find('.offer-card__feature_name_rooms-area')->find('.offer-card__feature-value')->text();
            preg_match("/\d{1,3}.\d{1,2}|\d{1,3}/", $grossarea, $numbers);
            $this->grossarea = preg_replace("/,/", ".", $numbers[0]);
            // echo "<br> grossarea=" . $grossarea;

        }
        if (preg_match('/Студии/', $pq->find('div.breadcrumbs-list')->find('a')->text(), $output_array)) {
            $this->rooms_count = 20;
            // echo " <br> парсим студию";
        }
    }

    public function ParsingLinkCian($sale)
    {
        $priceUP = $sale->price + 50000;
        $priceDown = $sale->price - 50000;

        // формируем referer;
        $Referer = "https://novgorod.cian.ru/cat.php?currency=2&deal_type=sale&engine_version=2&maxprice=$priceUP&minprice=$priceDown&offer_type=flat&region=4694&room1=$sale->rooms_count";

        $this->disactive = 0;
        $file = $this->CurlCian($sale, '', $Referer);
        $pq = phpQuery::newDocument($file);
        // проверка что объявление еще не удалено
        if (($pq->find('div.object_descr_header_warning')->text() == 'Объявление снято с публикации')) {
            //  echo "<br><h2>" . $pq->find('div.object_descr_header_warning')->text() . "</h2>";
            $this->disactive = 2;
        }
        //if (!empty($pq->find('#captcha'))) echo "<h3>CAPTCHA!!!!!</h3>";


        // достаем параметр $rooms_count
        $rooms_count = $pq->find('.object_descr_title')->text();
        preg_match("/\d+/", $rooms_count, $numbers);
        $this->rooms_count = $numbers[0];

        // выделяем блок с адресом
        if (empty($pq->find('.object_descr_addr')->find('a:eq(4)')->text())) {
            $this->street = $pq->find('.object_descr_addr')->find('a:eq(2)')->text();
            $this->house = $pq->find('.object_descr_addr')->find('a:eq(3)')->text();
        } else {
            $this->street = $pq->find('.object_descr_addr')->find('a:eq(3)')->text();
            $this->house = $pq->find('.object_descr_addr')->find('a:eq(4)')->text();

        }


        $this->address = $this->street . ", " . $this->house;

        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find('.object_descr_props')->text());
        //  echo "<br> object_descr_props= " . $object_descr_props;

        // обрезаем все ненужные пробелы
        $object_descr_props = str_replace("/(?:\s|&nbsp;)+/", '', $object_descr_props);

        // вырезаем блок с этажами
        preg_match_all("/Этаж:\d+\D+\/\D+\d+/", $object_descr_props, $floors);
        $floors = preg_split("/\//", $floors[0][0]);
        $this->floor = preg_replace("/\D+/", "", $floors[0]);
        $this->floorcount = preg_replace("/\D+/", "", $floors[1]);

        // достаем параметр $grossarea
        preg_match("/Общаяплощадь:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $grossarea);
        $this->grossarea = floatval(str_replace(',', '.', $grossarea[1]));


        // достаем параметр $living_area
        preg_match("/Жилаяплощадь:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $living_area);

        $this->living_area = floatval(str_replace(',', '.', $living_area[1]));

        // достаем параметр $kitchen_area
        preg_match("/Площадькухни:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $kitchen_area);
        $this->kitchen_area = floatval(str_replace(',', '.', $kitchen_area[1]));

        // достаем параметр $year
        preg_match("/Годпостройки:\d+\D+м/", $object_descr_props, $year);
        $this->year = preg_replace("/\D+/", "", $year[0]);


        // достаем параметр $house_type
        $this->house_type = preg_match_house_type($object_descr_props);

        // достаем параметр $price
        $price = $pq->find('#price_rur]')->text();
        $price = preg_split("/,/", $price);
        $this->price = $price[0];

        if (preg_match('/комната/', $pq->find('div.object_descr_title')->text(), $output_array)) {
            $this->rooms_count = 30;
            // echo " <br> парсим комнату";
            // достаем параметр $grossarea
            preg_match("/Площадькомнаты:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $grossarea);
            $this->grossarea = floatval(str_replace(',', '.', $grossarea[1]));


        }

    }

    public function CianPage1($pq, $url)
    {
        $this->disactive = 0;
        // проверка что объявление еще не удалено
        if (($pq->find('div.object_descr_header_warning')->text() == 'Объявление снято с публикации')) {
            //  echo "<br><h2>" . $pq->find('div.object_descr_header_warning')->text() . "</h2>";
            $this->disactive = 2;
        }

        //if (!empty($pq->find('#captcha'))) echo "<h3>CAPTCHA!!!!!</h3>";


        // достаем параметр $rooms_count
        $rooms_count = $pq->find('.object_descr_title')->text();
        if (preg_match("/\d+/", $rooms_count, $numbers)) {
            // echo "<br> парсим квартиру";
            $this->rooms_count = $numbers[0];
        };
        if (preg_match("/комната/", $rooms_count, $numbers)) {
            // echo "<br> парсим комнату";
            $this->rooms_count = round(30);
        };
        if (preg_match("/студия/", $rooms_count, $numbers)) {
            $this->rooms_count = 20;
        };
        //  echo "<br> ROOMS_COUNT = " . $this->rooms_count;
        // выделяем блок с адресом
        if (empty($pq->find('.object_descr_addr')->find('a:eq(4)')->text())) {
            $street = $pq->find('.object_descr_addr')->find('a:eq(2)')->text();
            $house = $pq->find('.object_descr_addr')->find('a:eq(3)')->text();
        } else {
            $street = $pq->find('.object_descr_addr')->find('a:eq(3)')->text();
            $house = $pq->find('.object_descr_addr')->find('a:eq(4)')->text();

        }
        $this->address = $street . ", " . $house;


        $gallery_list = $pq->find('div.fotorama__nav__frame--thumb');
        $images = [];
        foreach ($gallery_list as $item) {

            preg_match("/src=\".+\.jpg/", phpQuery::pq($item), $array_of_images);

            $image = preg_replace("/src=\"/", "", $array_of_images[0]);
            //   echo "<br> <img src=\"".$image."\">";
            array_push($images, $image);
        }
        //  my_var_dump($images);


        $this->images = serialize($images);
        //  echo " <br>" . $this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find('h3.realtor-card__title')->eq(0)->text();

        //echo "<br> person= " . $div_person;
        $div_phone = $pq->find('div.cf_offer_show_phone-number')->text();
        preg_match("/\d+\s\d{3}\s\d{3}-\d{2}-\d{2}/", $div_phone, $output_array);

        $div_phone = preg_replace("/\D+/", "", $output_array[0]);
        $this->phone1 = preg_replace("/\A7/", "8", $div_phone);
        //echo "<br> phone= " . $this->phone1;
        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find('div.object_descr_text')->text();
        $div_descriprion = preg_split("/$this->person/", $div_descriprion);

        //  echo "<br> div_descriprion= " . $div_descriprion[0];
        $this->description = $div_descriprion[0];
        // url
        $this->url = $url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($url);

        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find('.object_descr_props')->text());
        //  echo "<br> object_descr_props= " . $object_descr_props;

        // обрезаем все ненужные пробелы
        $object_descr_props = str_replace("/(?:\s|&nbsp;)+/", '', $object_descr_props);

        // вырезаем блок с этажами
        preg_match_all("/Этаж:\d+\D+\/\D+\d+/", $object_descr_props, $floors);
        $floors = preg_split("/\//", $floors[0][0]);
        $this->floor = preg_replace("/\D+/", "", $floors[0]);
        $this->floorcount = preg_replace("/\D+/", "", $floors[1]);

        // достаем параметр $grossarea
        preg_match("/Общаяплощадь:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $grossarea);
        $this->grossarea = floatval(str_replace(',', '.', $grossarea[1]));


        // достаем параметр $living_area
        preg_match("/Жилаяплощадь:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $living_area);

        $this->living_area = floatval(str_replace(',', '.', $living_area[1]));

        // достаем параметр $kitchen_area
        preg_match("/Площадькухни:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $kitchen_area);
        $this->kitchen_area = floatval(str_replace(',', '.', $kitchen_area[1]));

        // достаем параметр $year
        preg_match("/Годпостройки:\d+\D+м/", $object_descr_props, $year);
        $this->year = preg_replace("/\D+/", "", $year[0]);


        // достаем параметр $house_type
        $this->house_type = preg_match_house_type($object_descr_props);

        // достаем параметр $price
        $price = $pq->find('#price_rur]')->text();
        $price = preg_split("/,/", $price);
        $this->price = $price[0];

        if (preg_match('/комната/', $pq->find('div.object_descr_title')->text(), $output_array)) {


            // достаем параметр $grossarea
            preg_match("/Площадькомнаты:(\d{1,3}.\d{1,2}|\d{1,3})\D+м/", $object_descr_props, $grossarea);
            $this->grossarea = floatval(str_replace(',', '.', $grossarea[1]));


        }


    }

    public function CianPage($pq, $url)
    {
        $selectors = Selectors::getSelectors(Selectors::TYPE_DETAILED, CIAN_ID_SOURCE);

        // достаем параметр $rooms_count
        // info('I','alert');
        $title = $pq->find("." . $selectors['CIAN_PAGE_TITLE_DIV_CLASS'])->text();

        if (preg_match("/\d/", $title, $numbers)) {
            // echo "<br> парсим квартиру";
            $this->rooms_count = $numbers[0];
        };
        if (preg_match("/свободной/", $title, $numbers)) {
            // echo "<br> парсим комнату";
            $this->rooms_count = 10;
        };
        if (preg_match("/Комната/", $title, $numbers)) {
            echo "<br> парсим комнату";
            $this->rooms_count = 30;
        };
        if (preg_match("/Студия/", $title, $numbers)) {
            $this->rooms_count = 20;
        };
        //  echo "<br> ROOMS_COUNT = " . $this->rooms_count;
        // вычистяем количество елементов полного адреса (последние 2 отвечают за улицу и дом)
        $count = count($pq->find("a." . $selectors['CIAN_PAGE_ADDRESS_ITEM_DIV_CLASS']));
        //берем блок с адресами
        $street = $pq->find("." . $selectors['CIAN_PAGE_ADDRESS_DIV_CLASS'])->find("a:eq(" . ($count - 2) . ")")->text();
        $house = $pq->find("." . $selectors['CIAN_PAGE_ADDRESS_DIV_CLASS'])->find("a:eq(" . ($count - 1) . ")")->text();
        $this->address = $street . ", " . $house;


        $gallery_list = $pq->find("div." . $selectors['CIAN_PAGE_FOTORAMA_DIV_CLASS']);
        $images = [];
        foreach ($gallery_list as $item) {

            preg_match("/src=\".+\.jpg/", phpQuery::pq($item), $array_of_images);

            $image = preg_replace("/src=\"/", "", $array_of_images[0]);
            //   echo "<br> <img src=\"".$image."\">";
            array_push($images, $image);
        }
        //  my_var_dump($images);
        $data = $pq->find("a." . $selectors['CIAN_PAGE_LINK_COORDS_DIV_CLASS'])->attr('href');
        preg_match("/center=(.+)&/", $data, $output_array);
        $data = preg_split("/%2C/", $output_array[1]);
        $this->coords_x = round($data[0], 5);
        $this->coords_y = round($data[1], 5);

        $this->images = serialize($images);
        //  echo " <br>" . $this->images;
        //  echo " price=".$price;
        // доставем person
        $div_person = $pq->find("h2." . $selectors['CIAN_PAGE_PERSON_DIV_CLASS'])->text();
        // echo "<br> person= " . $div_person;
        $div_phone = $pq->find("div." . $selectors['CIAN_PAGE_PHONE_DIV_CLASS'])->text();
        echo $div_phone;
        $output_array =  preg_replace("/\D+/", "",$div_phone);

        $div_phone = preg_replace("/\D+/", "", $output_array);
        $this->phone1 = preg_replace("/\A7/", "8", $div_phone);
        //echo "<br> phone= " . $this->phone1;
        $this->person = $div_person;
        // доставаем description
        $div_descriprion = $pq->find("p." . $selectors['CIAN_PAGE_DESCRIPTION_DIV_CLASS'])->text();

        //  echo "<br> div_descriprion= " . $div_descriprion[0];
        $this->description = $div_descriprion;
        // url

        // выделяем блок с информацией
        $object_descr_props = preg_replace('/\s+/', '', $pq->find("div." . $selectors['CIAN_PAGE_INFOBLOCK_DIV_CLASS'])->text());
        echo "<br> object_descr_props= " . $object_descr_props;

        // вырезаем блок с этажами
        preg_match_all("/Этаж\d+из\d+/", $object_descr_props, $floors);
        //  my_var_dump($floors);
        $floors = preg_split("/из/", $floors[0][0]);
        $this->floor = preg_replace("/\D+/", "", $floors[0]);
        $this->floorcount = preg_replace("/\D+/", "", $floors[1]);

        // достаем параметр $grossarea
        preg_match("/Общая(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $grossarea);
        $this->grossarea = round(floatval($grossarea[1]), 1);


        // достаем параметр $living_area
        preg_match("/Жилая(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $living_area);

        $this->living_area = round(floatval($living_area[1]));

        // достаем параметр $kitchen_area
        preg_match("/Кухня(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props, $kitchen_area);
        $this->kitchen_area = round(floatval($kitchen_area[1]));

        // достаем параметр $year
        preg_match("/Построен(\d+)/", $object_descr_props, $year);
        $this->year = $year[1];


        // достаем параметр $house_type
        // выделяем блок с информацией
        $object_descr_props2 = preg_replace('/\s+/', '', $pq->find("." . $selectors['CIAN_PAGE_INFOBLOCK2_DIV_CLASS'])->text());
        // echo "<br> object_descr_props= " . $object_descr_props;
        preg_match("/Панельный|Кирпичный|Деревянный|Монолитный|Кирпично-Монолитный|Сталинский/", $object_descr_props2, $house_type);

        $this->house_type = preg_match_house_type($house_type[0]);


        // достаем параметр $price
        preg_match("/.\"offerPrice\":(.\d+)/", $pq->html(), $output_array);

        $this->price = $output_array[1];
        //  echo "price = ".$this->price;

        if ($this->rooms_count == 30) {
            // достаем параметр $grossarea
            preg_match("/Площадькомнаты(\d{1,3}.\d{1,2}|\d{1,3})м/", $object_descr_props2, $grossarea);
            // my_var_dump($grossarea);
            $this->grossarea = round(floatval($grossarea[1]), 1);


        }


    }

    public function CurlCian($sale, $proxy = '')
    {

        $priceUP = $sale->price + 50000;
        $priceDown = $sale->price - 50000;

        // формируем referer;
        $Referer = "https://novgorod.cian.ru/cat.php?currency=2&deal_type=sale&engine_version=2&maxprice=$priceUP&minprice=$priceDown&offer_type=flat&region=4694&room1=$sale->rooms_count";
        $header = array();
        $header[] = "Host: novgorod.cian.ru";
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0";
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.2";
        $header[] = "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4,es;q=0.2";
        //  $header[] = "Accept-Encoding: deflate, br";
        $header[] = "Accept: */*";
        //$header[] = "Cache-Control:max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "DNT: 1";
        $header[] = "Referer: $Referer";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 90);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_PROXY, $proxy);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'C:\OpenServer\domains\test\web\tmp\curlcookie2.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'C:\OpenServer\domains\test\web\tmp\curlcookie2.txt');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $sale->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $str = curl_exec($curl);
        // echo  $str;
        curl_close($curl);

        //$dom = new simple_html_dom();
        // $dom->load(gzdecode($str));
        return $str;
    }

}
