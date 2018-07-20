<?php

namespace app\models\ParsingModels;

use Yii;
use phpQuery;
use yii\base\Model;
use app\models\Selectors;
use app\models\ParsingExtractionMethods;
use app\models\ErrorsLog;
use app\utils\P;

class ParsingSync extends Model
{
    public $title;
    public $id_sources;
    public $address_line;
    public $url;
    public $id_in_source;
    public $price;
    public $starred;
    public $date_start;
    public $date_start_about;

    /**
     * @inheritdoc
     */


    public function extractTableData($id_sources, $pq_div)
    {
        $this->id_sources = $id_sources;

        switch ($id_sources) {
            case 1:
                {
                    $this->extractTabledIrr($pq_div);
                    break;
                }
            case 2:
                {
                    $this->extractTabledYandex($pq_div);

                    break;
                }
            case 3:
                {


                    $this->extractTabledAvito($pq_div);
                    break;

                }
            case 5:
                {
                    $this->extractTabledCian($pq_div);
                    break;
                }


        }


    }


    public function extractTabledYandex($pq_div)
    {

        $selectors = \Yii::$app->params['selectors'];

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
        $url = $pq_div->find('a')->attr('href');
        if (!strpos($url, "realty.yandex.ru")) $this->url = "https://realty.yandex.ru" . $url;
        else $this->url = $url;

        preg_match("/\d+/", $this->url, $output_array);
        $this->id_in_source = $output_array[0];
        $starred = $pq_div->find('div.offer-label')->html();

        if (!empty($starred)) $this->starred = true; else $this->starred = false;
        $date = $pq_div->find("div." . $selectors['YANDEX_TABLE_PUBLISHED_DATE_DIV_CLASS'])->text();
        echo "<br> date as String" . $date;
        if (preg_match("/(\d+)\sчасов\sназад/", $date, $output_array)) {
            $this->date_start = time() - $output_array[1] * 60 * 60;
            $this->date_start_about = true;
        } else {
            $this->date_start = ParsingExtractionMethods::Date_to_unix($date);

        }
        //  echo "<br> date_start".$date_start;

        $this->address_line = trim($pq_div->find("." . $selectors['YANDEX_TABLE_ADDRESS_DIV_CLASS'])->text());
    }

    public function extractTabledIrr($pq_div)
    {

        $selectors = \Yii::$app->params['selectors'];

        // my_var_dump($selectors);
        $this->title = $pq_div->find("div." . $selectors['IRR_TABLE_TITLE_DIV_CLASS'])->text();
        $this->price = preg_replace("/\D+/", "", $pq_div->find("div." . $selectors['IRR_TABLE_PRICE_DIV_CLASS'])->text());
        $this->url = $pq_div->find('a')->attr('href');
        preg_match("/advert\d+.html/", $this->url, $output_array1);
        preg_match("/\d+/", $output_array1[0], $output_array);

        $this->id_in_source = $output_array[0];
        $starred = $pq_div->find('div.js-servicesIcons')->html();

        if (!empty($starred)) $this->starred = true; else $this->starred = false;
        $this->address_line = trim($pq_div->find("div." . $selectors['IRR_TABLE_ADDRESS_DIV_CLASS'])->text());

        $date = $pq_div->find("div." . $selectors['IRR_TABLE_PUBLISHED_DATE_DIV_CLASS'])->html();

        $date = preg_grep("/$/", explode("\n", $date));
        //    my_var_dump($date);
        //  echo "<br> date as String".$date[7];
        $this->date_start = ParsingExtractionMethods::Date_to_unix($date[7]);
        //  echo "<br> date_start".$date_start;


    }

    public function extractTabledCian($pq_div)
    {

        $selectors = \Yii::$app->params['selectors'];

        //  my_var_dump($selectors);

        //  echo "<br>\$title_div_class=" . "div." . $title_div_class;

        $this->title = $pq_div->find("div." . $selectors['CIAN_TABLE_TITLE_DIV_CLASS'])->text();
        //  echo "<br>TITLE=" . $title;
        // берем div c ценой

        $this->price = P::ExtractNumders($pq_div->find("div." . $selectors['CIAN_TABLE_PRICE_DIV_CLASS'])->text());

        $this->url = $pq_div->find("a." . $selectors['CIAN_TABLE_URL_DIV_CLASS'])->attr('href');

        // вытаскиваем ссылку
        // вытаскиваем id
        $array_id = array_reverse(preg_split("/\//", $this->url));
        $this->id_in_source = $array_id[1];

        $starred = $pq_div->find("button." . $selectors['CIAN_TABLE_STARRED_DIV_CLASS'])->html();

        if (!empty($starred)) $starred = true; else $starred = false;

        // вытаскиваем address_line и preg_split by "Великий Новгород";


        $address_line = $pq_div->find("div." . $selectors['CIAN_TABLE_ADDRESS_DIV_CLASS'])->text();
        // echo "<br> \$address_line " . $address_line;
        //  echo "<br> \$module " .\Yii::$app->params['module']->region_rus;
        $address_line = preg_split("/" . \Yii::$app->params['module']->region_rus . ",/", $address_line);
        $this->address_line = trim($address_line[1]);

        $date = $pq_div->find("div." . $selectors['CIAN_TABLE_PUBLISHED_DATE_DIV_CLASS'])->text();
        //  echo "<br> date as String=" . $date;
        $this->date_start = ParsingExtractionMethods::Date_to_unix($date, 'cian');
        //  echo "<br> date_start" . $date_start;


    }


    public function extractTabledAvito($pq_div)
    {

        $selectors = \Yii::$app->params['selectors'];
        //    my_var_dump($selectors);


        $this->title = trim($pq_div->find("a." . $selectors['AVITO_TABLE_TITLE_DIV_CLASS'])->text());
        $prices = $pq_div->find("div." . $selectors['AVITO_TABLE_PRICE_DIV_CLASS'])->attr('data-prices');
        // вырезаем шаблон
        preg_match("/\"RUB\":\d+,\"USD/", $prices, $output_array);
        // забираем цифры

        preg_match("/\d+/", $output_array[0], $output_array);

        $this->url = "https://www.avito.ru" . $pq_div->find("a." . $selectors['AVITO_TABLE_TITLE_DIV_CLASS'])->attr('href');
        $this->price = $output_array[0];

        $this->address_line = trim(preg_replace("/📢📲/u", "", trim($pq_div->find("p." . $selectors['AVITO_TABLE_ADDRESS_DIV_CLASS'])->text())));
        $date = $pq_div->find("div." . $selectors['AVITO_TABLE_PUBLISHED_DATE_DIV_CLASS'])->attr('data-absolute-date');
        //  echo "<br> date as String" . $date;
        $this->date_start = ParsingExtractionMethods::Date_to_unix($date);
        //  echo "<br> date_start".$date_start;

        $this->id_in_source = array_pop(preg_split("/_/", $this->url));;


    }


    public function rules()
    {
        return [
            [['id_in_source'], 'string'],
            [['address_line'], 'string', 'max' => 200],
            [['title'], 'string', 'max' => 200],
            [['url'], 'string', 'min' => 10],
            [['date_start'], 'integer', 'min' => time() - 365 * 24 * 60 * 60, 'max' => time() + 1],
            [['price'], 'integer', 'min' => 0, 'max' => 1000000000],


        ];
    }

    public function loggedValidate($id_source)
    {
        if (!$this->validate()) {
            $error_log = new ErrorsLog();
            $error_log->id_error = ERROR_PARSING_SYNC_SOURCE_VALIDATION;
            $error_log->body = json_encode($this->errors);
            $error_log->time = time();
            $error_log->save();
        } else return true;
    }


}
