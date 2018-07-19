<?php

namespace app\models;

use app\models\ChromeDriver\MyChromeDriver;
use app\models\ParsingModels\Parsing;
use app\models\ParsingModels\ParsingSync;
use app\models\SalefiltersModels\SaleFiltersOnBlack;
use app\models\SalefiltersModels\SaleFiltersOnControl;
use Yii;

use yii\db\Expression;
use app\components\SaleWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\WebDriverKeys;
use app\utils\MyCurl;

/**
 * This is the model class for table "control".
 *
 * @property integer $id
 * @property string $fed_okrug
 * @property string $region
 * @property string $type_of_region
 * @property string $oblast_rus
 * @property string $region_rus
 * @property integer $max_step
 * @property integer $status
 * @property integer $is_analized
 * @property string $info_array
 */
class Control extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'control';
    }

    const P_SYNC = 1;
    const P_NEW = 2;
    const P_DET = 3;
    const P_APHONES = 4;
    const GEO = 5;
    const PROCC = 6;
    const ANALISYS = 7;
    const SIMILAR = 8;
    const DOWN_SYNC = 9;
    const UP_SYNC = 10;

    public static function mapTypesControls()
    {
        return [
            self::P_SYNC => 'P_SYNC',
            self::P_NEW => 'P_NEW',
            self::P_DET => 'P_DET',
            self::P_APHONES => 'P_APHONES',
            self::GEO => 'GEO',
            self::PROCC => 'PROCC',
            self::ANALISYS => 'ANALISYS',
            self::SIMILAR => 'SIMILAR',
            self::DOWN_SYNC => 'DOWN_SYNC',
            self::UP_SYNC => 'UP_SYNC',
        ];
    }

    public static function TypesControls()
    {
        return [
            self::P_SYNC,
            self::P_NEW,
            self::P_DET,
            self::P_APHONES,
            self::GEO,
            self::PROCC,
            self::ANALISYS,
            self::SIMILAR,
            self::DOWN_SYNC,
            self::UP_SYNC,
        ];
    }


    const STATUS_STOP_PARSING = 1;
    const STATUS_ACTIVE_PARSING = 0;
    const STATUSES = [
        Control::STATUS_ACTIVE_PARSING => "ACTIVE",
        Control::STATUS_STOP_PARSING => "STOP",
    ];


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fed_okrug', 'region', 'type_of_region', 'region_rus', 'status', 'is_analized', 'oblast_rus'], 'required'],
            [['max_step', 'status', 'is_analized'], 'integer'],
            [['fed_okrug'], 'string', 'max' => 15],
            [['log'], 'string', 'max' => 25],
            [['region', 'region_rus'], 'string', 'max' => 40],
            [['info_array'], 'string'],
            [['type_of_region', 'coords_x', 'coords_y'], 'string', 'max' => 10],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fed_okrug' => 'ФО',
            'region' => 'Region',
            'type_of_region' => 'type_region',
            'region_rus' => 'Region Rus',
            'max_step' => 'Max Step',
            'parsing_history_status' => '1-parsing-history, 2-fillparameters, 3 - fillgrossarea, 4-analiting, 5-normal',
            'is_analized' => 'Is Analized',
            'status' => 'status',
            'parsing_status' => 'parsing_status',
        ];
    }

    public function change_status($status)
    {
        $module = Control::findOne($this->id);
        $module->status = $status;
        if ($module->save()) return true;


    }

    public function log($log)
    {
        $module = self::findOne($this->id);
        $module->log = (string)$log;
        if ($module->save()) return true;


    }


    public static function mainScript()
    {
        /* @var $agentpro AgentPro */
        /* @var $module Control */

        Sessions::check();
        Yii::$app->cloud->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        $modules = Control::find()->where(['<>', 'status', 9])->all();
        $agentpro = AgentPro::getActive();

        foreach ($modules as $module) {
// присваиваем определяем регион в котором работаем на данный момент
            \Yii::$app->params['module'] = $module;

            if ($module->status != 9) {
                $module->setPrefixies($module->region);
                // удаляем сброшенные контроллеры
                ControlParsing::deleteMissedControllers(60);
                ControlParsing::deleteOverControllersNew(ControlParsing::ACTIVE);
                ControlParsing::deleteOverControllersNew(ControlParsing::SUCCESS);
                ControlParsing::deleteOverControllersNew(ControlParsing::BROKEN);
                ControlParsing::closeBroken();
                info("CONSOLE SCRIPT " . $module->region);

                if (!$module->parsing_status) {
                    info("PARSING IS NOT STOPPED");
                    if ($agentpro->status_parsingsync) {
                        if ($module->MyParsingSyncNewer() !== false) continue;
                    }
                    if ($agentpro->status_parsing_avito_phones) {
                        if ($module->ParsingAvitoPhones1() !== false) continue;
                    }

                    if ($agentpro->status_detailed_parsing) {
                        if ($module->DetaledParsing(50) !== false) continue;
                    }
                    if ($agentpro->status_parsing_new) {
                        if ($module->MyParsingNewNewer() !== false) continue;
                    }
                } else {
                    info("PARSING IS STOPPED", DANGER);
                }
                $module->save();


            }

        }
    }

    public static function mainScriptCloud()
    {
        /* @var $agentpro AgentPro */
        /* @var $module Control */

        Yii::$app->cloud->createCommand('SET SESSION wait_timeout = 28800;')->execute();
        // Yii::$app->cloud->createCommand('set global max_allowed_packet=1000000000;')->execute();

        $modules = Control::find()->where(['<>', 'status', 9])->all();

        $agentpro = AgentPro::getActive();

        foreach ($modules as $module) {
            info("CONSOLE SCRIPT " . $module->region);

            // присваиваем определяем регион в котором работаем на данный момент
            \Yii::$app->params['module'] = $module;

            if ($module->status != 9) {
                $module->setPrefixies($module->region);
                // удаляем сброшенные контроллеры
                ControlParsing::deleteMissedControllers(60);
                ControlParsing::deleteOverControllersNew(ControlParsing::ACTIVE);
                ControlParsing::deleteOverControllersNew(ControlParsing::SUCCESS);
                ControlParsing::deleteOverControllersNew(ControlParsing::BROKEN);
                if ($agentpro->status_geocogetion) {
                    if ($module->geocodetion(100) !== false) continue;
                }
                if ($agentpro->status_processing) $module->processing(50);
                if ($agentpro->status_similar_check) $module->similarCheck(50);
                if ($agentpro->status_analizing) $module->load_sale_statistic(50);
                if ($agentpro->status_sync) $module->Synchronisation(500);

                $module->save();


            }


        }
    }

    public function ParsingControl()
    {
        $count_active_process = 0;
        sleep(5);
        $response = shell_exec('tasklist | find /I "chrome.exe"');
        // my_var_dump($response);
        preg_match_all("/chrome.exe/", $response, $output);
        $count_of_process = count($output[0]) / 6;
        // my_var_dump($output);
        $active_process = ParsingControl::find()->where(['status' => ParsingControl::STATUS_ACTIVE])->andwhere(['in', 'type', [self::p_SYNC, 'PARSYNG_NEW', 'DETAILED_PARSING', 'PARSING_AVITO_PHONES']])->all();
        if ($active_process) {
            info(count($active_process) . " ACTIVE PARSING PROCESS");
            foreach ($active_process as $process) {
                info(" ACTIVE " . $process->type);
                $count_active_process = count($active_process);
            }

        } else {
            info('NO  AVTIVE PARSING PROCESS', 'alert');
            // info("НО ЕСТЬ " . $active_process . " ПОВИСШИХ ПРОЦЕССА ", 'alert');
            $lost_process = $count_of_process - $count_active_process;
            info(" EXIST " . $lost_process . " LOST PROCESS ", 'alert');
            if ($lost_process > 20) {
                //
                //
                $response = shell_exec('taskkill /IM "chrome.exe"');
                info(" DETELING THEM", 'alert');
                return true;
            }


        }
        info(" ACTIVE PROCESS " . $count_of_process, 'alert');
        if ($count_of_process >= 12) {

            $active_controls = Control::find()->where(['status' => 2])->all();
            if ($active_controls) {
                foreach ($active_controls as $control) {
                    $control->parsing_status = Control::STATUS_STOP_PARSING;
                    info("ДЕЛАЕМ ПАУЗУ В ПАРСИНГЕ" . $count_of_process, 'alert');
                    Renders::SystemMail("ДЕЛАЕМ ПАУЗУ В ПАРСИНГЕ");
                }
            }
        }
        $lost_process = $count_of_process - $count_active_process;
        return true;


        info(" count = " . count($output[0]));


    }

    /*
     * установке всех префиксов для таблиц*/
    public function setPrefixies($prefix)
    {
        Sale::setTablePrefix($prefix);
        Synchronization::setTablePrefix($prefix);
        SaleAnaliticsAddress::setTablePrefix($prefix);
        SaleAnaliticsSameAddress::setTablePrefix($prefix);
        SaleAnalitics::setTablePrefix($prefix);
        SaleFiltersOnBlack::setTablePrefix($prefix);
        SaleFiltersOnControl::setTablePrefix($prefix);
        Agents::setTablePrefix($prefix);
        Addresses::setTablePrefix($prefix);
        RealTags::setTablePrefix($prefix);
        SaleFilters::setTablePrefix($prefix);
        SaleLists::setTablePrefix($prefix);
        SmsApi::setTablePrefix($prefix);

    }


    public function parsing_and_import_addresses()
    {
        $prefix = $this->region;
        $city = $this->region_rus;

        $string = file_get_contents("c:\\" . $prefix . ".json");
        $json_array = json_decode($string);
        echo "<pre>";
        // var_dump($json_array->rows);
        echo "</pre>";
        foreach ($json_array->rows as $row) {

            //   echo "id=" . $row->rownumber . " " . $row->address . " " . $row->year . " " . $row->floors . " <a href=\"http://dom.mingkh.ru" . $row->url . "\"> url </a> </br>";

            Addresses::setTablePrefix($prefix);
            $new_address = new Addresses;
            $new_address->id = $row->rownumber;
            $new_address->coords_x = '';
            $new_address->coords_y = '';
            $new_address->street = '';
            $new_address->house = '';
            $new_address->hull = '';
            $new_address->locality = '';
            $new_address->district = '';
            $new_address->house_type = 0;
            $new_address->address_string_variants = '';
            $new_address->precision_yandex = '';
            $new_address->address_string_variants = '';
            $new_address->fullfilled = 0;
            $new_address->status = 0;
            $new_address->address = $row->address;
            $new_address->year = $row->year;
            $new_address->floorcount = $row->floors;
            $new_address->url = "http://dom.mingkh.ru" . $row->url;
            if ($new_address->save()) {
                echo "<br> успешно импортирован адрес" . $row->address;


            }
        }

    }

    public function filling_addresses()
    {
        $prefix = $this->region;
        $city = $this->region_rus;
        //  Addresses::setTablePrefix('Pskov');
        // ищем адреса где не прописаны параметры
        $addresses = Addresses::find()
            ->where(['locality' => null])
            // ->andwhere(['precision_yandex' => 'exact'])
            //  ->andwhere(['<>', 'house', ''])
            //  ->orderBy(new Expression('rand()'))
            ->limit(700)
            ->all();
//$driver = MyChromeDriver::Open();
        echo "elements to fill geocodate data  = ";
        $count_lost = Addresses::find()
            ->andwhere(['locality' => null])->count();
        echo $count_lost . "<br>";

        foreach ($addresses as $address) {
            echo "<br>";
            // делаем запрос к yandex maps api
            //  $fulladdress = $city . " , " . $address->address;
            $fulladdress = $address->address;
            echo "in " . $fulladdress;
            $xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($fulladdress) . '&results=1';
            echo "<a href=" . $xml_string . "> link </a>";
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
            $found_Province = $pq->find('Province')->text();


            $found_name = $found_ThoroughfareName . ", " . $found_PremiseNumber;


            $trimmed_street = trim(preg_replace(Geocodetion::STREET_VARIANTS, "", $found_ThoroughfareName));

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

            // когда мы получили улицу номер дома и корпус ищем эти данные в таблице
            $edited_address = Addresses::findOne($address->id);
            // $edited_address->address = $found_name;
            //$edited_address->coords_x = $coords_array[0];
            // $edited_address->coords_y = $coords_array[1];
            // $edited_address->street = $trimmed_street;
            // $edited_address->house = $house;
            // $edited_address->hull = $hull;
            // $edited_address->district = $found_district;
            // $edited_address->locality = $found_locality;
            // $edited_address->precision_yandex = $precision;
            $edited_address->province = $found_Province;
            if (!$edited_address->save()) var_dump($edited_address->errors);
        }


    }

    public function processing($limit = 50)
    {
        $type = self::PROCC;

        // берем объекты готовые к обработке
        $sales = $this->getREADY($type, $limit);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);
        foreach ($sales as $key => $sale) {
            if ($key % 30 == 0) {
                ControlParsing::updatingTime($id_parsingController);
            }
            $sale = Synchronization::findOne($sale->id);
            // изменилась только цена то обработку на телефон и похожие вариенты не проводим
            if ($sale->status == Sale::NEW_ITEM) {
                $sale->checkForAgents();
                $sale->setProccessingLog(Sale::CHECK_FOR_AGENTS);
            }

            $sale->SalefiltersCheck();
            $sale->setProccessingLog(Sale::SALEFILTER_CHECKED);
            $sale->changingStatuses('PROCESSED');
            if (!$sale->save()) my_var_dump($sale->getErrors());

            //  echo " <br>" . $sale->id;
        }
        ControlParsing::updating($id_parsingController);


        return true;


    }

    public function similarCheck($limit = 100)
    {
        $type = self::SIMILAR;

        // берем объекты готовые к обработке
        $sales = $this->getREADY($type, $limit);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);
        foreach ($sales as $key => $sale) {
            if ($key % 20 == 0) {
                ControlParsing::updatingTime($id_parsingController);
            }
            // изменилась только цена то обработку на телефон и похожие вариенты не проводим
            $sale->similarCheckNewer();
            $sale->setProccessingLog(Sale::SIMILARED);
            // $sale->save();


            //  echo " <br>" . $sale->id;
        }
        ControlParsing::updating($id_parsingController);


        return true;


    }


    public function geocodetion($limit = 20)
    {
        /* @var $sale Sale */
        $type = self::GEO;
        $start_time = time();
        // берем объекты готовые к обработке
        $sales = $this->getREADY($type, $limit);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);

        foreach ($sales as $key => $sale) {
            if ($key % 20 == 0) {
                ControlParsing::updatingTime($id_parsingController);
            }
            echo $sale->renderLong_title();
            $sale->geocodate();
            echo " <br>";
            // echo Html::a('OneGeocodation', ['my-debug/geocodation', 'id' => $sale->id]);
            // загружает в модель sale данные после геокодирования
            info($sale->renderLong_title());
            info(Geocodetion::GEOCODATED_STATUS_ARRAY[$sale->geocodated]);
            $sale->changingStatuses('GEOCODATED');
            $sale->save();


        }
        ControlParsing::updating($id_parsingController);


        if ((time() - $start_time) < 15) return false;
        return true;


    }

    public function load_sale_statistic($limit = 20)
    {
        $type = self::ANALISYS;
        // берем объекты готовые к обработке
        $sales = $this->getREADY($type, $limit);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);
//        echo Synchronization::Counts(Synchronization::find(), [
//            'load_analized' => [1, 2, 3],
//        ]);
        foreach ($sales as $sale) {
            if ($sale->is_full()) {
                $sale->LoadStatistic();

            }
            $sale->changingStatuses('LOAD_ANALIZED');
            $sale->save();
        }
        ControlParsing::updating($id_parsingController);
//        echo Synchronization::Counts(Synchronization::find(), [
//            'load_analized' => [1, 2, 3, 4],
//        ]);


    }


    public function MyParsingSync()
    {
        $type = self::p_SYNC;
        $config = $this->getREADY($type);

        if (!$config) {

            if ((time() - $this->last_check_of_lost) > ParsingConfiguration::PERIOD_OF_CHECK * 60 * 60) {
                $update = Control::findOne($this->id);
                $update->last_check_of_lost = time();
                $update->save();
                echo Synchronization::CheckLostLinks();
                // раз в сутки скидываем контроллер
                ControlParsing::resetParsingController();
            }
            if ((time() - $this->last_check_of_die) > ParsingConfiguration::PERIOD_CHECK_OF_DEATH * 60 * 60) {
                $update = Control::findOne($this->id);
                $update->last_check_of_die = time();
                $update->save();
                echo Synchronization::CheckDiedLinks();
            }
            return false;
        }


//        echo Synchronization::Counts(Synchronization::find(), [
//            'disactive' => [0, 1, 2, 3, 4, 5, 6, 7, 8],
//
//        ]);

        $driver = MyChromeDriver::Open();
        $driver->getMyIp();


        $id_parsingController = ControlParsing::create($type, $config, $driver->ip);

        if ($config->id_sources == 1) {
            $driver->get('https://irr.ru/');
            sleep(2);
        } elseif ($config->id_sources == 5) {
            $driver->get('https://novgorod.cian.ru/');
            if ($this->Captcha($driver) === false) return false;
            sleep(2);
        }

        $setting = ParsingConfiguration::LoadSettings($config);

        echo "<br> start link = <a href='" . $config->start_link . "'> " . $config->start_link . " </a>";
        echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
        // открываем ссылку ( если нет открывается то выходим)
        $response = $driver->get($config->start_link);
        // распарсиваем ресурс стартовой страницы

        $pq = \phpQuery::newDocument($response->getPageSource());
        $total_count = ParsingExtractionMethods::extractNumbersFromString($setting['total_count']['pattern'], $pq->find($setting['total_count']['div'])->text());

        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / $setting['items_per_page']) + 1;
        echo "<br> pages=" . $pages;
        //return true;
        // пока так !
        if (true) {
            // echo "<br> start_page_number=" . $config->success_stop;
            $limit_pages = $config->success_stop + ParsingConfiguration::PAGES_LIMIT;
            if ($limit_pages > $pages) $limit_pages = $pages;
            info("обрабатываем с " . $config->success_stop . " по " . $limit_pages . " страницы");
            sleep(rand(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD / 2, ParsingConfiguration::ONE_PAGE_WAITING_PERIOD));
            // ждем пока загрузится элемент div с табличными данными
            //   $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
            //  $driver->wait(10, 1000)->until(
//                WebDriverExpectedCondition::visibilityOf($element)
//            );
            // обнуление счетчиков
            $CountCheckedPage = 0;
            $ItemsCounter = 0;
            $counterADDRESS_CHANGE = 0;
            $counterPRICE_CHANGED = 0;
            $counterDATESTART_UPDATED = 0;
            $counterNEW = 0;
            $counterTHE_SAME = 0;

//            $cashed_items = Synchronization::getCachedCategory($config->id);
//            info("COUNT_CASHED_ITEMS =".count($cashed_items));

            // пробегаемся по страницам для сбора, короткой информации

            for ($i = ($config->success_stop + 1); $i <= $pages; $i++) {
                // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                if ($i != 1) {
                    // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                    $url = ParsingConfiguration::RuleOfUrl($config, $i);
                    echo "<br> парсим страницу" . $url;
                    $driver->get($url);
                    // ждем на странице какое-то время имитируя пользователя
                    sleep(rand(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD / 2, ParsingConfiguration::ONE_PAGE_WAITING_PERIOD));
                    // ждем пока загрузится элемент div с табличными данными
//                    $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
//                    $driver->wait(10, 1000)->until(
//                        WebDriverExpectedCondition::visibilityOf($element)
//                    );
                    // распарсиваем полученный ресурс страницы
                    $pq = \phpQuery::newDocument($driver->getPageSource());


                } else echo "<br> парсим страницу" . $config->start_link;

                // echo $driver->getPageSource();

                $tree = str_get_html($driver->getPageSource());
                // if ($config->source == 'yandex') sleep(rand(10, 13));
                // берем контейнер с вариантами
                if ($config->id_sources == 1) $pq = $pq->find('.js-productGrid')->eq(0);
                if ($config->id_sources == 5) {
                    Cian::loadTableClasses($driver->getPageSource());
                    $classDiv = Cian::$tableContainerDivClass;
                    $div_items = $pq->find("." . $classDiv);
                } else {
                    $div_items = $pq->find($setting['container']);
                }


                // $div_items = $pq->find($setting['container']);
                if (count($div_items) == 0) {
                    info("НА СТРАНИЦЕ НЕЧЕГО ПАРСИТЬ ВЫХОДИМ");
                    $config->UpdateAndSave($i, $pages, $ItemsCounter);
                    break;
                }

                echo " <br> на странице " . count($div_items) . " вариантов";
                // прогоняем его
                foreach ($div_items as $div_item) {
                    $ItemsCounter++;
                    $pq_div = pq($div_item);
                    // распарсиваем mini_container

                    if ($config->id_sources == 5) {
                        $response = Cian::extractTableContainerData($pq_div);
                    } else $response = ParsingExtractionMethods::ExtractTablesData($config, $pq_div);

                    // делаем синхронизацию полученного варианта ( new, update)
                    //
                    //  $SynchResponse = Synchronization::TODO($response,$config->id);
//                    $id_in_source = $response['id'];
//                    echo "<br>ID _IN SOURCE " . $id_in_source;
//
//                    if ($cashed_items[$id_in_source]) {
//                        info("БАЗЕ РАНЕЕ БЫЛ ЭТОТ ВАРИАНТ = " . $cashed_items[$id_in_source]['url']);
//                        $SynchResponse = Synchronization::TODO_NEW($response, $cashed_items[$id_in_source]);
//                        Synchronization::updateAll(['date_of_check' => time()], ['id' => $cashed_items['id']]);
//                    } else {
//                        info("БАЗЕ РАНЕЕ НЕ БЫЛ ЭТОТ ВАРИАНТ = " . $cashed_items[$id_in_source]['url'], 'danger');
//                        $SynchResponse = Synchronization::TODO($response);
//                    }
                    $SynchResponse = Synchronization::TODO($response);

                    if (preg_match("/ADDRESS_CHANGED/", $SynchResponse)) $counterADDRESS_CHANGE++;
                    if (preg_match("/PRICE_CHANGED/", $SynchResponse)) $counterPRICE_CHANGED++;
                    if (preg_match("/DATESTART_UPDATED/", $SynchResponse)) $counterDATESTART_UPDATED++;
                    if (preg_match("/NEW/", $SynchResponse)) $counterNEW++;
                    if (preg_match("/THE_SAME/", $SynchResponse)) $counterTHE_SAME++;
                    //  echo "<hr>";
                }


                // update счетчика обработанных страниц
                $CountCheckedPage++;
                // если мы обработали страниц больше лимита то выходим
                if ($CountCheckedPage >= ParsingConfiguration::PAGES_LIMIT) break;
                // делаем update текущей конфигурации исходя из страниц на которых мы остановились
                $config->UpdateAndSave($i, $pages, $ItemsCounter);
            }
            $config->UpdateAndSave($i, $pages, $ItemsCounter);
            $current_time = time();
            echo " <h3> удалось сверить " . $ItemsCounter . " объектов на " . $CountCheckedPage . " страницах, за " . ($current_time - $time_start) . " секунд</h3>";
            // $driver->quit();

        }
        $counts_array = [
            'ADDRESS_CHANGED' => $counterADDRESS_CHANGE,
            'PRICE_CHANGED' => $counterPRICE_CHANGED,
            'DATESTART_UPDATED' => $counterDATESTART_UPDATED,
            'NEW' => $counterNEW,
            'THE_SAME' => $counterTHE_SAME,
        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));

    }

    public function MyParsingSyncNewer()
    {
        /* @var $config ParsingConfiguration */
        /* @var $agentpro AgentPro */

        $agentpro = \Yii::$app->params['agent_pro'];


        $time_start = time();
        $type = self::P_SYNC;
        $config = $this->getREADY($type);
        if (!$config) {

            return false;
        }

        $id_parsingController = ControlParsing::create($type, $config, $driver->ip);
        $start_success_stop = $config->success_stop;
        $driver = MyChromeDriver::Open(MyChromeDriver::CURRENT_PROXY);
        if ($driver == MyChromeDriver::ERROR_LIMIT) {
            Notifications::VKMessage(" ONE SERVER SESSIONS LIMIT");
            return false;
        }
        $cashed_items = Synchronization::getCachedCategory($config->id);
        $checked_ids = [];
        info(" THIS CATEGORY HAD " . count($cashed_items) . " ITEMS INSIDE YOURSELF", DANGER);

        if ($config->id_sources == 1) {
            $driver->get('https://irr.ru/');
            sleep(2);
        } elseif ($config->id_sources == 5) {
            $driver->get('https://novgorod.cian.ru/');
            if ($this->Captcha($driver) === false) return false;
            sleep(2);
        }
        echo span("START_LINK -->" . Html::a($config->start_link, $config->start_link, ['target' => '_blank']));

        // echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
        // открываем ссылку ( если нет открывается то выходим)
        $driver->get($config->start_link);
        $pageSource = $driver->getPageSource();

        // проводим проверку селекторов
        Selectors::loadTableClasses($pageSource, $config->source->id);
        Selectors::loadStatClasses($pageSource, $config->source->id);

        // распарсиваем ресурс стартовой страницы
        $pq_page = \phpQuery::newDocument($pageSource);
        $total_count = Parsing::getTotalCount($pq_page, $config->source->id);

        info("total_count=" . $total_count);
        $total_pages = floor($total_count / $config->source->items_per_page) + 1;
        info("total pages=" . $total_pages);
        //return true;
        // пока так !
        if (true) {
            $limit_pages = $config->success_stop + $agentpro->page_limit;
            if ($limit_pages > $total_pages) $limit_pages = $total_pages;
            info("PARSING FROM " . $config->success_stop . " TO " . $limit_pages . " PAGES OF ID_CATEGORY = " . $config->id);
            $config->sleeping();


            // обнуление счетчиков
            $CountCheckedPage = 0;
            $ItemsCounter = 0;
            $counterADDRESS_CHANGE = 0;
            $counterPRICE_CHANGED = 0;
            $counterDATESTART_UPDATED = 0;
            $counterNEW = 0;
            $counterTHE_SAME = 0;

//            $cashed_items = Synchronization::getCachedCategory($config->id);
//            info("COUNT_CASHED_ITEMS =".count($cashed_items));

            // пробегаемся по страницам для сбора, короткой информации

            for ($page = ($config->success_stop + 1); $page <= $total_pages; $page++) {

                // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                if ($page != 1) {
                    // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                    $url = ParsingConfiguration::RuleOfUrl($config, $page);
                    info(" GETTING PAGE '" . $url . "'");
                    $driver->get($url);
                    // ждем на странице какое-то время имитируя пользователя
                    $config->sleeping();

                    // распарсиваем полученный ресурс страницы
                    $pq_page = \phpQuery::newDocument($driver->getPageSource());


                } else info("SCRAPING PAGE " . Html::a($config->start_link, $config->start_link, ['target' => '_blank']));

                // echo $driver->getPageSource();

                //  $tree = str_get_html($driver->getPageSource());
                // if ($config->source == 'yandex') sleep(rand(10, 13));
                // берем контейнер с вариантами
                if ($config->id_sources == 1) $pq_page = $pq_page->find('.js-productGrid')->eq(0);

                if ($selector = Selectors::find()->where(['id_sources' => $config->source->id])->andWhere(['type' => Selectors::TYPE_TABLE_CONTAINER])->one()) {
                    $div_selector = $selector->selector;
                } else {
                    INFO(" CANNOT FIND SELECTOR RECORD", DANGER);
                    die;
                }
                info(" DIV SELECTOR = " . $div_selector);
                $pq_containers = $pq_page->find("." . $div_selector);

                if (count($pq_containers) == 0) {
                    info("REFRESHING", SUCCESS);
                    $driver->navigate()->refresh();
                }
                $pq_page = \phpQuery::newDocument($driver->getPageSource());
                $pq_containers = $pq_page->find("." . $div_selector);
                if (count($pq_containers) == 0) {
                    info("REFRESHING", SUCCESS);
                    $driver->navigate()->refresh();
                }
                $pq_page = \phpQuery::newDocument($driver->getPageSource());
                $pq_containers = $pq_page->find("." . $div_selector);

                if (count($pq_containers) == 0) {
                    info("NOTHING TO SCRAPE ... EXITING", 'danger');
                    $config->UpdateAndSave($page, $total_pages, $ItemsCounter);
                    break;

                } else {
                    info(count($pq_containers) . " items on current page");
                }

                // пробегаемся по контейнерам
                if ($pq_containers) {
                    $toUpdateCheck = [];
                    foreach ($pq_containers as $pq_container) {
                        $ItemsCounter++;

                        $parsing = new ParsingSync();
                        $parsing->extractTableData($config->source->id, pq($pq_container));
                        $parsing->loggedValidate($config->source->id);

                        $active_item = $cashed_items[$parsing->id_in_source];
                        //  my_var_dump($parsing->toArray());

                        if (!in_array($active_item->id_in_source, $checked_ids)) $SynchResponse = Synchronization::TODO_NEW($parsing, $config, ['active_item' => $active_item]);
                        else {
                            info(" THIS ITEM HAS BEEN CHECKED YET IN THIS CHECKING...SKIP", DANGER);
                            continue;
                        }

                        $checked_ids[] = $SynchResponse[0];
                        $SynchResponse = $SynchResponse[1];

                        if (preg_match("/ADDRESS_CHANGED/", $SynchResponse)) $counterADDRESS_CHANGE++;
                        if (preg_match("/PRICE_CHANGED/", $SynchResponse)) $counterPRICE_CHANGED++;
                        if (preg_match("/DATESTART_UPDATED/", $SynchResponse)) $counterDATESTART_UPDATED++;
                        if (preg_match("/NEW/", $SynchResponse)) $counterNEW++;
                        if (preg_match("/THE_SAME/", $SynchResponse)) {
                            $counterTHE_SAME++;
                            if ($active_item) $toUpdateCheck[] = $active_item['id'];
                        }
                        //  echo "<hr>";
                    }


                    if ($toUpdateCheck) {
                        Synchronization::updateAll(['date_of_check' => time()], ['in', 'id', $toUpdateCheck]);
                        Sale::updateAll(['date_of_check' => time()], ['in', 'id', $toUpdateCheck]);
                        info(" UPDATE DATE_OF_CHECK TO " . count($toUpdateCheck) . " THE_SAME ITEMS", SUCCESS);
                        info(implode(",", $toUpdateCheck));
                    }

                    // update счетчика обработанных страниц
                    $CountCheckedPage++;
                    // если мы обработали страниц больше лимита то выходим
                    if ($CountCheckedPage >= $agentpro->page_limit) break;
                    // делаем update текущей конфигурации исходя из страниц на которых мы остановились
                    $config->UpdateAndSave($page, $total_pages, $ItemsCounter);

                }
                $config->UpdateAndSave($page, $total_pages, $ItemsCounter);
                if ($config->success_stop <= $page) {
                    if (!Parsing::IsInAvailablePages($page + 1, \phpQuery::newDocument($driver->getPageSource()), $config->source->id)) {
                        $total_pages = $page;
                        info(" Page " . ($page + 1) . " is OUT OF AVAILABLE PAGES", WARNING);
                        info(" BREAK LOOP", DANGER);
                        break;
                    }
                }

                ControlParsing::updatingTime($id_parsingController);
            }
            $config->UpdateAndSave($page, $total_pages, $ItemsCounter);
            $current_time = time();
            info($ItemsCounter . " ITEMS WERE CHECKED ON " . $CountCheckedPage . " PAGES DURING " . ($current_time - $time_start) . " SECONDS");

        }
        $counts_array = ['ADDRESS_CHANGED' => $counterADDRESS_CHANGE,
            'PRICE_CHANGED' => $counterPRICE_CHANGED,
            'DATESTART_UPDATED' => $counterDATESTART_UPDATED,
            'NEW' => $counterNEW,
            'THE_SAME' => $counterTHE_SAME,];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));

    }

    public
    function MyParsingSync1()
    {
        $type = self::p_SYNC;
        $config = $this->getREADY($type);

        if (!$config) {

            if ((time() - $this->last_check_of_lost) > ParsingConfiguration::PERIOD_OF_CHECK * 60 * 60) {
                $update = Control::findOne($this->id);
                $update->last_check_of_lost = time();
                $update->save();
                echo Synchronization::CheckLostLinks();
                // раз в сутки скидываем контроллер
                ControlParsing::resetParsingController();
            }
            if ((time() - $this->last_check_of_die) > ParsingConfiguration::PERIOD_CHECK_OF_DEATH * 60 * 60) {
                $update = Control::findOne($this->id);
                $update->last_check_of_die = time();
                $update->save();
                echo Synchronization::CheckDiedLinks();
            }
            return false;
        }


//        echo Synchronization::Counts(Synchronization::find(), [
//            'disactive' => [0, 1, 2, 3, 4, 5, 6, 7, 8],
//
//        ]);

        $driver = MyChromeDriver::Open();
        $driver->getMyIp();


        $id_parsingController = ControlParsing::create($type, $config, $driver->ip);

        if ($config->id_sources == 1) {
            $driver->get('https://irr.ru/');
            sleep(2);
        } elseif ($config->id_sources == 5) {
            $driver->get('https://novgorod.cian.ru/');
            if ($this->Captcha($driver) === false) return false;
            sleep(2);
        }
        $setting = ParsingConfiguration::LoadSettings($config);

        echo "<br> start link = <a href='" . $config->start_link . "'> " . $config->start_link . " </a>";
        echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
        // открываем ссылку ( если нет открывается то выходим)
        $response = $driver->get($config->start_link);
        // распарсиваем ресурс стартовой страницы

        $pq = \phpQuery::newDocument($response->getPageSource());
        $total_count = ParsingExtractionMethods::extractNumbersFromString($setting['total_count']['pattern'], $pq->find($setting['total_count']['div'])->text());

        echo "<br> total_count=" . $total_count;
        $pages = floor($total_count / $setting['items_per_page']) + 1;
        echo "<br> pages=" . $pages;
        //return true;
        // пока так !
        if (true) {
            // echo "<br> start_page_number=" . $config->success_stop;
            $limit_pages = $config->success_stop + ParsingConfiguration::PAGES_LIMIT;
            if ($limit_pages > $pages) $limit_pages = $pages;
            info("обрабатываем с " . $config->success_stop . " по " . $limit_pages . " страницы");
            sleep(rand(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD / 2, ParsingConfiguration::ONE_PAGE_WAITING_PERIOD));
            // ждем пока загрузится элемент div с табличными данными
            //   $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
            //  $driver->wait(10, 1000)->until(
//                WebDriverExpectedCondition::visibilityOf($element)
//            );
            // обнуление счетчиков
            $CountCheckedPage = 0;
            $ItemsCounter = 0;
            $counterADDRESS_CHANGE = 0;
            $counterPRICE_CHANGED = 0;
            $counterDATESTART_UPDATED = 0;
            $counterNEW = 0;
            $counterTHE_SAME = 0;

            // пробегаемся по страницам для сбора, короткой информации

            for ($i = ($config->success_stop + 1); $i <= $pages; $i++) {
                // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                if ($i != 1) {
                    // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                    $url = ParsingConfiguration::RuleOfUrl($config, $i);
                    echo "<br> парсим страницу" . $url;
                    $driver->get($url);
                    // ждем на странице какое-то время имитируя пользователя
                    sleep(rand(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD / 2, ParsingConfiguration::ONE_PAGE_WAITING_PERIOD));
                    // ждем пока загрузится элемент div с табличными данными
//                    $element = $driver->findElement(WebDriverBy::className($setting['data_container']));
//                    $driver->wait(10, 1000)->until(
//                        WebDriverExpectedCondition::visibilityOf($element)
//                    );
                    // распарсиваем полученный ресурс страницы
                    $pq = phpQuery::newDocument($driver->getPageSource());
                    if (!Parsing::IsInAvailablePages($i, $pq, $config->id_sources)) break;
                } else echo "<br> парсим страницу" . $config->start_link;
                // if ($config->source == 'yandex') sleep(rand(10, 13));
                // берем контейнер с вариантами
                if ($config->id_sources == 1) $pq = $pq->find('.js-productGrid')->eq(0);
                $div_items = $pq->find($setting['container']);
                if (count($div_items) == 0) {
                    $config->UpdateAndSave($i, $pages, $ItemsCounter);
                    break;
                }

                echo " <br> на странице " . count($div_items) . " вариантов";
                // прогоняем его
                foreach ($div_items as $div_item) {
                    $ItemsCounter++;
                    $pq_div = pq($div_item);
                    // распарсиваем mini_container
                    $response = ParsingExtractionMethods::ExtractTablesData($config, $pq_div);

                    // делаем синхронизацию полученного варианта ( new, update)
                    $SynchResponse = Synchronization::TODO($response);
                    if (preg_match("/ADDRESS_CHANGED/", $SynchResponse)) $counterADDRESS_CHANGE++;
                    if (preg_match("/PRICE_CHANGED/", $SynchResponse)) $counterPRICE_CHANGED++;
                    if (preg_match("/DATESTART_UPDATED/", $SynchResponse)) $counterDATESTART_UPDATED++;
                    if (preg_match("/NEW/", $SynchResponse)) $counterNEW++;
                    if (preg_match("/THE_SAME/", $SynchResponse)) $counterTHE_SAME++;
                    //  echo "<hr>";
                }


                // update счетчика обработанных страниц
                $CountCheckedPage++;
                // если мы обработали страниц больше лимита то выходим
                if ($CountCheckedPage >= ParsingConfiguration::PAGES_LIMIT) break;
                // делаем update текущей конфигурации исходя из страниц на которых мы остановились
                $config->UpdateAndSave($i, $pages, $ItemsCounter);
                if (!Parsing::IsInAvailablePages($i, $pq, $config->id_source)) continue;
            }
            $config->UpdateAndSave($i, $pages, $ItemsCounter);
            $current_time = time();
            echo " <h3> удалось сверить " . $ItemsCounter . " объектов на " . $CountCheckedPage . " страницах, за " . ($current_time - $time_start) . " секунд</h3>";
            // $driver->quit();

        }
        $counts_array = [
            'ADDRESS_CHANGED' => $counterADDRESS_CHANGE,
            'PRICE_CHANGED' => $counterPRICE_CHANGED,
            'DATESTART_UPDATED' => $counterDATESTART_UPDATED,
            'NEW' => $counterNEW,
            'THE_SAME' => $counterTHE_SAME,
        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));

    }

    /*
     * метод для парсинга новых объявлений
     * */
    public
    function MyParsingNew()
    {
        $type = self::P_NEW;
        $configs = $this->getREADY($type);
        if (!$configs) return false;

//        echo Synchronization::Counts(Synchronization::find(), [
//            'disactive' => [0, 1, 2, 3, 4, 5, 6, 7, 8],
//
//        ]);

        $driver = MyChromeDriver::Open();
        $driver->getMyIp();
        $id_parsingController = ControlParsing::create($type, $configs, $driver->ip);
        $time_start = time();
        // $id_parsingController = ControlParsing::create($type, $configs);
        foreach ($configs as $config) {
            $config = ParsingConfiguration::findOne($config->id);
            $setting = ParsingConfiguration::LoadSettings($config);
            if ($config->id_sources == 1) {
                $driver->get('https://irr.ru/');
                sleep(2);
            } elseif ($config->id_sources == 5) {
                $driver->get('https://novgorod.cian.ru/');
                if ($this->Captcha($driver) === false) break;
                sleep(2);
            }

            $last_checked_ids = [];
            echo "<br> start link = <a href='" . $config->start_link . "'> " . $config->start_link . " </a>";
            echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
// открываем ссылку ( если нет открывается то выходим)
            $response = $driver->get($config->start_link);

            // распарсиваем ресурс стартовой страницы
            $pq = phpQuery::newDocument($response->getPageSource());
            $total_count = ParsingExtractionMethods::extractNumbersFromString($setting['total_count']['pattern'], $pq->find($setting['total_count']['div'])->text());
            //  echo "<br> total_count=" . $total_count;
            $pages = floor($total_count / $setting['items_per_page']) + 1;
            //  echo "<br> pages=" . $pages;
            info("обрабатываем с " . $config->success_stop . " по " . $pages . " страницы");

            // echo "<br> start_page_number=" . $config->success_stop;
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
                ControlParsing::updatingTime($id_parsingController);
            }
            info("COUNT NEW" . $new);
            // $last_checked_ids = unserialize($config->last_checked_ids);
            // if (count($last_checked_ids) > 0 ) echo " <h3>WAS 1</h3>" . implode(",", unserialize($config->last_checked_ids));
            //  $config->last_checked_ids = serialize(array_slice($last_checked_ids, 0, 10));
            //  echo " <h3>NOW 1</h3>" . implode(",", unserialize($config->last_checked_ids));
            $current_time = time();
            echo " <h3> удалось сверить " . $ItemsCounter . " объектов на " . $CountCheckedPage . " страницах, за " . ($current_time - $time_start) . " секунд</h3>";
            // делаем update текущей конфигурации исходя из страниц на которых мы остановились
            $config->last_timestamp_new = time();
            $config->save();

        }
        ControlParsing::updating($id_parsingController);
        $currentModule = Control::findOne($this->id);

        $currentModule->last_timestamp = time();
        $currentModule->save();

        $driver->quit();


    }

    public
    function MyParsingNewNewer()
    {
        /* @var $config ParsingConfiguration */
        $type = self::P_NEW;
        $configs = $this->getREADY($type);
        if (!$configs) return false;
        $id_parsingController = ControlParsing::create($type, $configs);

        $driver = MyChromeDriver::Open(MyChromeDriver::CURRENT_PROXY);
        if ($driver == MyChromeDriver::ERROR_LIMIT) {
            Notifications::VKMessage(" ONE SERVER SESSIONS LIMIT");
            return false;
        }
        $time_start = time();

        // обнуление счетчиков
        $CountCheckedPage = 0;
        $ItemsCounter = 0;
        $counterADDRESS_CHANGE = 0;
        $counterPRICE_CHANGED = 0;
        $counterDATESTART_UPDATED = 0;
        $counterNEW = 0;
        $counterTHE_SAME = 0;


        foreach ($configs as $config) {
            $cashed_items = Synchronization::getCachedCategory($config->id);
            $checked_ids = [];
            info(" THIS CATEGORY HAD " . count($cashed_items) . " ITEMS INSIDE YOURSELF", DANGER);

            if ($config->id_sources == 1) {
                $driver->get('https://irr.ru/');
                sleep(2);
            } elseif ($config->id_sources == 5) {
                $driver->get('https://novgorod.cian.ru/');
                if ($this->Captcha($driver) === false) return false;
                sleep(2);
            }
            echo span("START_LINK -->" . Html::a($config->start_link, $config->start_link, ['target' => '_blank']));

            // echo "<br> next link = <a href='" . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . "'> " . ParsingConfiguration::RuleOfUrl($config, $config->success_stop + 1) . " </a>";
            // открываем ссылку ( если нет открывается то выходим)
            $driver->get($config->start_link);
            $pageSource = $driver->getPageSource();

            // проводим проверку селекторов
            Selectors::loadTableClasses($pageSource, $config->source->id);
            Selectors::loadStatClasses($pageSource, $config->source->id);

            // распарсиваем ресурс стартовой страницы
            $pq_page = \phpQuery::newDocument($pageSource);
            $total_count = Parsing::getTotalCount($pq_page, $config->source->id);

            info("total_count=" . $total_count);
            $total_pages = floor($total_count / $config->source->items_per_page) + 1;
            info("total pages=" . $total_pages);
            //return true;
            // пока так !
            if (true) {
                $limit_pages = $config->success_stop + ParsingConfiguration::PAGES_LIMIT;
                if ($limit_pages > $total_pages) $limit_pages = $total_pages;
                info("PARSING FROM " . $config->success_stop . " TO " . $limit_pages . " PAGES OF ID_CATEGORY = " . $config->id);
                $config->sleeping();


                // пробегаемся по страницам для сбора, короткой информации
                $countCheckedPageConfig = 0;
                for ($page = ($config->success_stop + 1); $page <= $total_pages; $page++) {
                    $countCheckedPageConfig++;
                    if ($countCheckedPageConfig > 3) {
                        info("GOT PAGE NUMBER 3", DANGER);
                        break;
                    }

                    // в стучае, если парсим первую страницу, то берем уже полученный ресурс
                    if ($page != 1) {
                        // формируем ссылку по правилам для данной конфигурации в зависимости от текущей страницы
                        $url = ParsingConfiguration::RuleOfUrl($config, $page);
                        info(" GETTING PAGE '" . $url . "'");
                        $driver->getWithCookies($url);
                        // ждем на странице какое-то время имитируя пользователя
                        $config->sleeping();

                        // распарсиваем полученный ресурс страницы
                        $pq_page = \phpQuery::newDocument($driver->getPageSource());


                    } else info("SCRAPING PAGE " . Html::a($config->start_link, $config->start_link, ['target' => '_blank']));

                    // echo $driver->getPageSource();

                    //  $tree = str_get_html($driver->getPageSource());

                    // берем контейнер с вариантами
                    if ($config->id_sources == 1) $pq_page = $pq_page->find('.js-productGrid')->eq(0);

                    $div_selector = Selectors::find()->where(['id_sources' => $config->source->id])->andWhere(['type' => Selectors::TYPE_TABLE_CONTAINER])->one()->selector;
                    $pq_containers = $pq_page->find("." . $div_selector);
                    if (count($pq_containers) == 0) {
                        info("NOTHING TO SCRAPE ... EXITING", 'danger');
                        $config->UpdateAndSave($page, $total_pages, $ItemsCounter);
                        break;
                    } else {
                        info(count($pq_containers) . " items on current page");
                    }
                    $counterCategoryNewPageCategory = 0;

                    // пробегаемся по контейнерам
                    if ($pq_containers) {
                        $toUpdateCheck = [];
                        foreach ($pq_containers as $pq_container) {
                            $ItemsCounter++;

                            $parsing = new ParsingSync();
                            $parsing->extractTableData($config->source->id, pq($pq_container));
                            $parsing->loggedValidate($config->source->id);

                            $active_item = $cashed_items[$parsing->id_in_source];
                            //  my_var_dump($parsing->toArray());

                            if (!in_array($active_item->id_in_source, $checked_ids)) $SynchResponse = Synchronization::TODO_NEW($parsing, $config, ['active_item' => $active_item]);
                            else {
                                info(" THIS ITEM HAS BEEN CHECKED YET IN THIS CHECKING...SKIP", DANGER);
                                continue;
                            }

                            $checked_ids[] = $SynchResponse[0];
                            $SynchResponse = $SynchResponse[1];

                            if (preg_match("/ADDRESS_CHANGED/", $SynchResponse)) $counterADDRESS_CHANGE++;
                            if (preg_match("/PRICE_CHANGED/", $SynchResponse)) $counterPRICE_CHANGED++;
                            if (preg_match("/DATESTART_UPDATED/", $SynchResponse)) $counterDATESTART_UPDATED++;
                            if (preg_match("/NEW/", $SynchResponse)) {
                                $counterNEW++;
                                $counterCategoryNewPageCategory++;
                            }
                            if (preg_match("/THE_SAME/", $SynchResponse)) {
                                $counterTHE_SAME++;
                                if ($active_item) $toUpdateCheck[] = $active_item->id;
                            }
                            //  echo "<hr>";
                        }
                        if ($toUpdateCheck) {
                            Synchronization::updateAll(['date_of_check' => time()], ['in', 'id', $toUpdateCheck]);
                            Sale::updateAll(['date_of_check' => time()], ['in', 'id', $toUpdateCheck]);
                            info(" UPDATE DATE_OF_CHECK TO " . count($toUpdateCheck) . " THE_SAME ITEMS");
                            info(implode(",", $toUpdateCheck));
                        }

                        // update счетчика обработанных страниц
                        $CountCheckedPage++;
                        // если мы обработали страниц больше лимита то выходим
                        if ($CountCheckedPage >= ParsingConfiguration::PAGES_LIMIT) break;
                        // делаем update текущей конфигурации исходя из страниц на которых мы остановились

                    }


                    ControlParsing::updatingTime($id_parsingController);
                    if ($counterCategoryNewPageCategory == 0) {
                        info("PAGE HAS NO NEW ITEMS ..... BREAK AND CONTINUE NEXT CONFIG...", SUCCESS);
                        $config->last_timestamp_new = time();
                        $config->save();
                        break;
                    } else {
                        info("PAGE HAS NEW ITEMS ..... CONTINUE ...", DANGER);
                    }
                }

                $current_time = time();
                info($ItemsCounter . " ITEMS WERE CHECKED ON " . $CountCheckedPage . " PAGES DURING " . ($current_time - $time_start) . " SECONDS");

            }


        }

        $counts_array = [
            'ADDRESS_CHANGED' => $counterADDRESS_CHANGE,
            'PRICE_CHANGED' => $counterPRICE_CHANGED,
            'DATESTART_UPDATED' => $counterDATESTART_UPDATED,
            'NEW' => $counterNEW,
            'THE_SAME' => $counterTHE_SAME,
        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));


        //  $driver->quit();


    }

    /*
     * @var $sale */

    public
    function DetaledParsing($limit = 20)
    {
        /* @var $sale Sale */
        $type = self::P_DET;
        $time_start = time();
        // поиск объектов на обработку
        $sales = $this->getREADY($type, $limit);

        // если нечего обрабатывать, то выходим
        if (!$sales) return false;

        // группируем объекты по id_sources для  того, чтобы обрабатывать их последовательно
        $grouped_sales = array_group_by($sales, 'id_sources');
        $driver = MyChromeDriver::Open(MyChromeDriver::CURRENT_PROXY);
        if ($driver == MyChromeDriver::ERROR_LIMIT) {
            Notifications::VKMessage(" ONE SERVER SESSIONS LIMIT");
            return false;
        }
        $id_parsingController = ControlParsing::create($type, $sales, $driver->ip);


        // $driver->getMyIp();

        foreach ($grouped_sales as $key => $sales) {

            $config = ParsingConfiguration::find()->where(['id_sources' => $key])->one();
            // при проходе irr делаем заход на стартовую страницу т.к. появляются всплывающие окна
            if ($config->id_sources == 1) {
                $driver->get('https://irr.ru/');
                sleep(2);
            } elseif ($config->id_sources == 5) {
                $driver->get('https://novgorod.cian.ru/');
                if ($this->Captcha($driver) === false) break;
                sleep(2);
            }

            // создаем запись контоля парсинга и берем ее ID
            foreach ($sales as $key_sales => $sale) {
                $break = false;
                // открываем ссылку ( если нет открывается то выходим)

                $driver->getWithCookies($sale->url);
                $response = $driver->getPageSource();
                if (($sale->id_sources == 5) and ($key_sales == 0)) {
                    Selectors::loadPageClasses($response, $sale->id_sources);

                }
                $response = $driver->SequentialProcessing(Parsing::sourceSequential()[$sale->id_sources]);


                if ($response == 'DELETED') {
                    $sale->changingStatuses('DELETED');
                } elseif ($response == 'DISABLED') {
                    $sale->changingStatuses('DISABLED');
                    $sale->changingStatuses('PARSED');
                } else {

                    info(Sale::DISACTIVE_CONDITIONS_ARRAY[$sale->disactive] . $sale->renderLong_title() . " " . $sale->phone1, PRIMARY);
                    if ($sale->id_sources == CIAN_ID_SOURCE) {
                        $parsing = new Parsing();
                        $parsing->extractPageData(\phpQuery::newDocument($response), $sale->url, $sale->id_sources);
                        // my_var_dump($parsing->toArray());
                    } else {
                        $parsing = ParsingExtractionMethods::ExtractPageData($response, $driver->getCurrentURL(), $sale->id_sources);

                    }
                    if ($parsing) $parsing->UpdateSale($sale);
                    info($sale->renderLong_title() . " " . $sale->phone1, PRIMARY);
                    $sale->changingStatuses('PARSED');

                }

                // если оно только что пригло и уже устарело то удалаем его т.к. всеравно нет телефона и цены
//                if ((!$sale->phone1) AND ($sale->id_sources != 3)) {
//                    $toDeleteSync = Synchronization::findOne($sale->id);
//                    if ($toDeleteSync) {
//                        $toDeleteSync->delete();
//                        info("удалили <a href='" . $sale->url . "' > " . $sale->id_in_source . "</a>", DANGER);
//
//                    }
//
//                }
                // сохраняем модель после обработки
                $sale->date_of_check = time();
                //  echo " <hr>";
                if (!$sale->save()) my_var_dump($sale->getErrors());
                ControlParsing::updatingTime($id_parsingController);

            }
            ControlParsing::updating($id_parsingController);

        }
//        echo Synchronization::Counts(Synchronization::find()->where(['not in', 'disactive', [1, 2]]), [
//            'parsed' => [1, 2, 3],
//
//        ]);
        //   $driver->quit();
        return true;

    }


// метод для получение объектов для обработки c выводом их количества и статуса
    protected
    function getREADY($type = '', $limit = 20, $params = [])
    {
        /* @var $agentpro AgentPro */


        $agentpro = \Yii::$app->params['agent_pro'];
        $available_sources = explode(",", $agentpro->id_sources);
        //  my_var_dump($available_sources);

        switch ($type) {
            case self::P_APHONES:
                {
                    $Broken_Controls = $this->getBrokenParsingControls($type);
                    // берем последнюю необработанную конфигурацию
                    $query = Synchronization::find();
                    $query->where(['OR',
                        ['IS', 'phone1', NULL],
                        ['phone1' => ''],
                    ])->andwhere(['id_sources' => 3]);
                    // вычитаем критически занятые ресурсы и id
                    //  $query->andFilterWhere(['not in', 'id_sources', ControlParsing::getBusySources($type)]);
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    // доступные глобально ресурся
                    $query->andFilterWhere(['in', 'id_sources', $available_sources]);

                    if ($Broken_Controls) $query->andwhere(['not in', 'id_sources', $Broken_Controls]);
                    $objects = $query->orderBy(new Expression('rand()'))->limit($limit)->all();
                    break;


                }
            case self::P_DET:
                {

                    $Broken_Controls = $this->getBrokenParsingControls($type);
                    $query = Synchronization::find();
                    $query->Where(['parsed' => 2])
                        ->andwhere(['not in', 'disactive', [1]]);

//                    $query->andwhere(['AND',
//                        ['not in', 'phone1', [NULL, '']],
//                        ['id_sources' => 3]]);

                    // вычитаем критически занятые ресурсы и id
                    //  $query->andFilterWhere(['not in', 'id_sources', ControlParsing::getBusySources($type)]);
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $query->orderBy(['id_sources' => SORT_ASC, 'date_of_check' => SORT_DESC]);

                    // доступные глобально ресурся
                    $query->andFilterWhere(['in', 'id_sources', $available_sources]);
                    if ($Broken_Controls) $query->andwhere(['not in', 'id_sources', $Broken_Controls]);
                    $objects = $query->limit($limit)->all();
                    break;


                }
            case self::UP_SYNC :
                {
                    $query = Synchronization::find()->andWhere(['sync' => 2]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::DOWN_SYNC;
                {
                    $query = Sale::find()->andWhere(['sync' => 2]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::ANALISYS;
                {
                    $query = Synchronization::find()->andWhere(['load_analized' => 2]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::GEO;
                {
                    $query = Synchronization::find()
                        ->where(['geocodated' => 8]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::PROCC;
                {
                    $query = Synchronization::find()
                        ->where(['processed' => 2]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::SIMILAR;
                {
                    $query = Synchronization::find()
                        ->where(['id_similar' => 0]);
                    $query->andWhere(['parsed' => Sale::DONE]);
                    // удаляем критически занятые id
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::P_NEW:
                {
                    $Broken_Controls = $this->getBrokenParsingControls($type);
                    $query = ParsingConfiguration::find()
                        ->where(['active' => 1])
                        ->andwhere(['<', 'last_timestamp_new', time() - $agentpro->period_check_new])
                        ->andwhere(['module_id' => $this->id]);
                    // вичитаем критически занятые ресурсы
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIds($type)]);
                    $query->andFilterWhere(['not in', 'id_sources', ControlParsing::getBusySources($type)]);
                    // доступные глобально ресурся
                    $query->andFilterWhere(['in', 'id_sources', $available_sources]);
                    if ($Broken_Controls) $query->andwhere(['not in', 'id_sources', $Broken_Controls]);
                    $objects = $query->limit($limit)->all();
                    break;
                }
            case self::P_SYNC:
                {
                    $period_of_check = $agentpro->period_check;
                    $Broken_Controls = $this->getBrokenParsingControls($type);
                    $query = ParsingConfiguration::find()
                        ->where(['active' => 1])
                        ->andwhere(['<', 'last_timestamp', time() - $period_of_check * 60 * 60])
                        ->andwhere(['module_id' => $this->id]);
                    // вичитаем критически занятые ресурсы
                    $query->andFilterWhere(['not in', 'id', ControlParsing::getBusyIdConfigs($type)]);
                    // $query->andFilterWhere(['not in', 'id_sources', ControlParsing::getBusySources($type)]);

                    // доступные глобально ресурся
                    $query->andFilterWhere(['in', 'id_sources', $available_sources]);
                    if ($Broken_Controls) $query->andwhere(['not in', 'id_sources', $Broken_Controls]);
                    $objects = $query->orderBy(new Expression('rand()'))->one();

                    $limit = 1;
                    break;

                }
            default :
                info(' ВЫ ВЫБРАЛИ НЕИЗВЕСТНУЮ ОПЦИЮ');


        }
        // получаем количество необработанных объектов  и выводим информацию
        $count = $query->count();
        if ($count < $limit) $limit = $count;
        if ($count != 0) {
            info(self::mapTypesControls()[$type] . " (" . $limit . " from " . $count . ")");
        } else info(self::mapTypesControls()[$type] . " (NO)", 'danger');

        return $objects;

    }

// вычисление сломанных контроллеров парсинга и вычитания их из поиска
    public
    function getBrokenParsingControls($type)
    {
        switch ($type) {
            case self::P_APHONES :
                {
                    $Broken_Controls = ControlParsing::find()
                        ->select('ids_sources')
                        ->distinct()
                        ->where(['status' => 4])
                        ->andWhere(['type' => self::P_APHONES])
                        ->column();
                    break;
                }
            case self::P_DET:
                {
                    $Broken_Controls = ControlParsing::find()
                        ->select('ids_sources')
                        ->distinct()->where(['in', 'status', [4]])
                        ->andWhere(['type' => self::P_DET])
                        ->column();

                    break;
                }
            case self::P_NEW:
                {
                    $Broken_Controls = ControlParsing::find()
                        ->select('ids_sources')
                        ->distinct()->where(['in', 'status', [4]])
                        ->andWhere(['in', 'type', [self::P_NEW, self::P_SYNC]])
                        // ->andwhere(['module_id' => $this->id])
                        ->column();
                    break;
                }
            case self::P_SYNC:
                {
                    $Broken_Controls = ControlParsing::find()
                        ->select('ids_sources')
                        ->distinct()->where(['in', 'status', [4]])
                        ->andWhere(['in', 'type', [self::P_NEW, self::P_SYNC]])
                        // ->andwhere(['id' => $this->id])
                        ->column();
                    break;
                }

            default :
                $Broken_Controls = [];
                break;
        }
        //  my_var_dump($Broken_Controls);
        // если есть сломанные ресурсы, то рендерим их:
        if (count($Broken_Controls)) {
            $total = '';
            foreach ($Broken_Controls as $broken_Control) {
                $total .= "," . $broken_Control;
            }
            $total = explode(",", $total);
            $total = array_values(array_unique($total));
            $total = array_diff($total, array('', 0, null));
            my_var_dump($total);
            return $total;
        } else return [];
    }


    public
    function ParsingAvitoPhones($limit = 5)
    {
        $type = self::P_APHONES;
        $time_start = time();
        // берем объекты
        $sales = $this->getREADY($type);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);

        $driver = MyChromeDriver::Open(MyChromeDriver::CURRENT_PROXY);
        if ($driver == MyChromeDriver::ERROR_LIMIT) {
            Notifications::VKMessage(" ONE SERVER SESSIONS LIMIT");
            return false;
        }
        $counterERROR = 0;
        $counterSUCCESS = 0;
        //  $driver->get('https://irr.ru/');
        //  $driver->manage()->deleteAllCookies();
        //  $cookie = new Cookie('cookie_name', 'cookie_value');
        //  $driver->manage()->addCookie($cookie);
        // распарсиваем ресурс стартовой страницы

        foreach ($sales as $sale) {
            // открываем ссылку ( если нет открывается то выходим)

            $url = $sale->url;
            $url = preg_replace("/www.avito/", "m.avito", $url);
            info($url);
            $driver->getWithCookies($url);
            sleep(rand(7, 10));

            if (!preg_match("/Сохранить.+поиск/", $driver->getPageSource())) {

                if (preg_match("/href=\"tel:(.{7,13})\"/", $driver->getPageSource(), $output_array)) $phone = $output_array[1];
                else {
                    if (preg_match("/action-show-number|amw-test-item-click/", $driver->getPageSource(), $output_array)) {
                        info("FOUND " . $output_array[0], SUCCESS);
                        $element = $driver->findElement(WebDriverBy::className($output_array[0]));
                        $driver->wait(10, 1000)->until(
                            WebDriverExpectedCondition::visibilityOf($element)
                        );


                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);

                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);
                        $driver->getKeyboard()->sendKeys(WebDriverKeys::ARROW_DOWN);

                        $driver->findElement(WebDriverBy::className($output_array[0]))->click();
                        sleep(rand(4, 6));

                    } else {
                        info("DIDNT FIND 'action-show-number'", DANGER);
                        $break = true;
                        info("ITEMS WAS DELETED", DANGER);
                    }
                }

            } else {
                $break = true;
                info("ITEMS WAS DELETED", DANGER);

            }
            if (!$break) {
                sleep(rand(3, 5));
                if ($phone) {
                    info("AVITO PHONE FOUND BY NEW SYSTEM ", SUCCESS);
                    $sale->phone1 = preg_replace("/\+7/", "8", $phone);


                    info($sale->phone1);
                } else {
                    $sale->phone1 = ParsingExtractionMethods::ExtractPhoneFromMAvito($driver->getPageSource());

                }

            } else {
                if (!$sale->phone1) {
                    $counterERROR++;
                    // если оно только что пригло и уже устарело то удалаем его т.к. всеравно нет телефона и цены

                    if (!Synchronization::findOne($sale->id)->delete()) info("CANNOT DELETE ITEMS", DANGER);
                    else  info(" удалили <a href='" . $sale->url . "' > " . $sale->id_in_source . "</a>", SUCCESS);

                }
            }
            if (preg_match("/\d{9,11}/", $sale->phone1)) {
                $counterSUCCESS++;
            } else {
                $counterERROR++;
                $sale->phone1 = '';
            }


            if (!$sale->save()) my_var_dump($sale->getErrors());

            ControlParsing::updatingTime($id_parsingController);
        }

        $counts_array = [
            'AVITO_PHONES_ERROR' => $counterERROR,
            'AVITO_PHONES_SUCCESS' => $counterSUCCESS,

        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));
        //  $driver->quit();
        return true;

    }

    public
    function emptyMethods()
    {
        return "JKHcxcxKG";
    }

    public
    function ParsingAvitoPhones1($limit = 20)
    {
        $type = self::P_APHONES;
        $time_start = time();
        // берем объекты
        $sales = $this->getREADY($type, 5);
        if (!$sales) return false;
        $id_parsingController = ControlParsing::create($type, $sales);
        if ($proxy = Proxy::find()->where(['status' => 1])->orderBy('time')->one()) {
            info(" PROXY " . $proxy->fulladdress . " WAS USED " . Yii::$app->formatter->asRelativeTime($proxy->time), SUCCESS);
            $proxy->time = time();
            $proxy->save();
            \Yii::$app->params['ip'] = $proxy->fulladdress;
        };


        // $sales = [];
        foreach ($sales as $sale) {
            // открываем ссылку ( если нет открывается то выходим)

            $url = $sale->url;
            $url = preg_replace("/www.avito/", "m.avito", $url);

            $curl = new MyCurl();
            if ($proxy) {
                $curl->ipPort = $proxy->fulladdress;
                $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
                $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
                $curl->setOpt(CURLOPT_PROXY, $curl->ipPort);
                $curl->setOpt(CURLOPT_PROXYUSERPWD, $proxy->login . ":" . $proxy->password);

            }


            $headers = [
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'DNT' => '1',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
                'Pragma' => 'akamai-x-cache-on, akamai-x-cache-remote-on, akamai-x-check-cacheable, akamai-x-get-cache-key, akamai-x-get-extracted-values, akamai-x-get-ssl-client-session-id, akamai-x-get-true-cache-key, akamai-x-serial-no, akamai-x-get-request-id,akamai-x-get-nonces,akamai-x-get-client-ip,akamai-x-feo-trace'
            ];
            $curl->setHeaders($headers);
            $curl->getUrlWithCookies($url);
            sleep(rand(4, 6));
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;
            $curl->close();

            $response = str_get_html($response);
            if (preg_match_all("/(action-show-number|amw-test-number-click)/", $response, $output_array)) {
                my_var_dump($output_array);
                $selector = $output_array[0][0];
                //  $selector = "action-show-number";
                info(" SELECTOR = " . $selector, SUCCESS);
                $hash = $response->find("." . $selector, 0)->href;
                if (!$hash) {

                }
                echo span("HASH IS " . $hash);


            } else {
                $error = Errors::findOne(AVITO_CANNOT_FIND_PHONEBUTTON_DIV_CLASS);
                if (!$response) $response = "THE RESPONSE IS EMPTY";
                AgentPro::throwError($error, $response);
                $counterERROR++;
                info(" DELETING THE ITEM", DANGER);
                $sale->delete();
                continue;

            }


            $hash = $response->find("." . $selector, 0)->href;


            $url_phone = "https://m.avito.ru/" . $hash . "?async";


            $headers = [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'DNT' => '1',
                'Referer' => $url,
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
            ];
            // info("URL =".$url_phone);
            // my_var_dump($headers);
            $curl = new MyCurl();
            if ($proxy) {
                $curl->ipPort = $proxy->fulladdress;
                $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
                $curl->setOpt(CURLOPT_PROXY, $curl->ipPort);
                $curl->setOpt(CURLOPT_PROXYUSERPWD, $proxy->login . ":" . $proxy->password);

            }
            $curl->setHeaders($headers);
            $curl->getUrlWithCookies($url_phone);
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;

            $response = json_decode($response);
            //  my_var_dump($response);
            $curl->close();


            info(" TELEPHONE = " . $response->phone);

            $phone = preg_replace("/\A7/", "8", preg_replace("/\D/", "", $response->phone));

            $sale->phone1 = $phone;
            if (preg_match("/\d{9,11}/", $sale->phone1)) {
                $counterSUCCESS++;
            } else {
                $counterERROR++;
                $sale->phone1 = '';
            }

            sleep(rand(4, 6));
            if (!$sale->save()) my_var_dump($sale->getErrors());
            ControlParsing::updatingTime($id_parsingController);


        }

        $counts_array = [
            'AVITO_PHONES_ERROR' => $counterERROR,
            'AVITO_PHONES_SUCCESS' => $counterSUCCESS,

        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));
        //  $driver->quit();
        return true;

    }

    public
    function ParsingAvitoPhonesNewer($limit = 20)
    {
        $time_start = time();
        // берем объекты
        $sales = $this->getREADY('PARSING_AVITO_PHONES');

        if (!$sales) return false;
        $driver = MyChromeDriver::Open();

        //  берем любую конфигрурацию авито
        $config = ParsingConfiguration::find()->where(['id_sources' => 3])->one();
        $id_parsingController = $config->createParsingController('PARSING_AVITO_PHONES');

        foreach ($sales as $sale) {
            // открываем ссылку ( если нет открывается то выходим)
            $url = preg_replace("/www.avito/", "m.avito", $sale->url);
            $response = $driver->get($url);
            if ($response === false) {
                echo "<h3> ПРОБЛЕМЫ С СОЕДИНЕНИЕМ</h3>";
                $driver->quit();
                die;
            }
            $response = $driver->SequentialProcessing(Parsing::sourceSequential()[31]);
            if ($response == 'DELETED') {
                $sale->changingStatuses('DELETED');
            } elseif ($response == 'DISABLED') {
                $sale->changingStatuses('DISABLED');
            } else {
                $sale->phone1 = ParsingExtractionMethods::ExtractPhoneFromMAvito($driver->getPageSource());
            }
            if (!$sale->phone1) {
                // если оно только что пригло и уже устарело то удалаем его т.к. всеравно нет телефона и цены
                if (!Synchronization::findOne($sale->id)->delete()) echo "<br> не удалось удалить";
                else  echo "<br> удалили <a href='" . $sale->url . "' > " . $sale->id_in_source . "</a>";

            }
            if (!$sale->save()) my_var_dump($sale->getErrors());


        }
        $counts_array = [
            'AVITO_PHONES_ERROR' => $counterERROR,
            'AVITO_PHONES_SUCCESS' => $counterSUCCESS,

        ];
        ControlParsing::updating($id_parsingController, 2, serialize($counts_array));

        $config->updateParsingController($id_parsingController);

        $driver->quit();
        return true;

    }


    public
    function Synchronisation($limit = 200)
    {
        $type = self::UP_SYNC;
        $syncs = $this->getREADY($type, $limit);

        // если ничего не пришло, то сразу выходим
        if ($syncs) {
            $id_parsingController = ControlParsing::create($type, $syncs);
            foreach ($syncs as $sync) {
                $sale = Sale::findOne($sync->id);
                if ($sale) {
                    $sale->setAttributes($sync->getAttributes());
                    $sale->grossarea = $sync->grossarea;
                    $sale->similar_ids = $sync->similar_ids;
                    $sale->id_similar = $sync->id_similar;
                    $sale->date_of_die = $sync->date_of_die;
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                    $sale->changingStatuses('SYNC');
                    $sale->save();
                } else {
                    $sale = new Sale();
                    $sale->setAttributes($sync->getAttributes());
                    $sale->grossarea = $sync->grossarea;
                    $sale->similar_ids = $sync->similar_ids;
                    $sale->id_similar = $sync->id_similar;
                    $sale->date_of_die = $sync->date_of_die;
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                    $sale->changingStatuses('SYNC');
                    $sale->save();
                }
                $sync->changingStatuses('SYNC');
                $sync->setProccessingLog(Sale::SYNC_UP);

                $sync->save();
            }
            ControlParsing::updating($id_parsingController);
        }

        $type = self::DOWN_SYNC;
        $syncs = $this->getREADY($type, $limit);

        // если ничего не пришло, то сразу выходим
        if (!$syncs) return false;
        $id_parsingController = ControlParsing::create($type, $syncs);
        foreach ($syncs as $sync) {
            $sale = Synchronization::findOne($sync->id);
            if ($sale) {
                $sale->setAttributes($sync->getAttributes());

                if (!$sale->save()) my_var_dump($sale->getErrors());
                $sale->changingStatuses('SYNC');
                $sale->save();
            } else {
                $sale = new Sale();
                $sale->setAttributes($sync->getAttributes());
                if (!$sale->save()) my_var_dump($sale->getErrors());
                $sale->changingStatuses('SYNC');
                $sale->save();

            }
            $sync->changingStatuses('SYNC');
            $sync->setProccessingLog(Sale::SYNC_UP);

            $sync->save();
        }
        ControlParsing::updating($id_parsingController);

    }

    public
    function Captcha($driver)
    {

        sleep(2);
        if (preg_match("/cian.ru\/captcha\/?/", $driver->getCurrentURL())) {
            info(" ПОЯВИЛАСЬ RECAPTCHA");
            $pq = phpQuery::newDocument($driver->getPageSource());
            // echo $pq->find('#form_captcha')->html();

            $iframe_scr = $pq->find('iframe')->attr('src');
            //echo "<br>iframe_scr = ".$iframe_scr;


            sleep(2);
            preg_match("/www.google.com\/recaptcha\/api2\/anchor\?k=(.+)&co=/", $iframe_scr, $output_array);
            my_var_dump($output_array);

            $data_key = $output_array[1];
            info(" data-key = " . $data_key);
            $key = "635691e6d90d1cea0a859cd75fa5c54c";
            $request = "http://rucaptcha.com/in.php?key=" . $key
                . "&method=userrecaptcha&googlekey=" . $data_key . "&pageurl=" . $driver->getCurrentURL();

            echo "<br> request = " . $request;

            $response = file_get_contents($request);
            sleep(4);
            if (preg_match("/OK.(\d+)/", $response, $output_array)) {
                sleep(60);
                $id_captha = $output_array[1];
                echo "<br>id_captcha = " . $id_captha;
                $request = "http://rucaptcha.com/res.php?key=" . $key
                    . "&action=get&id=" . $id_captha;
                $response = file_get_contents($request);
                echo "<br>response = " . $response;
                if (!preg_match("/CAPCHA_NOT_READY/", $response, $output_array)) {
                    preg_match("/OK.(.+)/", $response, $output_array);
                    $g_recaptcha_response = $output_array[1];
                    $driver->executeScript("document.getElementById('g-recaptcha-response').innerHTML='" . $g_recaptcha_response . "';");
                    sleep(1);
                    $driver->executeScript("document.getElementById('form_captcha').submit();");

                    return true;

                } else {
                    sleep(30);
                    info($response);
                    $response = file_get_contents($request);
                    echo "<br>response = " . $response;
                    if (!preg_match("/CAPCHA_NOT_READY/", $response, $output_array)) {
                        preg_match("/OK.(.+)/", $response, $output_array);
                        $g_recaptcha_response = $output_array[1];
                        $driver->executeScript("document.getElementById('g-recaptcha-response').innerHTML='" . $g_recaptcha_response . "';");
                        sleep(1);
                        $driver->executeScript("document.getElementById('form_captcha').submit();");

                        return true;

                    } else return false;
                }

            }
        } else {
            info(" НЕТ RECAPTCHA");
        }

        //  echo $driver->getPageSource();


    }

}
