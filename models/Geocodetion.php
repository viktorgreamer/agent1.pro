<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04.07.2017
 * Time: 17:23
 */

namespace app\models;


use app\components\Mdb;
use Yii;
use yii\base\Model;
use phpQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

// цель данного класса устанавливать точный адрес (id_address) в системе
//  методы данного класса определяют точный id_address

class Geocodetion extends Model
{
    const STREET_VARIANTS = "/(улица|проезд|проспект|бульвар|переулок|набережная|шоссе|ручей|Ручей)/";
    public $module;
    public $model;
    public $coords_x;
    public $coords_y;
    public $precision;
    public $fulladdress;
    public $xml_string;
    public $xml_coords;
    public $locality;
    public $AdministrativeAreaName;
    public $house_type;
    public $floorcount;
    public $district;
    public $ThoroughfareName;
    public $TrimmedThoroughfareName;
    public $PremiseNumber;
    public $VariableCounts;
    public $house;
    public $hull;
    public $geocodated;
    public $id_address;
    public $log;
    const ERROR = 9;

    const STOP = 5;

    const GEOCODATED_STATUS_ARRAY = [
        0 => 'NO GEOCODATION',
        1 => 'FULL EXACT',
        2 => 'EXACT_BY_NEAR',
        3 => 'NUM_BY_NEAR',
        4 => 'BY_DESCR',
        5 => 'NOT',
        6 => 'NEW ADDR',
        7 => 'MAN',
        8 => 'READY',
        9 => 'ERR',


    ];
    const PRECISION_YANDEX = ['exact','number','near','street','other','new'];

    public function init()
    {
        parent::init();
        $module = Yii::$app->params['module'];
       // $module = Yii::$app->session['module'];
        $this->module = $module;
    }

    public function getModule_text()
    {
        return $this->module->region;
    }

    public function getLink()
    {
        return Html::a('link', $this->xml_string, ['target' => 'blank']);

    }

    public function getLinkCoords()
    {
        return Html::a('link', $this->xml_coords, ['target' => 'blank']);

    }

    public function getMapString()
    {
        $customurl = "https://yandex.ru/maps/?mode=search&text=" . $this->fulladdress; //$model->id для AR
        return \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
            ['title' => Yii::t('yii', 'yandex maps'),
                'data-pjax' => '0',
                'target' => '_blank']);

    }

    public function getMap()
    {
        $customurl = "https://yandex.ru/maps/?mode=search&text=" . $this->coords_x . ", " . $this->coords_y; //$model->id для AR
        return \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
            ['title' => Yii::t('yii', 'yandex maps'),
                'data-pjax' => '0',
                'target' => '_blank']);

    }

    public function getGeocodated_status()
    {
        return self::GEOCODATED_STATUS_ARRAY[$this->geocodated];
    }

    public function getStreet()
    {
        $street = '';
        if ($this->TrimmedThoroughfareName) $street .= $this->TrimmedThoroughfareName;
        if ($this->house) $street .= "," . $this->house;
        if (($this->hull) and ($this->hull != '-')) $street .= ", " . $this->hull;

        return $street;
    }

    public function getAddress() {

        return Html::a('address', ['addresses/view', 'id' => $this->id_address], ['target' => '_blank']);
    }

// данный метод загружает в модель sale (salehistory) точный id_address если его установить не удалось то ставит параметр
// sale->geocodated = 5 (геокодирование не произзводилось))
// sale->geocodated = 1 (что значит геокодование произошло с абсолютной точнотью (exact))
// sale->geocodated = 2 (что значит геокодование произошло с абсолютной точнотью (exact), но по средством метода try_to_detect_nearist_id_addresses())
// sale->geocodated = 3 (точность number по средством метода try_to_detect_nearist_id_addresses())
// sale->geocodated = 4 (точность exact адрес найден в описании)

// sale->geocodated = 6 (точность number адрес создан только что)
// sale->geocodated = 9 (что значит ошибка)

    public static function getAllAddresses($module)
    {
        if (empty(Yii::$app->cache->get('all_addresses'))) {
            Addresses::setTablePrefix($module->city);
            $all_addresses = Addresses::find()->select('street, house, hull')->where(['status' => 2])->all();

            Yii::$app->cache->set('all_addresses', $all_addresses, 60 * 10);
        }

    }

    // метод для определения адреса для http-геозапроса yandex'a
    protected function generateFullAddress($type = 'sale')
    {

        if ($type == 'sale') {
            // экспорт параметров
            if ($this->model->house_type == 0) $this->house_type = null; else $this->house_type = $this->model->house_type;
            if ($this->model->floorcount == 0) $this->floorcount = null; else $this->floorcount = $this->model->floorcount;


            if (preg_match("/".$this->module->region_rus."/", $this->model->address)) {

                $this->log .= span(" <b>Город уже прописан в адресе</b><br>");
                $this->fulladdress = $this->model->address;

                if (preg_match("/".$this->module->oblast_rus."/", $this->model->address)) {
                    $this->log .= span("<b>Область уже прописана в адресе</b><br>");
                    $this->fulladdress = $this->model->address;
                }
            } else $this->fulladdress = $this->module->region_rus . "," . $this->model->address;
            $this->log .= span('SALE GEOCODATION', 'primary') . "<br>";
        }
        if ($type == 'address') {
            $this->fulladdress = $this->model->AdministrativeAreaName . "," . $this->model->locality . "," . $this->model->address;

            $this->log .= span('ADDRESS GEOCODATION', 'primary') . "<br>";
        }
    }

    public function TODO($type = 'sale')
    {

        $this->generateFullAddress($type);

        if ($type == 'sale') {
            $this->geocodate_sale();
        }


    }

    // главный управляющий метод
    protected function geocodate_sale()
    {
        // делаем запрос к http yandex геокодеру и прописываем свойства модели $this
        $this->get_yandex_request();

        // значит адрес пришел точный
        if ($this->precision == 'exact') {
            $this->geocodated = 1;
            $this->export_from_addresses();
            return true;
        } else {

            $this->log .= Mdb::ProgresBar();
            // если нет то ищем адрес методом перебора в описании
            $id_address_by_description = $this->SearchingAddressInString('description');
            if (($id_address_by_description) and ($this->geocodated != 5)) {
                $this->export_from_addresses();
                $this->id_address = $id_address_by_description;
                return true;
            } else {

                //ищем адрес методом перебора в адресе
                $id_address_by_address = $this->SearchingAddressInString('address');
                if (($id_address_by_address) and ($this->geocodated != 5)) {
                    $this->export_from_addresses();
                    return true;
                }
                // ищем методом перебора различных вариантов рядом
                if (in_array($this->precision, ['number', 'near'])) {
                    $id_address_by_near = $this->try_to_detect_nearist_id_addresses();
                    if (($this->geocodated == 2) and ($id_address_by_near)) $this->export_from_addresses();
                    if (($this->geocodated == 3) and ($id_address_by_near)) $this->export_from_addresses();
                    return true;
                }


            }
        }


        // если точность number а данный адрес не существует а базе создаем его и присваиваем
        if (($this->precision == 'number') and ($this->VariableCounts == 0)) {

            $this->log .= "creating address from number " . span($this->model->address);
            $this->create_new_address($this->model);
            $this->geocodated = 6;
            return true;
        }

        $this->log .= span(" cannot get address any methods ", DANGER) . "<br>";
        $this->id_address = null;

        if ((!$this->model->coords_x) or (!$this->model->coords_y)) {
            $this->log .= "присвоили координаты по умолчанию т.к. они были пустые<br>";
            $this->export_default_coords($this->model);
        } else  $this->log .= "оставили координаты которые есть<br>";
        $this->geocodated = 9;
        return false;


    }

    // присвоили координаты по умолчанию т.к. они были пустые
    public function export_default_coords($sale)
    {
        $sale->coords_x = $this->module->coords_x;
        $sale->coords_y = $this->module->coords_y;
    }

    public
    function try_your_own_engine($sale)
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
            $address = trim(preg_replace("/(улица|проезд|проспект|бульвар|ул|просп|переулок)/", "", $sale->address));
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

                if (strpos(mb_strtolower($address), $trimmed_street) !== false) {
                    //  echo " найдено совпадение " . $trimmed_street;


                    // иногда в названии есть цифры так вот о них надо знать заранее например улица 20 января
                    // ищем эти цифры
                    if (preg_match_all('/\d+/', $street->street, $number_in_name_street)) {
                        $array_of_number_in_name_street = $number_in_name_street[0];
                        // echo "<br> numbers in the name street";
                        //var_dump($array_of_number_in_name_street);
                    }
                    // ищем вообще все номера в имени удаляем номера в имени и получаем номер дома и корпус
                    if (preg_match_all('/\d+/', $address, $numbers)) {
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
                        if (preg_match_all('/А|Б|В|Г|Д|Е|Е|Ж|З|И|а|б|в|г|д|е|ж|з|и/', strstr($address, strpos($address, $house)), $numbers)) {
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
            echo "<br>" . $sale->address . " address id=" . $seaching_address->id;
            return $seaching_address->id;

        } else return false;
    }

    public
    function get_id_address()
    {
        $address = Addresses::find()
            ->where(['street' => $this->TrimmedThoroughfareName])
            ->andwhere(['house' => $this->house])
            ->andwhere(['locality' => $this->locality])
            ->andfilterwhere(['AdministrativeAreaName' => $this->AdministrativeAreaName])
            ->andFilterWhere(['hull' => $this->hull])
            ->one();

        if ($address) {
            $this->log .= span("НАШЛИ АДРЕС") . "<br>";

            $this->id_address = $address->id;
            return $address->id;
        } else if (($this->precision == 'exact')) return $this->create_new_address($this->model);


    }

// метод для создания нового адреса из модели $geocodetion и $sale
    public
    function create_new_address()
    {

        $exicts_by_name_street = Addresses::find()
            ->where(['locality' => $this->locality])
            ->andwhere(['district' => $this->district])
            ->andwhere(['hull' => $this->hull])
            ->andwhere(['house' => $this->house])
            ->andwhere(['street' => $this->TrimmedThoroughfareName])
            ->exists();
        $exicts_by_coords = Addresses::find()
            ->where(['coords_x' => $this->coords_x])
            ->andwhere(['coords_y' => $this->coords_y])
            ->exists();
        if ((!$exicts_by_name_street) AND (!$exicts_by_coords)) {
            $address = new Addresses();
            $address->coords_x = $this->coords_x;
            $address->coords_y = $this->coords_y;
            $address->locality = $this->locality;
            $address->district = $this->district;
            $address->street = $this->TrimmedThoroughfareName;
            $address->house = $this->house;
            $address->hull = $this->hull;
            $address->precision_yandex = $this->precision;
            $address->floorcount = $this->model->floorcount;
            $address->house_type = $this->model->house_type;
            $address->address = $this->ThoroughfareName . ", " . $this->PremiseNumber;
            // отладочное сообщение
            $address->save();
        }
        // ищем id только что созданной записи
        $address = Addresses::find()
            ->where(['coords_x' => $this->coords_x])
            ->andWhere(['coords_y' => $this->coords_y])
            ->one();
        $this->id_address = $address->id;

        return $address->id;


    }

    // взаимный обмен параметрами
    public
    function export_from_addresses()
    {
        // параметр address не переносим т.к. он нужен для отслеживания дальнейших изменений
        $address = Addresses::find()
            ->where(['id' => $this->get_id_address()])
            ->one();

        // переносим
        if ($address) {
            // update параметров в sale
            // $address->update_to_sale($this->model);

            // если адрес неполный то попытаемся его дополнить параметрами из sale
            if (!$address->isFull()) {
                $log = $address->update_from_sale($this->model);
            }

            // если обновились какие то параметры то созраняем модель
            if ($log) {
                $address->save();
                $this->log .= $log;
            }
        }
    }

    // делаем запрос к yandex и получаем свойства модели Geocodation
    public
    function get_yandex_request($type = 'sale')
    {
        $this->xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($this->fulladdress) . '&results=1';
        $file = file_get_contents($this->xml_string);
        $this->extract_response($file);
        if ($type == 'sale') {
            $this->log .= " AdministrativeAreaName " . $this->AdministrativeAreaName . " oblast_rus " . $this->module->oblast_rus;

            if ($this->AdministrativeAreaName == $this->module->oblast_rus) {
                if (in_array($this->precision, ['exact', 'number','near'])) return true;
            } else  $this->log .= span('АНАЛИЗ YANDEX API ADDRESS НЕВОЗМОЖЕН', 'alert') . "<br>";

            if (($this->model->coords_x) and ($this->model->coords_x)) {

                $this->coords_x = $this->model->coords_x;
                $this->coords_y = $this->model->coords_y;

                $this->xml_coords = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($this->getCoords()) . '&results=1';
                $this->log .= span('TRYING TO GET ADDRESS FROM COORDS') . "<br>";

                $file = file_get_contents($this->xml_coords);
                $this->extract_response($file,'coords');

                $this->log .= " AdministrativeAreaName " . $this->AdministrativeAreaName . " oblast_rus " . $this->module->oblast_rus;

                if ($this->AdministrativeAreaName != $this->module->oblast_rus) {
                    $this->log .= span('GETTING ADDRES FROM  HTTP-YANDEX UNREALYABLE', DANGER) . "<br>";
//                    $this->AdministrativeAreaName = null;
//                    $this->TrimmedThoroughfareName = null;
//                    $this->precision = 'other';
                } else $this->log .= span('CAN GET ADDRESS',SUCCESS) . "<br>";
            } else $this->log .= span('CAN GET ADDRESS', SUCCESS) . "<br>";


        }


    }

    public function getCoords()
    {
        return $this->coords_y . " ," . $this->coords_x;
    }

    public function extract_response($file, $type = 'address')
    {
        $pq = phpQuery::newDocument($file);


        $this->precision = $pq->find('precision')->text();
        if ($type != 'coords') {
            $coords = str_replace(' ', ',', $pq->find('pos')->text());
            // получаем координаты
            $coords_array = explode(",", $coords);
            $this->coords_x = $coords_array[1];
            $this->coords_y = $coords_array[0];
        }

        $this->log .= Html::a('coords after extract','http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($this->getCoords()) . '&results=1', ['target' => '_blank'])."<br>";
        $this->locality = $pq->find('LocalityName')->text();
        $this->AdministrativeAreaName = $pq->find('AdministrativeAreaName')->text();

        $this->district = $pq->find('DependentLocalityName')->text();
        $this->ThoroughfareName = $pq->find('ThoroughfareName')->text();
        // ThoroughfareName приходит с пробелами и с 'улица переулок и пр." для работы вырезаем данные элементы
        $this->TrimmedThoroughfareName = trim(preg_replace(self::STREET_VARIANTS, "", $this->ThoroughfareName));
        if ($this->TrimmedThoroughfareName == '') $this->TrimmedThoroughfareName = null;

        $this->PremiseNumber = $pq->find('PremiseNumber')->text();
        if (preg_match_all('/\d+/', $this->PremiseNumber, $numbers)) {
            // приходит ответ номера дома
            $this->house = $numbers[0][0];
            // приходит ответ номера корпуса
            if ($numbers[0][1]) $this->hull = $numbers[0][1];
            else $this->hull = '-';


            // if ($house != 0) echo "house =  " . $house;
            // if (!empty($hull)) echo ", hull =  " . $hull;
        }
        // скидываем hull и house чтобы они не участвовали в поиске
        if ($this->precision == 'near') {
            $this->house = null;
            $this->hull = null;
        }
    }

// далаем запрос к yandex и получаем свойства модели gelcodation
    public
    function Load_Address($address)
    {
        $this->fulladdress = $address->locality . ", " . $address->street . ", " . $address->house;
        if ($address->hull != '-') $this->fulladdress .= ", к" . $address->hull;
        echo "<br>" . $this->fulladdress;


        $this->xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($this->fulladdress) . '&results=1';
        echo "<a href=" . $this->xml_string . " > link </a>";
        $file = file_get_contents($this->xml_string);
        $pq = phpQuery::newDocument($file);

        $this->precision = $pq->find('precision')->text();
        $coords = str_replace(' ', ',', $pq->find('pos')->text());

        // получаем координаты
        $coords_array = explode(",", $coords);
        $this->coords_x = $coords_array[1];
        $this->coords_y = $coords_array[0];
        $this->locality = $pq->find('LocalityName')->text();

        $this->district = $pq->find('DependentLocalityName')->text();
        $this->ThoroughfareName = $pq->find('ThoroughfareName')->text();
        // ThoroughfareName приходит с пробелами и с 'улица переулок и пр." для работы вырезаем данные элементы
        $this->TrimmedThoroughfareName = trim(preg_replace(self::STREET_VARIANTS, "", $this->ThoroughfareName));
        if ($this->TrimmedThoroughfareName == '') $this->TrimmedThoroughfareName = null;

        $this->PremiseNumber = $pq->find('PremiseNumber')->text();
        if (preg_match_all('/\d+/', $this->PremiseNumber, $numbers)) {
            // приходит ответ номера дома
            $this->house = $numbers[0][0];
            // приходит ответ номера корпуса
            if ($numbers[0][1]) $this->hull = $numbers[0][1];
            else $this->hull = '-';


            // if ($house != 0) echo "house =  " . $house;
            // if (!empty($hull)) echo ", hull =  " . $hull;
        }
        $address->coords_x = $this->coords_x;
        $address->coords_y = $this->coords_y;
        $address->precision_yandex = $this->precision;
        $address->address = $this->ThoroughfareName . ", " . $this->PremiseNumber;
        $address->locality = $this->locality;
        $address->district = $this->district;
        $address->street = $this->TrimmedThoroughfareName;
        $address->house = $this->house;
        $address->hull = $this->hull;
        $address->fullfilled = 2;
        $address->save();

    }

// ищем дома с адресами похожими названием улицы и номером дома этажностью и типом дома
    public
    function try_to_detect_nearist_id_addresses()
    {
        $house_type = $this->house_type;
        $floorcount = $this->floorcount;
        $addresses = $this->GenerateAddresses($house_type, $floorcount);

        // если количество адресов 0
        if (count($addresses) == 0) {
            $this->log .= " VARIANTS_OF_ADDRESS " . span(count($addresses)) . "-> DEL " . span('house_type');
            $house_type = null;
            $addresses = $this->GenerateAddresses($house_type, $floorcount);
        }
        if (count($addresses) == 0) {
            $this->log .= " VARIANTS_OF_ADDRESS " . span(count($addresses)) . " DEL " . span('floorcount');
            $floorcount = null;
            $addresses = $this->GenerateAddresses($house_type, $floorcount);
        }
        $this->VariableCounts = count($addresses);
        $this->log .= span('FINAL') . "VARIANTS_OF_ADDRESS " . span(count($addresses));

        // если количество адресов 0 , return false;
        if ($this->VariableCounts == 0) {
            $this->log .= span('FAILURE', 'alert') . "SEARCH_BY_NEAR<br>";
            return false;
        }
        // если количество адресов 1
        if ($this->VariableCounts == 1) {
            // делаем запрос к этому адресу
            $this->id_address = $addresses->id;
            $this->geocodated = 2;
            $this->log .= "нашли адрес " . span($addresses->address);
            return $addresses->id;


        };
        // если количество адресов больше 1
        if (($this->VariableCounts > 1) and ($this->VariableCounts < 5)) {

            // делаем запрос к первому попавшемуся адресу
            foreach ($addresses as $address) {
                $this->geocodated = 3;
                $this->log .= "нашли адрес (!!! приблизительный адрес)" . span($addresses->address) . " " . self::GEOCODATED_STATUS_ARRAY[$this->geocodated] . "<br>";
                return $address->id;
            }

        }


    }

    // метод поиска шаблона вариантов адресов в тексте (описании или адресе)

    public
    function SearchingAddressInString($type_haystack)
    {
        $this->log .= "SEARCH IN " . span($type_haystack) . "<br>";

        switch ($type_haystack) {
            case 'address': {
                // если точность address пришла не `number`,`near`, то поиск по линии address бесполезен вернем false;
                if (!in_array($this->precision, ['near', 'number'])) {
                    $this->log .= span('FAILURE', 'alert') . " SEARCH IN " . span($type_haystack) . "  " . span('BAD PRECESION', 'alert') . " <br>";
                    return false;
                }
                // если address пустой, то поиск по линии address бесполезен вернем false;
                if ($this->model->address == '') {
                    $this->log .= span('FAILURE', 'alert') . " SEARCH IN " . span($type_haystack) . " " . span('NO ADDRESS', 'alert') . " <br>";
                    return false;
                } else $haystack = $this->model->address . "u";

                break;
            }
            case 'description': {
                // если description пустой, то поиск по линии description бесполезен вернем false;
                if ($this->model->description == '') {
                    $this->log .= span('FAILURE', 'alert') . " SEARCH IN " . span($type_haystack) . " " . span('NO DESCRIPTION', 'alert') . " <br>";
                    return false;
                } else  $haystack = $this->model->description . "u";
                break;
            }


        }
        // если прошли первичные фильтры, то идем дальше
        $this->log .= ": <i>" . mb_strtolower($haystack) . "</i> street = " . $this->TrimmedThoroughfareName . " house= " . $this->house . "<hr> ";

        //  ищем дома с адресами похожими названием улицы и номером дома в описании,  если известно название улицы и номер дома сортируем их в обратном  порядке
        // чтобы в московская 126к4 н нашел сначала !126к4 а только потом 126 без корпуса
        // перебор всех вариантов в зависимоти от house_type и floorcount
        $response = $this->FindAddressInOrderDependencies($type_haystack, $haystack);


        if ($response) {

            $id_address = $response['id_address'];
            $count_of_matches = $response['count_of_matches'];
            $this->log .= " кол-во адресов : <b> " . $count_of_matches . "</b><br>";
            $this->log .= span("id_address =" . $id_address) . " " . self::GEOCODATED_STATUS_ARRAY[$this->geocodated] . "<br>";
            $this->log .= Mdb::ProgresBar();
            if ($count_of_matches == 1) {

                $this->geocodated = 4;
                $this->id_address = $response['id_address'];
                return $id_address;
            } else {

                $this->geocodated = 4;
                $this->id_address = $response['id_address'];
                return $id_address;
            }
        } else {
            $this->log .= Mdb::ProgresBar();
            return false;
        }
    }

    // данный метод создан переборки всех возможных вариантов улиц и домов с floorcount, house_type или без них
    public function FindAddressInOrderDependencies($type_haystack, $haystack)
    {
        $this->log .= span('FindAddressInOrderDependencies') . "<br>";
        // массив вариаций поиска по параметрам
        $array_sequence_of_parameters = [
            '0' => ['house_type' => null, 'floorcount' => null, 'count_of_matches' => 0, 'id_address' => 0],
            //'1' => ['house_type' => null, 'floorcount' => $this->floorcount, 'count_of_matches' => 0, 'id_address' => 0],
            //'2' => ['house_type' => $this->house_type, 'floorcount' => null, 'count_of_matches' => 0, 'id_address' => 0],
            //'2' => ['house_type' => $this->house_type, 'floorcount' => $this->floorcount, 'count_of_matches' => 0, 'id_address' => 0]
        ];
        $this->log .= \Yii::$app->view->render('@app/views/my-debug/_modal_info', ['icon' => 'array_start', 'message' => json_encode($array_sequence_of_parameters)]);


        foreach ($array_sequence_of_parameters as $key => $parameter) {
            // выбираем все вариации адресов с параметрами
            $addresses = $this->GenerateAddresses($parameter['house_type'], $parameter['floorcount']);
            $this->log .= "сгенерировано " . count($addresses) . " адреса(-ов) исходя из параметров<br>";
            // my_var_dump($addresses);
            $count_of_matches = 0;
            $id_addressess = [];
            if ($addresses) {
                foreach ($addresses as $address) {

                    // ищем адреса в $haystack
                    $id_address = $this->SearchAddressPattern($address, $type_haystack, $haystack);
                    if ($id_address) {
                        array_push($id_addressess, $id_address);
                        $count_of_matches++;
                        continue;
                    }
                }
                $this->log .= \Yii::$app->view->render('@app/views/my-debug/_modal_info', ['icon' => 'patterns', 'message' => Yii::$app->params['pattern_log']]);
            }
            // выбрасываем поля из массива, где $count_of_matches =0  иначе заносим параметры в массив
            if ($count_of_matches == 0) {
                $this->log .= span('нет совпадений', 'alert') . "<hr>";
                unset($array_sequence_of_parameters[$key]);
            }

            elseif ($count_of_matches == 1) {
                // если пришел только один вариант то выходим
                $this->log .= span('EXACT') . " SEARCH IN " . span($type_haystack) . " <br>";
                return [
                    'count_of_matches' => 1,
                    'id_address' => $id_addressess[0]
                ];
            } else {
                $this->log .= span('NOT EXACT', 'alert') . " SEARCH IN " . span($type_haystack) . " <br>";
                $new_array_sequence_of_parameters = [
                    'house_type' => $array_sequence_of_parameters[$key]['house_type'],
                    'floorcount' => $array_sequence_of_parameters[$key]['floorcount'],
                    'count_of_matches' => $count_of_matches,
                    'id_address' => $id_addressess[0]
                ];


            }

        }
        // теперь обрабатываем этот массив сортируем чтобы меньщие варианты были счерху, переиндексируем и берем первый элемент
        ArrayHelper::multisort($new_array_sequence_of_parameters, ['count_of_matches'], [SORT_ASC]);

        // my_var_dump($array_sequence_of_parameters);
        // выводим массив который стал
        $this->log .= \Yii::$app->view->render('@app/views/my-debug/_modal_info',
            ['icon' => 'array_finish', 'message' => json_encode($new_array_sequence_of_parameters, true)]);

        if ($new_array_sequence_of_parameters['id_address']) {

            return $new_array_sequence_of_parameters;
        } // если ничего нет массиве возвращаем false ;
        else {
            $this->log .= span('FAILURE', 'alert') . " SEARCH IN " . span($type_haystack) . " <br>";
            return false;
        }

    }


    public
    function GenerateAddresses($house_type, $floorcount)
    {

        // единственный глобальный метод генерации вариантов доступных адресов  в зависимоти от приходящих параметров
        return Addresses::find()
            ->Where(['in', 'precision_yandex', ['exact', 'number', 'near']])
            ->andWhere(['<>', 'street', ''])
            ->andFilterWhere(['street' => $this->TrimmedThoroughfareName])
            ->andFilterWhere(['AdministrativeAreaName' => $this->AdministrativeAreaName])
            ->andfilterwhere(['house' => $this->house])
            ->andfilterwhere(['house_type' => $house_type])
            ->andfilterwhere(['locality' => $this->locality])
            ->andfilterwhere(['floorcount' => $floorcount])
            ->andFilterWhere(['<>', 'street', '-'])
            ->groupBy('street, house, hull')
            ->orderBy('street DESC, house DESC, hull DESC')// это чтобы проверка на ддом без корпуса осуществлялась в самом конце
            ->all();
    }

    public
    function SearchAddressPattern($address, $type_haystack, $haystack)
    {
        $pattern = $address->pattern;
        if ((mb_strtolower($haystack)) and ($pattern)) {

            if (preg_match($pattern, mb_strtolower($haystack), $output)) {
                $this->log .= span("!!!!! нашли адрес (в " . $type_haystack . ") " . $address->address);

                return $address->id;

            }
            // if (Yii::$app->controller->action->id == 'geocodetion-test') echo $message;
        }


        return false;

    }


}