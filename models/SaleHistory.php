<?php

namespace app\models;

use Yii;
use phpQuery;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "Velikiy_Novgorod_sale".
 *
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
 * @property integer $date_of_check
 * @property integer $disactive
 * @property string $id_in_source
 */

class SaleHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_history';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_history";
        }
    }

    public static function setTablePrefix($prefix)
    {
        self::$tablePrefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'date_start', 'rooms_count', 'price', 'id_address', 'house_type', 'floor', 'floorcount', 'id_sources', 'status_unique_phone', 'status_unique_date', 'status_blacklist2', 'analized', 'year'], 'integer'],
            [['coords_x', 'coords_y'], 'number'],
            [ ['price'], 'number', 'min' => 1000, 'max' => 100000000],
            [['description', 'images', 'url', 'person', 'id_irr_duplicate'], 'string'],
            [['title', 'phone1', 'city', 'address', 'locality'], 'string', 'max' => 255],
            [['id_in_source'], 'string', 'max' => 40],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Date Start',
            'rooms_count' => 'Rooms Count',
            'title' => 'Title',
            'price' => 'Price',
            'phone1' => 'Phone1',
            'city' => 'City',
            'address' => 'Address',
            'coords_x' => 'Coords X',
            'coords_y' => 'Coords Y',
            'id_street' => 'Id Street',
            'description' => 'Description',
            'floor' => 'Floor',
            'floorcount' => 'Floorcount',
            'id_sources' => 'Id Sources',
            'grossarea' => 'Grossarea',
            'images' => 'Images',
            'url' => 'Url',
            'status_unique_phone' => 'Status Unique Phone',
            'status_unique_advert' => 'Status Unique Advert',
            'status_unique_date' => 'Status Unique Date',
            'status_blacklist2' => 'Status Blacklist2'
        ];
    }

    /**
     * @inheritdoc
     * @return saleQuery the active query used by sale AR class.
     */
    public static function find()
    {
        return new SaleQuery(get_called_class());
    }

// метод парсинга всех данных
    public function ParsingSaleHistory($adv)
    {

        // парсим время
        $this->date_start = parsing_date($adv->time);

        // если пришел заголовок то
        if (isset($adv->title) && $adv->title != '') {

            // парсим этаж этажность
            if (empty($adv->param_2415)) {
                $this->floorcount = parsing_floorcounts($adv->title);
            } else {
                $this->floorcount = $adv->param_2415;
            }
            // парсим этаж
            if (empty($adv->param_2315)) {
                $this->floor = parsing_floors($adv->title);
            } else {
                $this->floor = $adv->param_2315;
            }

            // Получаем площадь
            $this->grossarea = parsing_grossarea($adv->title);

            if ($adv->cat2 == 'Комнаты') {
                $this->rooms_count = 30;
                echo $this->rooms_count . "добавили" . $adv->title;
            } else {
                if (($adv->param_1945 != 'Студия')) {
                    $this->rooms_count = $adv->param_1945;
                    echo $this->rooms_count . "добавили" . $adv->title;
                } else {
                    $this->rooms_count = 20;
                    echo $this->rooms_count . "добавили" . $adv->title;
                }


                // $this->rooms_count = rooms_count($adv->title);
            }
            if ($this->rooms_count == 0) $this->rooms_count = rooms_count($adv->title);

        };
        // парсим id ресурса
        $this->id_sources = parsing_id_resourse($adv->source);
        // парсинг телефон
        $this->phone1 = parsing_phone($adv->phone);
        // парсим тип дома
        $this->house_type = parsing_house_type($adv->param_2009);

        $this->title = $adv->title;
        $this->description = $adv->description;
        // парсинг улицы
        $this->address = $adv->address;

        $this->id = $adv->id;
        $this->price = $adv->price;

        // вставляем фотки
        $images = '';
        foreach ($adv->images as $key => $value) {
            $images = $images . "X" . $value->imgurl;
        }
        $this->images = $images;

        $this->city = $adv->city;
        $this->person = $adv->person;
        $coords_array = $adv->coords;

        $this->coords_x = round($coords_array->lat, 5);
        $this->coords_y = round($coords_array->lng, 5);
        /* echo "<tr>";
         echo "<td>{$adv->id}</td>";
         echo "<td><a href=" . $adv->url . ">{$adv->title}</a></td>";
         echo "<td>{$adv->param_2009}</td>";
         echo "</tr>";

         echo "<tr>";
         echo "<td>{$this->id}</td>";
         echo "<td>{$this->title}</td>";
         echo "<td>{$this->house_type}</td>";
         echo "</tr>";*/
        if ($this->date_start > (time() - 30 * 24 * 60 * 60)) $is_time_for_sale = true;
        //одновременно создаем объакт sale и проподим с ним все процедуры
        if ($is_time_for_sale) {
            $sale = new Sale();
            $sale->setAttributes($this->getAttributes());
            $is_dublicate = $sale->export_from_irr_dublicates_if_exists();


        }
        // источник url
        $this->url = $adv->url;
        $this->id_in_source = ParsingExtractionMethods::getOriginalIdFromUrl($this->url);
        // делаем проверку на дубликат ирр
        if (!$this->export_from_irr_dublicates_if_exists()) {
        };


        if ($is_time_for_sale) {
            $sale->setAttributes($this->getAttributes());
            echo " <br> adding <b>sale</b>";

            // подгружаем статистику
            // $sale->load_statistic();

        }

        // вставляем данные в таблицу
        if ($adv->nedvigimost_type == 'Продам') {
            // сохраняем можель sale
            if ($is_time_for_sale) {
                if (!$sale->save()) {

                    // если данное объявление эже есть то просто обновляем параметры
                    $saved_sale = Sale::find()->where(['id' => $adv->id])->one();
                    if ($saved_sale) {
                        $saved_sale->date_start = $this->date_start;
                        $saved_sale->rooms_count = $this->rooms_count;
                        $saved_sale->price = $this->price;
                        $saved_sale->disactive = 3;
                        $saved_sale->description = $this->description;
                       if ($this->id_sources == 3) $saved_sale->date_of_check = $this->date_start;
                        if ($saved_sale->save()) return "update";
                        else {
                            return "error";
                            print_r($saved_sale->getErrors());
                        }
                    }

                }
            }

            // сохраняем модель salehistory
            if ($this->save()) {

                echo " <br> adding <b>salehistory</b>";
                return "save";
            } else {

                // если данное объявление эже есть то просто обновляем параметры
                $saved_sale = SaleHistory::find()->where(['id' => $adv->id])->one();
                if ($saved_sale) {
                    $saved_sale->date_start = $this->date_start;
                    $saved_sale->rooms_count = $this->rooms_count;
                    $saved_sale->price = $this->price;
                    $saved_sale->description = $this->description;
                    if ($saved_sale->save()) return "update";
                    else {
                        return "error";
                        print_r($saved_sale->getErrors());
                    }
                }

            }


        }


    }

    // метод парсинга всех данных
    public function ParsingSaleHistoryDaily($adv, $is_room = false)
    {

        // парсим время
        $this->date_start = parsing_date($adv->time);

        // если пришел заголовок то
        if (isset($adv->title) && $adv->title != '') {

            // парсим этаж этажность
            if (empty($adv->param_2415)) {
                $this->floorcount = parsing_floorcounts($adv->title);
            } else {
                $this->floorcount = $adv->param_2415;
            }
            // парсим этаж
            if (empty($adv->param_2315)) {
                $this->floor = parsing_floors($adv->title);
            } else {
                $this->floor = $adv->param_2315;
            }

            // Получаем площадь
            $this->grossarea = parsing_grossarea($adv->title);

            if ($is_room) $this->rooms_count = 30;
            else {
                if (($adv->param_1945 != 'Студия')) {
                    $this->rooms_count = $adv->param_1945;
                } else $this->rooms_count = 20;


                // $this->rooms_count = rooms_count($adv->title);
            }

        };
        // парсим id ресурса
        $this->id_sources = parsing_id_resourse($adv->source);
        // парсинг телефон
        $this->phone1 = parsing_phone($adv->phone);
        // парсим тип дома
        $this->house_type = parsing_house_type($adv->param_2009);

        $this->title = $adv->title;
        $this->description = $adv->description;
        // парсинг улицы
        $this->address = $adv->address;

        $this->id = $adv->id;
        $this->price = $adv->price;

        // вставляем фотки
        $images = '';
        foreach ($adv->images as $key => $value) {
            $images = $images . "X" . $value->imgurl;
        }
        $this->images = $images;

        $this->city = $adv->city;
        $this->person = $adv->person;
        $coords_array = $adv->coords;

        $this->coords_x = round($coords_array->lat, 5);
        $this->coords_y = round($coords_array->lng, 5);


        //одновременно создаем объакт sale и проподим с ним все процедуры
        $sale = new Sale();
        $sale->setAttributes($this->getAttributes());
        $is_dublicate = $sale->export_from_irr_dublicates_if_exists();


        // источник url
        $this->url = $adv->url;
        // делаем проверку на дубликат ирр
        if (!$this->export_from_irr_dublicates_if_exists()) {
            if ($this->grossarea == 0) {

                // если нет то пытаемся рассчитать из среднего
                // $this->try_to_find_same_squre_average();
                if ($this->grossarea == 0) {
                    // пробуем поискать площадь в описании
                    $this->try_to_find_grossarea_in_description();

                }
                if ($this->grossarea == 0) {

                    // если нет сделать запрос phpQuery
                    $this->phpQueryParsingGroosarea();

                }
            }
            // проводим геокодирование
            // сначала пробуем собственный метод


            if (!$this->TryToGeolocateUsingOwnEngine()) {
                echo "<br>- yandex maps api -";
                $this->geolocate_without_saving();

            }
            if ($this->broken != 1) {
                //  $this->create_or_update_statistic();
                $this->create_or_update_statistic_address_daily();
                $this->create_or_update_statistic_same_address_daily();
            }


        };


        $sale->setAttributes($this->getAttributes());
        echo " <br> adding <b>sale</b>";

        // подгружаем статистику
        $sale->load_statistic();


        // вставляем данные в таблицу
        if ($adv->nedvigimost_type == 'Продам') {
            // сохраняем можель sale

            if (!$sale->save()) {

                // если данное объявление эже есть то просто обновляем параметры
                $saved_sale = Sale::find()->where(['id' => $adv->id])->one();
                if ($saved_sale) {
                    $saved_sale->date_start = $this->date_start;
                    $saved_sale->rooms_count = $this->rooms_count;
                    $saved_sale->price = $this->price;
                    $saved_sale->description = $this->description;
                    if ($saved_sale->save()) return "update";
                    else {
                        return "error";
                        print_r($saved_sale->getErrors());
                    }
                }

            } else return "save";


            // сохраняем модель salehistory
            if ($this->save()) {
                return "save";
            } else {

                // если данное объявление эже есть то просто обновляем параметры
                $saved_sale = SaleHistory::find()->where(['id' => $adv->id])->one();
                if ($saved_sale) {
                    // $saved_sale->date_start = $this->date_start;
                    $saved_sale->rooms_count = $this->rooms_count;
                    $saved_sale->price = $this->price;
                    $saved_sale->description = $this->description;
                    if ($saved_sale->save()) return "update";
                    else {
                        return "error";
                        print_r($saved_sale->getErrors());
                    }
                }

            }


        }


    }


    public function geolocate($prefix)
    {
        Addresses::setTablePrefix($prefix);
        // получаем координаты и точность ввиде массива
        $array_coords = $this->get_coords_and_precision();
        $precision = $array_coords[2];
        {
            // если координаты пришли, а также это новый адрес то пытаемся создать новый адрес
            if (($array_coords) and ($this->is_new_address($array_coords))) {
                echo '    Такого адреса еще нет в базе, заносим';
                $this->create_new_address($array_coords);
            }
            // ехпортируем из таблицы addresses id address а также подтягиваем параметры house type floorcount если они отсутсвтвуют
            $this->export_from_addresses($array_coords);

            // поиск данного объекта в базе sale
            $sale = Sale::findOne($this->id);
            if ($sale) {
                $sale->locality = $this->locality;
                $sale->status_unique_advert = 1;
                $sale->id_address = $this->id_address;
                $sale->save();
                echo "Одновременно продекодировали sale";
            }
            if ($array_coords) {
                echo "Геокодирование удалось: " . $this->id;
                return true;
            }


        }
    }

    public function geolocate_without_saving()
    {

        // получаем координаты и точность ввиде массива
        $array_coords = $this->get_coords_and_precision();
        $precision = $array_coords[2];
        {
            // если координаты пришли, а также это новый адрес то пытаемся создать новый адрес
            if (($array_coords) and ($this->is_new_address($array_coords))) {
                echo '  new address';
                $this->create_new_address($array_coords);
            }
            // ехпортируем из таблицы addresses id address а также подтягиваем параметры house type floorcount если они отсутсвтвуют
            $this->export_from_addresses_withoutsaving($array_coords);

        }
    }


    public
    function is_full()
    {
        if (($this->id_address != 0) and ($this->grossarea != 0)
            and ($this->rooms_count != 0) and ($this->house_type != 0) and ($this->floorcount != 0)
        ) return true;
    }

    public
    function is_new_address($array_coords)
    {
        $is_new_address = !Addresses::find()
            ->where(['coords_x' => $array_coords[0]])
            ->andwhere(['coords_y' => $array_coords[1]])
            ->exists();

        return $is_new_address;


    }

    public
    function get_coords_and_precision()
    {

        $fulladdress = $this->city . "," . $this->address;
        // Обращение к http-геокодеру
        $xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($fulladdress) . '&results=1';
        // echo $xml_string;
        $xml = simplexml_load_file($xml_string);
        // Если геокодировать удалось, то записываем в БД
        $found = $xml->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found;
        // echo "found = " . $found;
        if ($found != 0) {

            $precision = $xml->GeoObjectCollection->featureMember->GeoObject->metaDataProperty->GeocoderMetaData->precision->__ToString();
            if ($precision == 'number') {
                if ($this->id_sources == 1) $this->try_to_detect_address_when_precision_number();
            } else {
                $coords = str_replace(' ', ',', $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);
                echo "<br>";
                $result = "id=" . $this->id . "-" . $this->address . " - coords='" . $coords . "' - precision=" . $precision;
                echo $result;
                return explode(",", $coords . "," . $precision);
            }

        } else return false;
    }

    public
    function get_coords_and_precision_new()
    {
        $fulladdress = $this->city . "," . $this->address;
        $xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($fulladdress) . '&results=2';

        $file = file_get_contents($xml_string);
        $pq = phpQuery::newDocument($file);

        $precision = $pq->find('precision')->text();
        $coords = str_replace(' ', ',', $pq->find('pos')->text());

        // получаем координаты
        $coords_array = explode(",", $coords);
        $found_locality = $pq->find('LocalityName')->text();

        $found_district = $pq->find('DependentLocalityName')->text();
        $found_ThoroughfareName = $pq->find('ThoroughfareName')->text();
        $found_PremiseNumber = $pq->find('PremiseNumber')->text();
        if (preg_match_all('/\d+/', $found_PremiseNumber, $numbers)) {
            // приходит ответ номера дома
            $house = $numbers[0][0];
            // приходит ответ номера корпуса
            $hull = $numbers[0][1];
            // if ($house != 0) echo "house =  " . $house;
            // if (!empty($hull)) echo ", hull =  " . $hull;
        }

// если точность определения адреса пришла Number пытаемся более точно определить адрес по дполнительным параметрам
        if (($precision == 'number') and ($this->floorcount != 0) and ($this->house_type != 0)) {
            echo "<br><a href='" . $xml_string . "'> link</a>";
            echo $found_ThoroughfareName;
            echo $found_PremiseNumber;
            $addresses = Addresses::find()
                ->where(['street' => $found_ThoroughfareName])
                ->andwhere(['house' => $house])
                ->andwhere(['house_type' => $this->house_type])
                ->andwhere(['floorcount' => $this->floorcount])
                ->all();
            if (count($addresses) == 1) {
                echo " нашли один аддрес";
                $address = Addresses::find()
                    ->where(['street' => $found_ThoroughfareName])
                    ->andwhere(['house' => $house])
                    ->andwhere(['house_type' => $this->house_type])
                    ->andwhere(['floorcount' => $this->floorcount])
                    ->one();
                $this->id_address = $address->id;


            } else if (count($addresses) > 1) {

                foreach ($addresses as $address) {
                    if (!$address->hull) {
                        $pattern = "/" . $address->street . ".+" . $address->house . ".+" . $address->hull . "/";
                        if (preg_match($pattern, $this->description, $output)) {
                            echo " удалось найти <b>" . $pattern . "адреc в описании ";
                            echo $this->description;
                            echo
                            $this->id_address = $address->id;
                        }
                    }

                }

            } else echo "шляпа какая то";


        }
        $found_name = $found_ThoroughfareName . ", " . $found_PremiseNumber;

        $trimmed_street = trim(preg_replace("/(улица|проезд|проспект|бульвар)/", "", $found_ThoroughfareName));


    }


    public function TryToGeolocateUsingOwnEngine_old()
    {
        global $city;
        $all_available_streets = Addresses::find()
            ->select('street')
            ->distinct('street')
            ->where(['<>', 'street', ''])
            ->andwhere(['<>', 'street', '-'])
            // ->andwhere(['locality' => $city])
            ->all();

        // echo "<br> all_available_streets:";
        // echo implode(",", ArrayHelper::getColumn($all_available_streets, ['street']));
        // пробаемся по всем доступным улицам в данном городе
        foreach ($all_available_streets as $street) {
            // если улица состояит из нескольких слов то вытаемся определить
            $trimmed_street = trim(preg_replace("/\d|,|\./", "", $street->street));

            // если в названии найдется обрезанное название улицы то ведем поиск номера и корпуса
            $message = '';
            $numbers = [];
            $success = false;


            if (strpos(mb_strtolower($this->address), mb_strtolower(mb_substr($trimmed_street, 1)))) {
                // echo " searching <i>" .$trimmed_street."</i>";
                $success = true;
                // иногда в названии есть цифры так вот о них надо знать заранее например улица 20 января
                // ищем эти цифры
                if (preg_match_all('/\d+/', $street->street, $number_in_name_street)) {
                    $array_of_number_in_name_street = $number_in_name_street[0];
                    //  echo " numbers in the name street";
                    var_dump($array_of_number_in_name_street);
                }
                // ищем вообще все номера в имени удаляем номера в имени и получаем номер дома и корпус
                if (preg_match_all('/\d+/', $this->address, $numbers)) {
                    $numbers = $numbers[0];
                    // если есть номера в имени то удаляем их из всех номеров
                    if (!empty($array_of_number_in_name_street)) {
                        $old_numbers = array_diff($numbers, $array_of_number_in_name_street);
                        $numbers = [];
                        // делаем переиндексацию массива
                        foreach ($old_numbers as $number) {
                            $numbers[] = $number;
                        }

                    }
                    $house = $numbers[0];
                    $hull = $numbers[1];
                    if ($house != 0) echo "house =  " . $house;
                    if ($hull != 0) echo ", hull =  " . $hull;

                    // попытаемся поискать литеру в номере дома вырезав из адреса название улицы по номер дома
                    if (preg_match_all('/А|Б|В|Г|Д|Е|Е|Ж|З|И|а|б|в|г|д|е|ж|з|и/', strstr($this->address, strpos($this->address, $house)), $numbers)) {
                        // приходит ответ номера корпуса
                        $hull = $numbers[0][0];
                        echo ", litera =  " . $hull;

                    }

                } // else echo "номеров в адресе не найдено вообще";

                // когда мы получили улицу номер дома и корпус ищем эти данные в таблице
                $seaching_address = Addresses::find()
                    ->where(['street' => $street->street])
                    ->andWhere(['house' => $house])
                    ->andfilterWhere(['hull' => $hull])
                    ->one();

                if ($seaching_address) {
                    // echo "<b> id=" . $seaching_address->id . "</b>";
                    // делаем экспорт данных
                    echo "<b> <i>" . $this->address . " </i> id=" . $seaching_address->id . "</b>";


                    // если что то пришло то переносим правильный адрес и id_address
                    if ($seaching_address) {
                        $this->address = $seaching_address->address;
                        $this->id_address = $seaching_address->id;
                        $this->locality = $seaching_address->locality;
                        // если пропущены какие то из параметров взаимный обмен параметрами про пропуске
                        if (($seaching_address->house_type == 0) or ($seaching_address->floorcount == 0)) {
                            // если попаким то причинам в таблице адресов пропущен параметр house type
                            if (($seaching_address->house_type == 0) and ($this->house_type != 0)) {
                                echo "<b>!!!!!!! !удалось прописать house type из других параметров</b>";
                                $seaching_address->house_type = $this->house_type;
                            }
                            // если попаким то причинам в таблице адресов пропущен параметр floorcount
                            if (($seaching_address->floorcount == 0) and ($this->floorcount != 0)) {
                                echo "<b>!!!!!!!!!!!!!!удалось прописать floorcount из других параметров</b>";
                                $seaching_address->floorcount = $this->floorcount;
                            }
                        }
                    }
                    // если каките то параметры пришли то сохраняем address
                    if ($seaching_address) {
                        if (!$seaching_address->save()) var_dump($seaching_address->errors);

                    }

                    // переносим из модели адрес если параметры hose_type или floorcount пришли нулевые
                    if (($this->house_type == 0) and ($seaching_address->house_type != 0)) {
                        $this->house_type = $seaching_address->house_type;
                    }

                    if (($this->floorcount == 0) and ($seaching_address->floorcount != 0)) {
                        $this->floorcount = $seaching_address->floorcount;
                    }

                    return true;

                } else echo " <br> address is not found";
                continue;
            }


        }
    }

    public function TryToGeolocateUsingOwnEngine()
    {


        $all_available_streets = Addresses::find()
            ->select('street')
            ->distinct('street')
            ->where(['<>', 'street', ''])
            ->andwhere(['<>', 'street', '-'])
            // ->andwhere(['locality' => $city])
            ->all();

        $n = 0;

        foreach ($all_available_streets as $street) {
            $this->address = trim(preg_replace("/(улица|проезд|проспект|бульвар|ул|просп|переулок)/", "", $this->address));
            //echo "<br>пытаемся найти улицу" . $street->street . "<br>";

            // если улица состояит из нескольких слов то вытаемся вытащить сначала последнее
            $pattern = '/\s|-/';
            // echo " улица без номеров" . trim(preg_replace("/\d|,|\./", "", $street->street));
            $matches = preg_split($pattern, preg_replace("/\d|,|\./", "", $street->street));
            // var_dump(array_reverse($matches));
            $new_arr = array_diff($matches, array('', NULL, false));
            //echo "<pre>";
            // var_dump(array_reverse($new_arr));
            //echo "<pre>";
            foreach (array_reverse($new_arr) as $match) {

                $trimmed_street = mb_strtolower(trim(preg_replace("/\d|,|\./", "", $match)));
                // echo "<br>ищем <i>" . $trimmed_street . "</i> в <b>". mb_strtolower($this->address)."</b>";
                // если в названии найдется обрезанное название улицы то ведем поиск номера и корпуса

                if (strpos(mb_strtolower($this->address), $trimmed_street) !== false) {
                    //  echo " найдено совпадение " . $trimmed_street;


                    // иногда в названии есть цифры так вот о них надо знать заранее например улица 20 января
                    // ищем эти цифры
                    if (preg_match_all('/\d+/', $street->street, $number_in_name_street)) {
                        $array_of_number_in_name_street = $number_in_name_street[0];
                        // echo "<br> numbers in the name street";
                        //var_dump($array_of_number_in_name_street);
                    }
                    // ищем вообще все номера в имени удаляем номера в имени и получаем номер дома и корпус
                    if (preg_match_all('/\d+/', $this->address, $numbers)) {
                        $numbers = $numbers[0];
                        // если есть номера в имени то удаляем их из всех номеров
                        if (!empty($array_of_number_in_name_street)) {
                            $old_numbers = array_diff($numbers, $array_of_number_in_name_street);
                            $numbers = [];
                            // делаем переиндексацию массива
                            foreach ($old_numbers as $number) {
                                $numbers[] = $number;
                            }

                        }
                        $house = $numbers[0];
                        $hull = $numbers[1];
                        //  if ($house != 0) echo "house =  " . $house;
                        //  if ($hull != 0) echo ", hull =  " . $hull;

                        // попытаемся поискать литеру в номере дома вырезав из адреса название улицы по номер дома
                        if (preg_match_all('/А|Б|В|Г|Д|Е|Е|Ж|З|И|а|б|в|г|д|е|ж|з|и/', strstr($this->address, strpos($this->address, $house)), $numbers)) {
                            // приходит ответ номера корпуса
                            $hull = $numbers[0][0];
                            echo ", litera =  " . $hull;

                        }

                    } //else echo "номеров в адресе не найдено вообще";

                    // когда мы получили улицу номер дома и корпус ищем эти данные в таблице
                    $seaching_address_count = Addresses::find()
                        ->where(['street' => $street->street])
                        ->andWhere(['house' => $house])
                        ->andWhere(['hull' => $hull])
                        ->count();

                    if ($seaching_address_count == 1) {
                        $seaching_address = Addresses::find()
                            ->where(['street' => $street->street])
                            ->andWhere(['house' => $house])
                            ->andfilterWhere(['hull' => $hull])
                            ->one();
                        $n++;
                        //echo "<b> id=" . $seaching_address->id . "</b>";
                        $success = true;
                    } elseif ($seaching_address_count == 0) {

                        // echo "<h1> совпадений не найдено </h1>";
                        continue;
                    } else {
                        //echo "<h1> два или больше совпадений </h1>";
                        continue;
                    }


                }


            }

        }


        if ($n == 1) {
            echo "<br>- my own -";
            // если что то пришло то переносим правильный адрес и id_address
            echo "<br>" . $this->address . " address id=" . $seaching_address->id;
            $this->address = $seaching_address->address;
            $this->id_address = $seaching_address->id;
            $this->locality = $seaching_address->locality;
            // если пропущены какие то из параметров взаимный обмен параметрами про пропуске
            if (($seaching_address->house_type == 0) or ($seaching_address->floorcount == 0)) {
                // если попаким то причинам в таблице адресов пропущен параметр house type
                if (($seaching_address->house_type == 0) and ($this->house_type != 0)) {
                    //  echo "<b>!!!!!!! !удалось прописать house type из других параметров</b>";
                    $seaching_address->house_type = $this->house_type;
                }
                // если попаким то причинам в таблице адресов пропущен параметр floorcount
                if (($seaching_address->floorcount == 0) and ($this->floorcount != 0)) {
                    //  echo "<b>!!!!!!!!!!!!!!удалось прописать floorcount из других параметров</b>";
                    $seaching_address->floorcount = $this->floorcount;
                }
                // если каките то параметры пришли то сохраняем address
                if ($seaching_address) {
                    if (!$seaching_address->save()) var_dump($seaching_address->errors);

                }
            }


            // переносим из модели адрес если параметры hose_type или floorcount пришли нулевые
            if (($this->house_type == 0) and ($seaching_address->house_type != 0)) {
                $this->house_type = $seaching_address->house_type;
            }

            if (($this->floorcount == 0) and ($seaching_address->floorcount != 0)) {
                $this->floorcount = $seaching_address->floorcount;
            }
            if ($seaching_address->year != 0) {
                $this->year = $seaching_address->year;
            }

            return true;

        } else return false;
    }

    public
    function back_xml_request($array_coords)
    {

        $coords = $array_coords[0] . "," . $array_coords[1];
        $xml_string_back = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($coords) . '&results=1';
        $file = file_get_contents($xml_string_back);
        $pq = phpQuery::newDocument($file);

        $precision = $pq->find('precision')->text();
        $coords = str_replace(' ', ',', $pq->find('pos')->text());

        // получаем координаты
        $coords_array = explode(",", $coords);
        $found_locality = $pq->find('LocalityName')->text();

        $found_district = $pq->find('DependentLocalityName')->text();
        $found_ThoroughfareName = $pq->find('ThoroughfareName')->text();
        $found_PremiseNumber = $pq->find('PremiseNumber')->text();


        $found_name = $found_ThoroughfareName . ", " . $found_PremiseNumber;

        $trimmed_street = trim(preg_replace("/(улица|проезд|проспект|бульвар)/", "", $found_ThoroughfareName));

        echo "street '<i>" . $trimmed_street . "</i>'";


        echo " N " . $found_PremiseNumber . "";

        $success = true;
        $numbers = [];
        // ищем номера в $found_PremiseNumber получаем номер дома и корпус
        if (preg_match_all('/\d+/', $found_PremiseNumber, $numbers)) {
            // приходит ответ номера дома
            $house = $numbers[0][0];
            // приходит ответ номера корпуса
            $hull = $numbers[0][1];
            if ($house != 0) echo "house =  " . $house;
            if (!empty($hull)) echo ", hull =  " . $hull;
        }
        // попытаемся поискать литеру в номере дома
        if (preg_match_all('/А|Б|В|Г|Д|Е|Е|Ж|З|И|а|б|в|г|д|е|ж|з|и/', $found_PremiseNumber, $numbers)) {
            // приходит ответ номера корпуса
            $hull = $numbers[0][0];
            echo ", litera =  " . $hull;

        }


        if ($found_name == '') $found_back = 'пусто';
        return [
            'found_name' => $found_name,
            'found_house' => $house,
            'found_hull' => $hull,
            'found_street' => $trimmed_street,
            'found_district' => $found_district,
            'found_locality' => $found_locality
        ];

    }

    public
    function create_new_address($array_coords)
    {
        $precision = $array_coords[2];
        $new_address = new Addresses();
        $otvet = $this->back_xml_request($array_coords);
        var_dump($otvet);
        $new_address->house_type = $this->house_type;
        $new_address->coords_x = $array_coords[0];
        $new_address->coords_y = $array_coords[1];
        $new_address->floorcount = $this->floorcount;
        $new_address->locality = $otvet['found_locality'];
        $new_address->district = $otvet['found_district'];
        $new_address->house = $otvet['found_house'];
        $new_address->hull = $otvet['found_hull'];
        $new_address->street = $otvet['found_street'];

        if ($precision == 'exact') $new_address->address = $otvet['found_name']; // если адрес точный то записываем имя адреса из яндекса иначе тот что пришел
        if ($precision == 'number') {
            $new_address->address = $otvet['found_name'];
            $new_address->address_string_variants = $this->address;
        } // если адрес точный то записываем имя адреса из яндекса иначе тот что пришел
        if (($precision != 'exact') and ($precision != 'number')) $new_address->address = $this->address;
        $new_address->year = 0;
        $new_address->precision_yandex = $precision;
// еслиданные провалидировались то заносим их в базу
        if ($new_address->validate()) {
            if ($new_address->save()) {
            };
        } else {
            var_dump($new_address->errors);
        }
    }

    public
    function export_from_addresses($array_coords)
    {
        $precisions = ['exact', 'number', 'street', 'near'];

        // находим из таблицы адресов те которые соотвутствуют заданным координатам и точности
        $address = Addresses::find()
            ->where(['coords_x' => $array_coords[0]])
            ->andwhere(['coords_y' => $array_coords[1]])
            ->andWhere(['in', 'precision_yandex', $precisions])
            ->one();
        if ($address->id == 0) var_dump($address);
        echo " существующий id_address = " . $address->id;
        // если что то пришло то переносим правильный адрес и id_address
        if ($address) {
            $this->address = $address->address;
            $this->id_address = $address->id;
            $this->locality = $address->locality;
            $this->year = $address->year;
            // если пропущены какие то из параметров взаимный обмен параметрами про пропуске
            if (($address->house_type == 0) or ($address->floorcount == 0)) {
                // если попаким то причинам в таблице адресов пропущен параметр house type
                if (($address->house_type == 0) and ($this->house_type != 0)) {
                    echo "!!!!!!! !удалось прописать house type из других параметров";
                    $address->house_type = $this->house_type;
                }
                // если попаким то причинам в таблице адресов пропущен параметр floorcount
                if (($address->floorcount == 0) and ($this->floorcount != 0)) {
                    echo "!!!!!!!!!!!!!!удалось прописать floorcount из других параметров";
                    $address->floorcount = $this->floorcount;
                }
            }
        }
// если каките то параметры пришли то сохраняем address
        if ($address) {
            if ($address->save()) {
            } else {
                var_dump($address->errors);
            }
        }


        $this->status_unique_advert = 1;

        // переносим из модели аддрес если параметры hose_type или floorcount пришли нулевые
        if (($this->house_type == 0) and ($address->house_type != 0)) {
            $this->house_type = $address->house_type;
        }

        if (($this->floorcount == 0) and ($address->floorcount != 0)) {
            $this->floorcount = $address->floorcount;
        }

        if ($this->validate()) {
            $this->save();
            $message = " адрес  базе sale успешно обновлен";
        } else
            var_dump($this->errors);
    }

    public
    function export_from_addresses_withoutsaving($array_coords)
    {
        $precisions = ['exact', 'number', 'street', 'near'];

        // находим из таблицы адресов те которые соотвутствуют заданным координатам и точности
        $address = Addresses::find()
            ->where(['coords_x' => $array_coords[0]])
            ->andwhere(['coords_y' => $array_coords[1]])
            ->andWhere(['in', 'precision_yandex', $precisions])
            ->one();
        if ($address->id == 0) var_dump($address);
        echo " id_address = " . $address->id;


        // если что то пришло то переносим правильный адрес и id_address
        if ($address) {
            $this->address = $address->address;
            $this->id_address = $address->id;
            $this->locality = $address->locality;
            $this->year = $address->year;
            // если пропущены какие то из параметров взаимный обмен параметрами про пропуске
            if (($address->house_type == 0) or ($address->floorcount == 0)) {
                // если попаким то причинам в таблице адресов пропущен параметр house type
                if (($address->house_type == 0) and ($this->house_type != 0)) {
                    echo "<b>!!!!!!! !удалось прописать house type из других параметров</b>";
                    $address->house_type = $this->house_type;
                }
                // если попаким то причинам в таблице адресов пропущен параметр floorcount
                if (($address->floorcount == 0) and ($this->floorcount != 0)) {
                    echo "<b>!!!!!!!!!!!!!!удалось прописать floorcount из других параметров</b>";
                    $address->floorcount = $this->floorcount;
                }
            }
        }
        // если каките то параметры пришли то сохраняем address
        if ($address) {
            if (!$address->save()) var_dump($address->errors);

        } else $this->broken = 1;


        // переносим из модели адрес если параметры hose_type или floorcount пришли нулевые
        if (($this->house_type == 0) and ($address->house_type != 0)) {
            $this->house_type = $address->house_type;
        }

        if (($this->floorcount == 0) and ($address->floorcount != 0)) {
            $this->floorcount = $address->floorcount;
        }
        if ($address->year != 0) {
            $this->year = $address->year;
        }
        // если геокодирование не удалось с достаточной точностью (exact or number) то ставим статус broken
        //$this->set_broken_geocodate();
    }

    public
    function fill_the_missing_paramerts()
    {
        // ищем из истории где данногу id_присвоен параметр
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address->house_type != 0) {
            $this->house_type = $address->house_type;

        }
        if ($address->floorcount != 0) $this->floorcount = $address->floorcount;


    }

    public
    function get_average_price_address()
    {

    }

// проверка на то что такой записи больше нет
    public
    function is_unique_sale_analitics_address($prefix)
    {
        SaleAnaliticsAddress::setTablePrefix($prefix);
        $exist = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        if ($exist == 0) echo " нет такой статистики";
        else echo " is unique stat address = " . $exist;
        return $exist;
    }

    public
    function is_unique_sale_analitics($prefix)
    {
        SaleAnalitics::setTablePrefix($prefix);
        $exist = SaleAnalitics::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['house_type' => $this->house_type])
            ->andwhere(['year' => $this->year()])
            ->andwhere(['locality' => $this->locality])
            ->exists();
        if ($exist == 0) echo " нет такой статистики";
        else echo " is unique stat = " . $exist;
        return $exist;
    }

    public function create_or_update_statistic_address()
    {

        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 10;
        // выделяем этот id
        $updated_sale_analiticts_address = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        echo "<br>";
        // var_dump($updated_sale_analiticts_address);

        if ($updated_sale_analiticts_address) {

            // вычисление всех вариантов на которые данный новый вариант может сказаться
            echo " делаем upgrade <b>SaleAnaliticsAddress</b> методом 2 rooms_count=" . $this->rooms_count . " S=" . $this->grossarea . " price = " . $this->price;

            $updated_sales_analiticts_address = SaleAnaliticsAddress::find()
                ->where(['rooms_count' => $this->rooms_count])
                ->andwhere(['id_address' => $this->id_address])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->all();
            foreach ($updated_sales_analiticts_address as $updated_sale_analiticts_address) {
                echo "<br>";
                echo "old average_price =" . $updated_sale_analiticts_address->average_price . " count = " . $updated_sale_analiticts_address->average_price_count;
                $updated_sale_analiticts_address->average_price = round(($updated_sale_analiticts_address->average_price * $updated_sale_analiticts_address->average_price_count + $this->price)
                    / ($updated_sale_analiticts_address->average_price_count + 1));
                $updated_sale_analiticts_address->average_price_count++;
                echo "S=" . $updated_sale_analiticts_address->grossarea . " new average_price =" . $updated_sale_analiticts_address->average_price . " count = " . $updated_sale_analiticts_address->average_price_count;

                if (!$updated_sale_analiticts_address->save()) var_dump($updated_sale_analiticts_address->errors);
            }
            echo "<br>";


        } else {

            echo "создаем статистику <b>SaleAnaliticsAddress</b> rooms_count=" . $this->rooms_count . " price=" . $this->price . " S=" . $this->grossarea;


            // TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics_address = New SaleAnaliticsAddress();


            $new_sale_analitics_address->rooms_count = $this->rooms_count;
            $new_sale_analitics_address->grossarea = $this->grossarea;
            $new_sale_analitics_address->id_address = $this->id_address;
            $new_sale_analitics_address->floorcount = $this->floorcount;

            //   вычисляем среднюю цену по данному адресу
            $new_sale_analitics_address->average_price = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->average('price'));

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $new_sale_analitics_address->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->count());

            if (!$new_sale_analitics_address->save()) var_dump($new_sale_analitics_address->errors);
            echo "<br>";
        }

    }

    // апгрейд статистики на стартовом режиме
    public function create_or_update_statistic_address_start()
    {

        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 10;
        // выделяем этот id
        $updated_sale_analiticts_address = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        //echo "<br>";
        // var_dump($updated_sale_analiticts_address);

        if (!$updated_sale_analiticts_address) {

            echo "<br>создаем статистику SaleAnaliticsAddress ";


            // TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics_address = New SaleAnaliticsAddress();


            $new_sale_analitics_address->rooms_count = $this->rooms_count;
            $new_sale_analitics_address->grossarea = $this->grossarea;
            $new_sale_analitics_address->id_address = $this->id_address;
            $new_sale_analitics_address->floorcount = $this->floorcount;

            //   вычисляем среднюю цену по данному адресу
            $prices1 = SaleHistory::find()
                ->select('price')
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->asArray()
                ->all();

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $new_sale_analitics_address->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->count());


            $prices = [];
            foreach ($prices1 as $item) {
                array_push($prices, $item['price']);
            }


            $new_prices = $prices;
            if (count($new_prices) != 0) {
                $average = round(array_sum($prices) / count($prices));


                foreach ($prices as $price) {
                    if (($price > $average) or ($price * 1.8 < $average)) {
                        echo "<br>данный вариант выпадает из общего числа" . $price;
                        unset($new_prices[array_search($price, $prices)]);
                        //   echo "-  удалили его";
                    }
                }
                if (count($new_prices) != 0) {

                    $new_sale_analitics_address->average_price = round(array_sum($new_prices) / count($new_prices));
                }
            }


            if (!$new_sale_analitics_address->save()) var_dump($new_sale_analitics_address->errors);
        } else echo "<br>данная статистика SaleAnaliticsAddress уже существует";

    }


    public function create_or_update_statistic_address_daily()
    {

        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 10;
        // выделяем этот id
        $updated_sale_analiticts_address = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        //echo "<br>";
        // var_dump($updated_sale_analiticts_address);

        if (!$updated_sale_analiticts_address) {

            echo "<br>создаем статистику SaleAnaliticsAddress ";


            // TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics_address = New SaleAnaliticsAddress();


            $new_sale_analitics_address->rooms_count = $this->rooms_count;
            $new_sale_analitics_address->grossarea = $this->grossarea;
            $new_sale_analitics_address->id_address = $this->id_address;
            $new_sale_analitics_address->floorcount = $this->floorcount;

            //   вычисляем среднюю цену по данному адресу
            $prices1 = SaleHistory::find()
                ->select('price')
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->asArray()
                ->all();

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $new_sale_analitics_address->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->count());


            $prices = [];
            foreach ($prices1 as $item) {
                array_push($prices, $item['price']);
            }


            $new_prices = $prices;
            if (count($new_prices) != 0) {
                $average = round(array_sum($prices) / count($prices));


                foreach ($prices as $price) {
                    if (($price > $average) or ($price * 1.8 < $average)) {
                        echo "<br>данный вариант выпадает из общего числа" . $price;
                        unset($new_prices[array_search($price, $prices)]);
                        //   echo "-  удалили его";
                    }
                }
                if (count($new_prices) != 0) {

                    $new_sale_analitics_address->average_price = round(array_sum($new_prices) / count($new_prices));
                }
            }


            if (!$new_sale_analitics_address->save()) var_dump($new_sale_analitics_address->errors);
        } else {
            echo "<br>данная статистика SaleAnaliticsAddress уже существует";

            // TO DO не забудь поиграться с  разбросом в процентах
            $update_sale_analitics_address = SaleAnaliticsAddress::find()
                ->where(['rooms_count' => $this->rooms_count])
                ->andwhere(['id_address' => $this->id_address])
                ->andwhere(['grossarea' => $this->grossarea])
                ->one();

            //   вычисляем среднюю цену по данному адресу
            $prices1 = SaleHistory::find()
                ->select('price')
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->asArray()
                ->all();

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $update_sale_analitics_address->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['id_address' => $this->id_address])
                ->count());


            $prices = [];
            foreach ($prices1 as $item) {
                array_push($prices, $item['price']);
            }


            $new_prices = $prices;
            if (count($new_prices) != 0) {
                $average = round(array_sum($prices) / count($prices));


                foreach ($prices as $price) {
                    if (($price > $average) or ($price * 1.8 < $average)) {
                        echo "<br>данный вариант выпадает из общего числа" . $price;
                        unset($new_prices[array_search($price, $prices)]);
                        //   echo "-  удалили его";
                    }
                }
                if (count($new_prices) != 0) {

                    $update_sale_analitics_address->average_price = round(array_sum($new_prices) / count($new_prices));
                }
            }


            if (!$update_sale_analitics_address->save()) var_dump($update_sale_analitics_address->errors);
        }

    }

    public function create_or_update_statistic()
    {

        // TO DO не забудь поиграться с  разбросом в процентах
        // количество месяцев за которые собирать статистику
        $period = 2;
        // процент отклонения площади похожик вариантов
        $persent = 5;
        // выделяем этот id
        $updated_sale_analiticts = SaleAnalitics::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['floorcount' => $this->floorcount])
            ->andwhere(['house_type' => $this->house_type])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['year' => $this->year])
            ->andwhere(['locality' => $this->locality])
            ->exists();

        echo "<br>";
        //  var_dump($updated_sale_analiticts);


        if ($updated_sale_analiticts) {
            // вычисление всех вариантов на которые данный новый вариант может сказаться
            echo " делаем upgrade <b>SaleAnalitics</b> методом 2 rooms_count=" . $this->rooms_count . " S=" . $this->grossarea . " price = " . $this->price;

            $updated_sales_analiticts = SaleAnalitics::find()
                ->where(['rooms_count' => $this->rooms_count])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andfilterwhere(['<=', 'year', ($this->year + 7)])
                ->andfilterwhere(['>=', 'year', ($this->year - 7)])
                ->andwhere(['floorcount' => $this->floorcount])
                ->andwhere(['locality' => $this->locality])
                ->andwhere(['house_type' => $this->house_type])
                ->all();
            echo " count = " . count($updated_sales_analiticts);
            foreach ($updated_sales_analiticts as $updated_sale_analiticts) {
                echo "<br> S=" . $updated_sale_analiticts->grossarea;
                echo "old average_price =" . $updated_sale_analiticts->average_price . " count = " . $updated_sale_analiticts->average_price_count . " S="
                    . $updated_sale_analiticts->grossarea . " ";
                $updated_sale_analiticts->average_price = round(($updated_sale_analiticts->average_price * $updated_sale_analiticts->average_price_count + $this->price)
                    / ($updated_sale_analiticts->average_price_count + 1));
                $updated_sale_analiticts->average_price_count++;
                echo " new average_price =" . $updated_sale_analiticts->average_price . " count = " . $updated_sale_analiticts->average_price_count;

                if (!$updated_sale_analiticts->save()) var_dump($updated_sale_analiticts->errors);
            }
            echo "<br>";
        } else {

            echo "создаем статистику <b>SaleAnalitics</b> rooms_count=" . $this->rooms_count . " price=" . $this->price . " S=" . $this->grossarea;


            // TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics = New SaleAnalitics();

            $period = 12;
            $new_sale_analitics->rooms_count = $this->rooms_count;
            $new_sale_analitics->grossarea = $this->grossarea;
            $new_sale_analitics->house_type = $this->house_type;
            $new_sale_analitics->floorcount = $this->floorcount;
            $new_sale_analitics->year = $this->year;
            $new_sale_analitics->locality = $this->locality;

            //   вычисляем среднюю цену по данному адресу
            $new_sale_analitics->average_price = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
                ->andwhere(['floorcount' => $this->floorcount])
                ->andfilterwhere(['<=', 'year', ($this->year + 7)])
                ->andfilterwhere(['>=', 'year', ($this->year - 7)])
                ->andwhere(['locality' => $this->locality])
                ->andwhere(['house_type' => $this->house_type])
                ->average('price'));

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $new_sale_analitics->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
                ->andwhere(['locality' => $this->locality])
                ->andwhere(['floorcount' => $this->floorcount])
                ->andfilterwhere(['<=', 'year', ($this->year + 7)])
                ->andfilterwhere(['>=', 'year', ($this->year - 7)])
                ->andwhere(['house_type' => $this->house_type])
                ->count());

            if (!$new_sale_analitics->save()) var_dump($new_sale_analitics->errors);

            echo "<br>";
        }
    }

    // апгрейд статистики на стартовом режиме
    public function create_or_update_statistic_start()
    {

        // TO DO не забудь поиграться с  разбросом в процентах
        // количество месяцев за которые собирать статистику
        $period = 2;
        // процент отклонения площади похожик вариантов
        $persent = 5;
        // выделяем этот id
        $updated_sale_analiticts = SaleAnalitics::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['floorcount' => $this->floorcount])
            ->andwhere(['house_type' => $this->house_type])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['year' => $this->year()])
            ->andwhere(['locality' => $this->locality])
            ->exists();

        // echo "<br>";
        // var_dump($updated_sale_analiticts);


        if (!$updated_sale_analiticts) {


            echo "<br>создаем статистику SaleAnalitics";


            // TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics = New SaleAnalitics();

            $period = 12;
            $new_sale_analitics->rooms_count = $this->rooms_count;
            $new_sale_analitics->grossarea = $this->grossarea;
            $new_sale_analitics->house_type = $this->house_type;
            $new_sale_analitics->floorcount = $this->floorcount;
            $new_sale_analitics->year = $this->year;
            $new_sale_analitics->locality = $this->locality;

            //   вычисляем среднюю цену по данному адресу
            $new_sale_analitics->average_price = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
                ->andwhere(['floorcount' => $this->floorcount])
                ->andfilterwhere(['<=', 'year', ($this->year + 7)])
                ->andfilterwhere(['>=', 'year', ($this->year - 7)])
                ->andwhere(['locality' => $this->locality])
                ->andwhere(['house_type' => $this->house_type])
                ->average('price'));

            // вычисляем количество вариантов из которых производилось вычисление средней цены
            $new_sale_analitics->average_price_count = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
                ->andwhere(['locality' => $this->locality])
                ->andwhere(['floorcount' => $this->floorcount])
                ->andfilterwhere(['<=', 'year', ($this->year + 7)])
                ->andfilterwhere(['>=', 'year', ($this->year - 7)])
                ->andwhere(['house_type' => $this->house_type])
                ->count());

            if (!$new_sale_analitics->save()) var_dump($new_sale_analitics->errors);
        } else echo "<br>данная статистика SaleAnalitics уже имеется ";

    }


    // статистика похожих адресов
    public function create_or_update_statistic_same_address_start()
    {
        $address = Addresses::findOne($this->id_address);


        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 5;
        // выделяем этот id
        $updated_sale_analiticts_same_address = SaleAnaliticsSameAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        //  echo "<br>";
        //var_dump($updated_sale_analiticts_same_address);

        if (!$updated_sale_analiticts_same_address) {

            echo "<br>создаем статистику SaleAnaliticsSameAddress";


            $radiuses = [900, 1500, 2000, 3000, 5000];
            $new_sale_analitics_same_address = New SaleAnaliticsSameAddress();
            $new_sale_analitics_same_address->rooms_count = $this->rooms_count;
            $new_sale_analitics_same_address->grossarea = $this->grossarea;
            $new_sale_analitics_same_address->id_address = $this->id_address;

            foreach ($radiuses as $radius) {
// TO DO не забудь поиграться с  разбросом в процентах
                $new_sale_analitics_same_address->radius = $radius;
                // $new_sale_analitics_same_address->floorcount = $this->floorcount;
                $id_addresses_400 = $address->getNearestSameIdAddresses($radius);
                if (!empty($id_addresses_400)) {
                    $new_sale_analitics_same_address->average_price_400_count = round(SaleHistory::find()
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->count());


                    $prices1 = SaleHistory::find()
                        ->select('price')
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->asArray()
                        ->all();
                    $prices = [];
                    foreach ($prices1 as $item) {
                        array_push($prices, $item['price']);
                    }


                    $new_prices = $prices;
                    if (count($new_prices) != 0) {
                        $average = round(array_sum($prices) / count($prices));


                        foreach ($prices as $price) {
                            if (($price > $average) or ($price * 1.8 < $average)) {
                                echo "<br>данный вариант выпадает из общего числа" . $price;
                                unset($new_prices[array_search($price, $prices)]);
                                //   echo "-  удалили его";
                            }
                        }
                        if (count($new_prices) != 0) {

                            $new_sale_analitics_same_address->average_price_400 = round(array_sum($new_prices) / count($new_prices));
                        }
                    }


                } else break;
                // вычисляем количество вариантов из которых производилось вычисление средней цены


                echo "<br> количенство объектов" . $new_sale_analitics_same_address->average_price_400_count . " при радиусе" . $radius;

                if ($new_sale_analitics_same_address->average_price_400_count > 10) break;
            }
            if (!empty($id_addresses_400)) $new_sale_analitics_same_address->average_price_400 = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['in', 'id_address', $id_addresses_400])
                ->average('price'));

            if (!$new_sale_analitics_same_address->save()) var_dump($new_sale_analitics_same_address->errors);
        } else echo "<br>данная статистика SaleAnaliticsSameAddress уже существует";


    }

    public function create_or_update_statistic_same_address_test($message)
    {
        $address = Addresses::findOne($this->id_address);


        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 5;


//            echo "<br>создаем статистику SaleAnaliticsSameAddress";


        $radiuses = [300, 900, 1500, 2000, 3000, 5000];
        $new_sale_analitics_same_address = New SaleAnaliticsSameAddress();
        $new_sale_analitics_same_address->rooms_count = $this->rooms_count;
        $new_sale_analitics_same_address->grossarea = $this->grossarea;
        $new_sale_analitics_same_address->id_address = $this->id_address;

        foreach ($radiuses as $radius) {
// TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics_same_address->radius = $radius;
            // $new_sale_analitics_same_address->floorcount = $this->floorcount;
            $id_addresses_400 = $address->getNearestSameIdAddresses($radius);
            if (!empty($id_addresses_400)) {
                $new_sale_analitics_same_address->average_price_400_count = SaleHistory::find()
                    ->filterWhere(['rooms_count' => $this->rooms_count])
                    ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                    ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                    ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                    ->andWhere(['in', 'id_address', $id_addresses_400])
                    ->count();


                $prices1 = SaleHistory::find()
                    ->select('price')
                    ->filterWhere(['rooms_count' => $this->rooms_count])
                    ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                    ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                    ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                    ->andWhere(['in', 'id_address', $id_addresses_400])
                    ->asArray()
                    ->all();
                $prices = [];
                // формируем массив с ценами
                foreach ($prices1 as $item) {
                    array_push($prices, $item['price']);
                }


                $new_prices = $prices;
                if (count($new_prices) != 0) {
                    $average = round(array_sum($prices) / count($prices));
                    $simple_average_price = $average;


                    foreach ($prices as $price) {
                        if (($price > $average) or ($price * 1.8 < $average)) {
//                                echo " - " . $price;
                            unset($new_prices[array_search($price, $prices)]);
                            //   echo "-  удалили его";
                        }
                    }

                    asort($new_prices);
                    $count = count($new_prices);
//                        echo "<br>" . implode(",", $new_prices);
//                        echo "<br> кол-во элементов в массиве:".$count;
//                        echo "<br> брем только 30 % цен нижних цена в новом массиве".round(0.4*$count);
                    $new_prices_sliced = array_slice($new_prices, 0, round(0.4 * $count));
//                        echo "<br> новый массив после отрезки бесполезных вариантов : " . implode(",", $new_prices);
                    if (count($new_prices_sliced) != 0) $average = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
//                        echo "<br>новое cреднее число равно" . $average;
                    $sliced_average_price = $new_prices[round(0.4 * $count)];


                    if (count($new_prices_sliced) != 0) {
                        $sales_history = SaleHistory::find()
                            ->filterWhere(['rooms_count' => $this->rooms_count])
                            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                            ->andWhere(['in', 'id_address', $id_addresses_400])
                            ->andWhere(['<', 'price', $average])
                            ->all();

                        $new_sale_analitics_same_address->average_price_400 = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
                    }
                }


            } else break;
            // вычисляем количество вариантов из которых производилось вычисление средней цены


            $message .= "<br> количенство объектов" . $new_sale_analitics_same_address->average_price_400_count . " при радиусе" . $radius;

            if ($new_sale_analitics_same_address->average_price_400_count > 10) break;
        }


        return [
            'message' => $message,
            'prices' => $prices,
            'new_prices' => $new_prices,
            'average' => $average,
            'simple_average_price' => $simple_average_price,
            'sliced_average_price' => $sliced_average_price,
            'sales_history' => $sales_history,
        ];


    }

    public function create_or_update_statistic_same_address_test_advansed($message)
    {
        $address = Addresses::findOne($this->id_address);


        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 5;


//            echo "<br>создаем статистику SaleAnaliticsSameAddress";


        $radiuses = [300, 900, 1500, 2000, 3000, 5000];
        $new_sale_analitics_same_address = New SaleAnaliticsSameAddress();
        $new_sale_analitics_same_address->rooms_count = $this->rooms_count;
        $new_sale_analitics_same_address->grossarea = $this->grossarea;
        $new_sale_analitics_same_address->id_address = $this->id_address;

        foreach ($radiuses as $radius) {
// TO DO не забудь поиграться с  разбросом в процентах
            $new_sale_analitics_same_address->radius = $radius;
            // $new_sale_analitics_same_address->floorcount = $this->floorcount;
            $id_addresses_400 = $address->getNearestSameIdAddresses($radius);
            if (!empty($id_addresses_400)) {
                $new_sale_analitics_same_address->average_price_400_count = SaleHistory::find()
                    ->filterWhere(['rooms_count' => $this->rooms_count])
                    ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                    ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                    ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                    ->andWhere(['in', 'id_address', $id_addresses_400])
                    ->count();
// удаляем откровенные дубликаты которые портят статистику
                $all_sales_history = SaleHistory::find()
                    ->select(['id', 'phone1', 'id_address'])
                    ->asArray()
                    ->filterWhere(['rooms_count' => $this->rooms_count])
                    ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                    ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                    ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                    ->andWhere(['in', 'id_address', $id_addresses_400])
                    ->all();
                // my_var_dump($all_sales_history);
                $new_all_sales_history = $all_sales_history;
                $i = 0;
                // удаляем из массива дабуликаты по адресу и телефону
                foreach ($all_sales_history as $sale_history) {
                    foreach ($new_all_sales_history as $new_sale_history) {
                        //если количество повторений равно более 1 то это явный дубликат
                        if (($sale_history['phone1'] == $new_sale_history['phone1']) and ($sale_history['id_address'] == $new_sale_history['id_address']) and ($sale_history['id'] != $new_sale_history['id'])) {
                            unset($all_sales_history[$i]);
                        }
                    }
                    $i++;

                }
                $unique_ids = [];
                // формируем массив с ids
                foreach ($all_sales_history as $item) {
                    array_push($unique_ids, $item['id']);
                }

                // выбираем цены из уникальных ids
                $prices1 = SaleHistory::find()
                    ->select('price')
                    ->Where(['in', 'id', $unique_ids])
                    ->asArray()
                    ->all();

                $sales_history = SaleHistory::find()
                    ->Where(['in', 'id', $unique_ids])
                    ->all();
                $prices = [];

                // формируем массив с ценами
                foreach ($prices1 as $item) {
                    array_push($prices, $item['price']);
                }


                $new_prices = $prices;
                if (count($new_prices) != 0) {
                    $average = round(array_sum($prices) / count($prices));
                    $simple_average_price = $average;


                    foreach ($prices as $price) {
                        if (($price > $average) or ($price * 1.8 < $average)) {
//                                echo " - " . $price;
                            unset($new_prices[array_search($price, $prices)]);
                            //   echo "-  удалили его";
                        }
                    }

                    asort($new_prices);
                    $count = count($new_prices);
//                        echo "<br>" . implode(",", $new_prices);
//                        echo "<br> кол-во элементов в массиве:".$count;
//                        echo "<br> брем только 30 % цен нижних цена в новом массиве".round(0.4*$count);
                    $new_prices_sliced = array_slice($new_prices, 0, round(0.4 * $count));
//                        echo "<br> новый массив после отрезки бесполезных вариантов : " . implode(",", $new_prices);
                    if (count($new_prices_sliced) != 0) $average = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
//                        echo "<br>новое cреднее число равно" . $average;
                    $sliced_average_price = $new_prices[round(0.4 * $count)];


                    if (count($new_prices_sliced) != 0) {
                        $sales_history = SaleHistory::find()
                            ->filterWhere(['rooms_count' => $this->rooms_count])
                            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                            ->andWhere(['in', 'id_address', $id_addresses_400])
                            ->andWhere(['<', 'price', $average])
                            ->all();

                        $new_sale_analitics_same_address->average_price_400 = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
                    }
                }


            } else
                break;
            // вычисляем количество вариантов из которых производилось вычисление средней цены


            $message .= "<br> количенство объектов" . $new_sale_analitics_same_address->average_price_400_count . " при радиусе" . $radius;

            if ($new_sale_analitics_same_address->average_price_400_count > 10) break;
        }


        return [
            'message' => $message,
            'prices' => $prices,
            'new_prices' => $new_prices,
            'average' => $average,
            'simple_average_price' => $simple_average_price,
            'sliced_average_price' => $sliced_average_price,
            'sales_history' => $sales_history,
        ];


    }

    public function create_or_update_statistic_same_address_daily()
    {
        $address = Addresses::findOne($this->id_address);


        // TO DO не забудь поиграться с  разбросом в процентах
        $period = 12;
        $persent = 5;
        // выделяем этот id
        $updated_sale_analiticts_same_address = SaleAnaliticsSameAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        //  echo "<br>";
        //var_dump($updated_sale_analiticts_same_address);

        if (!$updated_sale_analiticts_same_address) {

            echo "<br>создаем статистику SaleAnaliticsSameAddress";


            $radiuses = [900, 1500, 2000, 3000, 5000];
            $new_sale_analitics_same_address = New SaleAnaliticsSameAddress();
            $new_sale_analitics_same_address->rooms_count = $this->rooms_count;
            $new_sale_analitics_same_address->grossarea = $this->grossarea;
            $new_sale_analitics_same_address->id_address = $this->id_address;

            foreach ($radiuses as $radius) {
// TO DO не забудь поиграться с  разбросом в процентах
                $new_sale_analitics_same_address->radius = $radius;
                // $new_sale_analitics_same_address->floorcount = $this->floorcount;
                $id_addresses_400 = $address->getNearestSameIdAddresses($radius);
                if (!empty($id_addresses_400)) {
                    $new_sale_analitics_same_address->average_price_400_count = round(SaleHistory::find()
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->count());


                    $prices1 = SaleHistory::find()
                        ->select('price')
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->asArray()
                        ->all();
                    $prices = [];
                    foreach ($prices1 as $item) {
                        array_push($prices, $item['price']);
                    }


                    $new_prices = $prices;
                    if (count($new_prices) != 0) {
                        $average = round(array_sum($prices) / count($prices));


                        foreach ($prices as $price) {
                            if (($price > $average) or ($price * 1.8 < $average)) {
                                echo "<br>данный вариант выпадает из общего числа" . $price;
                                unset($new_prices[array_search($price, $prices)]);
                                //   echo "-  удалили его";
                            }
                        }
                        if (count($new_prices) != 0) {

                            $new_sale_analitics_same_address->average_price_400 = round(array_sum($new_prices) / count($new_prices));
                        }
                    }


                } else break;
                // вычисляем количество вариантов из которых производилось вычисление средней цены


                echo "<br> количенство объектов" . $new_sale_analitics_same_address->average_price_400_count . " при радиусе" . $radius;

                if ($new_sale_analitics_same_address->average_price_400_count > 20) break;
            }
            if (!empty($id_addresses_400)) $new_sale_analitics_same_address->average_price_400 = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['in', 'id_address', $id_addresses_400])
                ->average('price'));

            if (!$new_sale_analitics_same_address->save()) var_dump($new_sale_analitics_same_address->errors);
        } else {
            $updated_sale_analiticts_same_address = SaleAnaliticsSameAddress::find()
                ->where(['rooms_count' => $this->rooms_count])
                ->andwhere(['id_address' => $this->id_address])
                ->andwhere(['grossarea' => $this->grossarea])
                ->one();

            $radiuses = [900, 1500, 2000, 3000, 5000];

            foreach ($radiuses as $radius) {
// TO DO не забудь поиграться с  разбросом в процентах
                $updated_sale_analiticts_same_address->radius = $radius;
                // $new_sale_analitics_same_address->floorcount = $this->floorcount;
                $id_addresses_400 = $address->getNearestSameIdAddresses($radius);
                if (!empty($id_addresses_400)) {
                    $updated_sale_analiticts_same_address->average_price_400_count = round(SaleHistory::find()
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->count());


                    $prices1 = SaleHistory::find()
                        ->select('price')
                        ->filterWhere(['rooms_count' => $this->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_400])
                        ->asArray()
                        ->all();
                    $prices = [];
                    foreach ($prices1 as $item) {
                        array_push($prices, $item['price']);
                    }


                    $new_prices = $prices;
                    if (count($new_prices) != 0) {
                        $average = round(array_sum($prices) / count($prices));


                        foreach ($prices as $price) {
                            if (($price > $average) or ($price * 1.8 < $average)) {
                                echo "<br>данный вариант выпадает из общего числа" . $price;
                                unset($new_prices[array_search($price, $prices)]);
                                //   echo "-  удалили его";
                            }
                        }
                        if (count($new_prices) != 0) {

                            $updated_sale_analiticts_same_address->average_price_400 = round(array_sum($new_prices) / count($new_prices));
                        }
                    }


                } else break;
                // вычисляем количество вариантов из которых производилось вычисление средней цены


                echo "<br> количенство объектов" . $updated_sale_analiticts_same_address->average_price_400_count . " при радиусе" . $radius;

                if ($updated_sale_analiticts_same_address->average_price_400_count > 10) break;
            }
            if (!empty($id_addresses_400)) $updated_sale_analiticts_same_address->average_price_400 = round(SaleHistory::find()
                ->filterWhere(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
                ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
                ->andWhere(['in', 'id_address', $id_addresses_400])
                ->average('price'));

            if (!$updated_sale_analiticts_same_address->save()) var_dump($updated_sale_analiticts_same_address->errors);


            echo "<br>данная статистика SaleAnaliticsSameAddress уже существует";
        }


    }


    // берем похожие объекты
    public function get_nearest_same_address_objects($radius = 1000)
    {
        $address = Addresses::findOne($this->id_address);

        $id_addresses = $address->getNearestSameIdAddresses($radius);

        $period = 12;
        $persent = 5;

        //   вычисляем среднюю цену по данному адресу
        $sales_history_objects = SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
            ->andWhere(['in', 'id_address', $id_addresses])
            ->orderBy('date_start DESC')
            ->all();


        return $sales_history_objects;


    }

    // берем похожие объекты по данному адресу
    public function get_same_address_objects()
    {
        $period = 12;
        $persent = 10;

        $sales_history_objects = SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $persent) / 100)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $persent) / 100)])
            ->andWhere(['id_address' => $this->id_address])
            ->orderBy('date_start DESC')
            ->all();


        return $sales_history_objects;


    }


    // функции для   определения площади

    public function try_to_find_same_phone_and_id_address()
    {

        $sale_not_missed_squre_NULL = SaleHistory::find()
            ->select('grossarea')
            ->andWhere(['rooms_count' => $this->rooms_count])
            ->andWhere(['phone1' => $this->phone1])
            ->andwhere(['<>', 'grossarea', NULL])
            ->andWhere(['id_address' => $this->id_address])
            ->all();
        if (count($sale_not_missed_squre_NULL) > 0) {

            echo " в id " . $this->id . " missed squre" . $this->price;
            // echo " в description <b>" . $this->description . " </b>: <br>";
            echo " but the squre similar objects here: <br>";
            foreach ($sale_not_missed_squre_NULL as $item1) {
                echo " в id " . $item1->id . " squre  = " . $item1->grossarea . " and price" . $item1->price . "<br>";

            }
            $sale_not_missed_squre_NULL->grossarea;
        }

    }

    public function try_to_find_same_squre_average()
    {
        $sale_not_missed_grossarea = SaleHistory::find()
            ->andWhere(['rooms_count' => $this->rooms_count])
            ->andwhere(['<>', 'grossarea', 0])
            ->andWhere(['id_address' => $this->id_address])
            ->all();

        echo " в id " . $this->id . " rooms_count " . $this->rooms_count . " address <i>" . $this->address . "</i>";
        $array_of_grossarea = [];
        if (($this->not_enough_accuracy()) or ($this->address == '')) {

            echo " <b>нет адреса </b> ";

        } else {
            if (count($sale_not_missed_grossarea) != 0) {

                foreach ($sale_not_missed_grossarea as $item2) {
                    echo " - " . $item2->grossarea;
                    array_push($array_of_grossarea, $item2->grossarea);
                }

                $average = round(array_sum($array_of_grossarea) / count($array_of_grossarea));
                // вычисляем процент ошибки
                $max_value = round(max($array_of_grossarea) * 100 / $average, 2) - 100;
                $min_value = 100 - round(min($array_of_grossarea) * 100 / $average, 2);
                echo " % of mistake from min <b>" . $min_value . "</b><br>";
                echo " % of mistake from max <b>" . $max_value . "</b>";
                echo " average" . $average;
                echo "<br>";
                if (($max_value < 10) and ($min_value < 10)) $this->grossarea = round($average);

            } else " <b>данные отсутствуют </b>";
        }


    }

    public function try_to_find_grossarea_in_description()
    {
        // формируем массив всех вариантов площадей
        $array_of_squre_salehistory = SaleHistory::find()
            ->select('grossarea')
            ->distinct('grossarea')
            ->where(['id_address' => $this->id_address])
            ->andwhere(['rooms_count' => $this->rooms_count])
            ->all();
        $array_of_squre = [];
        foreach ($array_of_squre_salehistory as $j) {
            if ($j != 0) array_push($array_of_squre, $j->grossarea);
        }
// ищем совпадение друхзначных чисел с массиовов
        $string = $this->description;
        echo "<br>";
        echo " в id " . $this->id . " missed squre" . $this->price;
        echo " в description <b>" . $this->description . " </b>: <br>";
        preg_match_all("/\d\d/", $string, $matches);
        foreach ($matches[0] as $i) {
            if (in_array($i, $array_of_squre)) {
                echo "удалось найти совпадение по площади";
                echo "Нашли площадь <h6>" . $i . "</h6>";
                return $i;
            } else return false;


        }
    }

    public function not_enough_accuracy()
    {

        $address = Addresses::findOne($this->id_address);
        if (($address->precision_yandex == 'street') or ($address->precision_yandex == 'other')) return true;

    }

    public function year()
    {

        $address = Addresses::findOne($this->id_address);

        if (($address->year != Null) or ($address->year != 0)) return $address->year;
        else return false;

    }


    public function set_year($year)
    {
        $address = Addresses::findOne($this->id_address);
        if (($address->year != $year) and ($address->year != 0)) {
            echo "<h3>проблема несовместимости годов</h3>" . $address->year . "<br>";
            $address->year = $year;
            $address->save();
        }

    }

    public function import_year()
    {
        $address = Addresses::findOne($this->id_address);
        if ($address->year != 0) {
            $sale_history = SaleHistory::findOne($this->id);
            $sale_history->year = $address->year;
            if (!$sale_history->save()) var_dump($sale_history->errors());
        }

    }

    public function try_to_find_year_and_set()
    {
        if (preg_match_all('/[1,2][9,0]\d\d/', $this->description, $matches)) {

            foreach ($matches[0] as $item) {
                if (in_array($item, range(1950, 2018))) {
                    echo "<b>" . $item . "</b>  ";
                    echo $this->description;
                    echo "<br>";
                    $this->set_year($item);
                    return $item;
                } else return false;

            }
        }


    }


    public function try_to_find_year()
    {
        if (preg_match_all('/[1,2][9,0]\d\d/', $this->description, $matches)) {

            foreach ($matches[0] as $item) {
                $ajax_button = "<button class=\"btn btn-success btn-xs set-year-and-fix-it\"
                                data-id_address=\"$this->id_address\"
                                data-year=\"$item\">$item</button>";
                $message = "<b>" . $ajax_button . "</b> in ";

                if (in_array($item, range(1950, 2018))) {
                    $strpos = strpos($this->description, $item);
                    $hilighted_str_start = substr($this->description, $strpos - 15, 15);
                    $hilighted_str_end = substr($this->description, $strpos + 4, 15);
                    $message .= $hilighted_str_start . "<b>" . $item . "</b>" . $hilighted_str_end . " - ";
                    $response = [
                        'message' => $message,
                        'year' => $item
                    ];

                    return $response;
                } else return [
                    'message' => '',
                    'year' => 0
                ];

            }
        }


    }


    public
    function parse_missing_house_type_parameters()
    {
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address) {
            if (($address->house_type != 0) and ($address->house_type != Null)) {

                // echo " house_type = " . $address->house_type;
                // находим все sale c данным id address
                $sales_to_change_house_type = SaleHistory::find()
                    ->where(['id_address' => $this->id_address])
                    ->all();
                // пробегаемся по ним
                foreach ($sales_to_change_house_type as $item3) {


                    if ($sales_to_change_house_type) {
                        $sale_to_change_house_type = SaleHistory::find()
                            ->where(['id' => $item3->id])
                            ->one();
                        $sale_to_change_house_type->house_type = $address->house_type;
                        //echo "удалось присвоить house_type id =" . $sale_to_change_house_type->id . "house_type = " . $address->house_type;
                        if (!$sale_to_change_house_type->save()) $sale_to_change_house_type->errors;
                    }
                }
            }
        }

    }

    public
    function parse_missing_floorcount_parameters()
    {
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address) {
            if (($address->floorcount != 0) and ($address->floorcount != Null)) {

                // echo " floorcount = " . $address->floorcount;
                // находим все sale c данным id address
                $sales_to_change_floorcount = SaleHistory::find()
                    ->where(['id_address' => $this->id_address])
                    ->all();
                // пробегаемся по ним
                foreach ($sales_to_change_floorcount as $item3) {


                    if ($sales_to_change_floorcount) {
                        $sale_to_change_floorcount = SaleHistory::find()
                            ->where(['id' => $item3->id])
                            ->one();
                        $sale_to_change_floorcount->floorcount = $address->floorcount;
                        // echo "удалось присвоить floorcount id =" . $sale_to_change_floorcount->id . "floorcount = " . $address->floorcount;
                        if (!$sale_to_change_floorcount->save()) $sale_to_change_floorcount->errors;
                    }
                }
            }
        }

    }

    public function try_to_detect_rooms_count()
    {

        echo "присваиваем rooms_count было " . $this->rooms_count . " стало " . rooms_count($this->title) . " " . $this->title . "<br>";
        $this->rooms_count = rooms_count($this->title);
        if (!in_array($this->rooms_count, [1, 2, 3, 4, 5, 6, 20, 30])) {
            echo " troble" . $this->title . " " . $this->id;
            echo $this->rooms_count;
        } else {
            echo "no troble";
            if ($this->save()) 1;
            else echo $this->errors;

        }
    }

    public function phpQueryParsingGroosarea()
    {
        // парсинг со страницы irr
        if ($this->id_sources == 1) {
            $file = file_get_contents($this->url);
            $pq = phpQuery::newDocument($file);
            echo " <a href=\"" . $this->url . "\"> url </a>";
            $str = $pq->find('.productPage__characteristicsItemValue')->text();
            preg_match("/\d\d/", $str, $output_array);
            $this->grossarea = (int)$output_array[0];
            $str1 = $pq->find('.productPage__infoColumnBlockText')->text();
            preg_match_all("/Этажей в здании:.+/", $str1, $output_array);
            preg_match("/\d+/", $output_array[0][0], $numbers);
            $this->floorcount = $numbers[0];

            preg_match_all("/Материал стен:.+/", $str1, $output_array);
            $house_type = preg_split("/Материал стен:.+/", $output_array[0][0]);
            $this->house_type = parsing_house_type(ucfirst(trim($house_type[1])));


            echo "<br> got grossarea by phpQueryOwnParsing" . $this->grossarea;
            echo "<br> got house type by phpQueryOwnParsing" . $this->house_type;
            echo "<br> got floorcount by phpQueryOwnParsing" . $this->floorcount;
        }
    }

    public function phpQueryParsingIrr()
    {
        // парсинг со страницы irr
        if ($this->id_sources == 1) {
            $file = file_get_contents($this->url);
            $pq = phpQuery::newDocument($file);

            $str = $pq->find('.productPage__characteristicsItemValue')->text();
            preg_match("/\d\d/", $str, $output_array);
            $this->grossarea = (int)$output_array[0];
            $str1 = $pq->find('.productPage__infoColumnBlockText')->text();
            preg_match_all("/Этажей в здании:.+/", $str1, $output_array);
            preg_match("/\d+/", $output_array[0][0], $numbers);
            $this->floorcount = $numbers[0];

            preg_match_all("/Материал стен:.+/", $str1, $output_array);
            $house_type = preg_split("/Материал стен:/", $output_array[0][0]);
            $this->house_type = parsing_house_type(trim($house_type[1]));


            if ($this->grossarea) echo "<br> got grossarea by phpQueryOwnParsing" . $this->grossarea;
            echo "<br> got house type by phpQueryOwnParsing" . $this->house_type;
            echo "<br> got floorcount by phpQueryOwnParsing" . $this->floorcount;
        }

        /* if ($this->id_sources == 2) {
             $file = file_get_contents($this->url);
             $pq = phpQuery::newDocument($file);

             $str = $pq->find('.offer-card__main-feature-title')->text();
             preg_match("/\d\d/", $str, $output_array);
             $this->grossarea = (int)$output_array[0];

             if ($this->grossarea) echo "<br> got grossarea by phpQueryOwnParsing" . $this->grossarea;
         }*/
    }

    public function export_from_irr_dublicates_if_exists()
    {
        $one_irr_dublicate = self::find()
            ->andWhere(['<>', 'id', $this->id])
            ->andWhere(['title' => $this->title])
            ->andWhere(['rooms_count' => $this->rooms_count])
            ->andWhere(['phone1' => $this->phone1])
            // ->andWhere(['description' => $this->description])
            ->andWhere(['id_sources' => $this->id_sources])
            ->one();
// если данные элементы нащлись то
        if (count($one_irr_dublicate) > 0) {
// копируем параметры чтоб не присваивать посредством обработки а именно
            $this->id_address = $one_irr_dublicate->id_address;
            $this->locality = $one_irr_dublicate->locality;
            $this->floor = $one_irr_dublicate->floor;
            $this->floorcount = $one_irr_dublicate->floorcount;
            $this->grossarea = $one_irr_dublicate->grossarea;


            // берем список дубликатов ирр из найденного дубликата ирр добавляем в этот список собственный id  переносим это все с $this


            if ($one_irr_dublicate->id_irr_duplicate != '') {
                echo "<h6> у данного дубликата уже ранее были дубликаты</h6>";
                if ($this->id_irr_duplicate == '') {
                    echo "у данного дубликата не было ранее дубликатов";
                    $this->id_irr_duplicate = $one_irr_dublicate->id_irr_duplicate . "," . $one_irr_dublicate->id;
                } else {

                    $this->id_irr_duplicate = $this->id_irr_duplicate . "," . $one_irr_dublicate . "," . $one_irr_dublicate->id;
                }

            } else {
                if ($this->id_irr_duplicate == '') {
                    echo "у данного дубликата не было ранее дубликатов";
                    $this->id_irr_duplicate = (string)$one_irr_dublicate->id;
                } else {
                    echo "<h6> у данного дубликата уже ранее были дубликаты</h6>";

                    $this->id_irr_duplicate = $this->id_irr_duplicate . "," . $one_irr_dublicate->id;

                }
            }

            echo "<br > добавили с список дубликатов " . $this->id_irr_duplicate;
            echo " < br> данное объявление имеет дубликаты по описанию " . $this->id . " в количестве " . count($one_irr_dublicate) . " поданное " . date('j.n.Y', $this->date_start);
            echo "удалили этот дублиткат ирр" . $one_irr_dublicate->id;
            $one_irr_dublicate->delete();
            return true;
        } else {
            return false;
            echo " < br>не нашлось будликата";
        }

    }

    // делаем статус объекта непригодным для добавления в статистику т.к. не удалось его прогеокодировать
    public function set_broken_geocodate()
    {
        if (($this->id_address == null) or ($this->id_address == 0)) {

            $this->broken = 1;
        } else {
            $address = Addresses::findOne($this->id_address);
            if (in_array($address->precision_yandex, ['street', 'near', 'other'])) $this->broken = 1;;

        }
    }

}
