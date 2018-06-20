<?php

namespace app\models;

use kartik\select2\Select2;
use Yii;
use phpQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Parsing_Configuration".
 *
 * @property integer $id
 * @property string $id_sources
 * @property string $name
 * @property string $start_link
 * @property string $module
 * @property string $last_ip
 * @property integer $last_timestamp
 * @property integer $last_timestamp_new
 *
 * @property string $non_start_link
 * @property string $last_checked_ids
 *
 * @property integer $success_stop;
 * @property integer $active;
 */
class ParsingConfiguration extends \yii\db\ActiveRecord
{
    const PAGES_LIMIT = 100; // количество одновременно обрабатываемых странних за один скрипт
    const MAX_WAITING_DRIVER_TIMEOUT = 100000;  // максимальный тайм ауйт в милисекундах
    const ONE_PAGE_WAITING_PERIOD = 6;
    const PERIOD_OF_CHECK_LOST_LINKS = 6;
    const PERIOD_OF_CHECK = 10;  // период сверки изменений в часах
    const PERIOD_OF_CHECK_NEW = 500; // период проверки новых объявлений
    const PERIOD_CHECK_OF_DEATH = 48;
    const STATUS = [
        0 => 'неактивно',
        1 => 'активно'];

    const ACTIVE = 1;
    const DISACTIVE = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Parsing_Configuration';
    }

    public static function LAST_TIMESTAMP($module_id = 1)
    {
        return ParsingConfiguration::find()->where(['active' => 1])->andWhere(['module_id' => $module_id])->min('last_timestamp');

    }

    public static function getLastCheck($id_module)
    {

        $time = ParsingConfiguration::find()->where(['active' => ParsingConfiguration::ACTIVE])->andWhere(['module_id' => $id_module])->min('last_timestamp');


    }

    /**
     * @inheritdoc
     */
    public function createParsingController($type = 'DEFAULT', $params = [])
    {
        // установка конфигурации сломанной прошлом скрипте
        $broken_ParsingController = ControlParsing::find()->where(['status' => 1])->one();
        if ($broken_ParsingController) {
            $broken_ParsingController->status = 3;
            $broken_ParsingController->save();

        }
        // удаление лишних успешных контролерров
        $succesfulControllers = ControlParsing::find()->select('id')->where(['status' => 2])->andwhere(['>', 'date_start', time() - 500])->limit(20)->orderBy(['date_start' => SORT_DESC])->column();

        $delete_succesfulControllers = ControlParsing::find()->where(['status' => 2])->andWhere(['not in', 'id', $succesfulControllers])->all();
        foreach ($delete_succesfulControllers as $item) {
            //  info("<br> удалили" . $item->type . " статус" . $item->status);
            // $item->delete();
        }
        // удаление случайно сломанных контроллеров 2 часовой давности
        $brokenControllers = ControlParsing::find()->select('id')->where(['status' => 3])->andWhere(['>', 'date_start', time() - 2 * 60 * 60])->orderBy(['date_start' => SORT_DESC])->column();

        $delete_succesfulControllers = ControlParsing::find()->where(['status' => 3])->andWhere(['not in', 'id', $brokenControllers])->all();
        foreach ($delete_succesfulControllers as $item) {
            //   info("<br> удалили" . $item->type . " статус" . $item->status . " двух часовой давности", 'alert');
            $item->delete();
        }
        // удаление сстарынх контролееров
        $oldControllers = ControlParsing::find()->andWhere(['<', 'date_start', time() - 48 * 60 * 60])->orderBy(['date_start' => SORT_DESC])->all();

        foreach ($oldControllers as $item) {
            //  info("удалили старый контроллер", 'alert');
            $item->delete();
        }

        // фиксирование сломанной конфигурации 300 секундной давности
        $brokenControllers_300sec = ControlParsing::find()->where(['status' => 3])->andWhere(['>', 'date_start', time() - 300]);
        if ($brokenControllers_300sec->count() > 4) {
            foreach ($brokenControllers_300sec->all() as $item) {
                info("<br> ПОСТАВИЛИ НА УЧЕТ" . $item->type . " статус" . $item->status . " 300 сек давности", 'alert');
                $item->status = 4;
                $item->save();
            }
        }
        $delete_succesfulControllers = ControlParsing::find()->where(['status' => 3])->andWhere(['not in', 'id', $brokenControllers])->all();
        foreach ($delete_succesfulControllers as $item) {
            info(" удалили" . $item->type . " статус" . $item->status, 'alert');
            $item->delete();
        }
        $parsingController = new ControlParsing();
        $parsingController->id_sources = $this->id_sources;
        $parsingController->date_start = time();
        $parsingController->type = $type;
        // ставим активный статус
        $parsingController->status = 1;
        if (!empty($params['busy_ids'])) $parsingController->ids = implode(",", $params['busy_ids']);
        if (!empty($params['busy_id_sources'])) $parsingController->ids_sources = implode(",", $params['busy_id_sources']);

        if ((!empty($params['busy_ids'])) or (empty($params['busy_id_sources']))) {

            $parsingController->params = serialize($params);
        } else $parsingController->params = '';
        $parsingController->config_id = $this->id;
        if (!$parsingController->save()) my_var_dump($parsingController->getErrors());
        return $parsingController->id;


    }

    public function updateParsingController($id)
    {
        $parsingController = ControlParsing::findOne($id);
        $parsingController->date_finish = time();
        $parsingController->status = 2;
        $parsingController->ids = '';
        $parsingController->ids_sources = '';
        $parsingController->params = null;
        $parsingController->save();

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_link', 'module_id'], 'required'],
            [['last_timestamp', 'last_timestamp_new', 'success_stop', 'active', 'id_sources', 'module_id'], 'integer'],
            [['start_link', 'non_start_link', 'name'], 'string', 'max' => 256],

            [['last_checked_ids'], 'string'],
            [['last_ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sources' => 'sources',
            'start_link' => 'Start Link',
            'last_ip' => 'Ip',
            'last_timestamp' => 'sync',
            'last_timestamp_new' => 'new',
            'success_stop' => 'page',
            'active' => 'Status',
            'name' => 'name',
            'module_id' => 'City',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function getModules()
    {
        return ArrayHelper::map(Control::find()->all(), 'id', 'region');
    }

    public function getModule()
    {
        return ArrayHelper::map(Control::find()->all(), 'id', 'region_rus')[$this->module_id];
    }


    /**
     * @inheritdoc
     */
    public function UpdateSuccessStop($page)
    {
        $this->success_stop = $page;
        if (!$this->save()) my_var_dump($this->getErrors());

    }

    public function FinalStop()
    {
        $this->success_stop = 1;
        $html = my_curl_response('https://2ip.ru');
        $pq = phpQuery::newDocument($html);

        $ip = $pq->find('.ip')->find('big')->text();
        $this->last_timestamp = time();
        $this->last_ip = $ip;

        if (!$this->save()) my_var_dump($this->getErrors());

    }

    /**
     * метод ищет самую позднюю необработанную конфигурацию за последние $period_of_check часов
     */

    public static function GetLast($id_SOURCES_ARRAY = [1, 2, 3, 5], $period_of_check = ParsingConfiguration::PERIOD_OF_CHECK)
    {
        $last_check = ParsingConfiguration::find()
            ->where(['active' => 1])
            ->andWhere(['in', 'id_sources', $id_SOURCES_ARRAY])
            ->orderBy('last_timestamp')
            ->one();
        $Exist = ParsingConfiguration::find()
            ->where(['active' => 1])
            ->andwhere(['<', 'last_timestamp', time() - $period_of_check * 60 * 60])
            ->andWhere(['in', 'id_sources', $id_SOURCES_ARRAY])
            ->orderBy('last_timestamp')
            ->one();
        if ($Exist) return $Exist;
        else {
            echo "<br> делали сверку " . (time() - $last_check->last_timestamp) . " секунд назад,  до проверки осталось " . round(((Self::PERIOD_OF_CHECK * 60 * 60 + $last_check->last_timestamp - time()) / 3600), 2) . " часов";
            return false;
        }


    }

    public static function GetAllForNew($Broken_Controls)
    {
        $last_check = ParsingConfiguration::find()
            ->where(['active' => 1])
            ->andwhere(['<', 'last_timestamp_new', time() - ParsingConfiguration::PERIOD_OF_CHECK_NEW])
            ->limit(4)
            ->all();
        if ($last_check) return $last_check;
        else {
            return false;
        }


    }

    /**
     * тестовый метод ищет самую позднюю необработанную конфигурацию за последние $hours часов
     */
    public static function GetLastDev($hours = 24, $id_sources = 0)
    {
        if ($id_sources == 0) $id_sources = null;
        return self::find()
            ->where(['<', 'last_timestamp', time() - $hours * 60 * 60])
            ->andfilterwhere(['id_sources' => $id_sources])
            ->andwhere(['active' => 1])
            ->orderBy(new Expression('rand()'))
            ->one();

    }

    /**
     * тестовый метод ищет самую позднюю необработанную конфигурацию за последние $hours часов
     */


    public function setSource($id_sources)
    {
        $update_PConfig = self::findOne($this->id);
        $update_PConfig->id_sources = $id_sources;
        $update_PConfig->save();
    }

    /*
     * метод загружающий параметры текущей конфигрурации для каждого ресурса
     * */

    public static function LoadSettings($config)
    {

        switch ($config->id_sources) {
            case 1:
                {
                    return [
                        'data_container' => 'js-productBlock',
                        'container' => 'div.js-productBlock',
                        'items_per_page' => 30,
                        'total_count' =>
                            [
                                'div' => 'div.listingStats',
                                'pattern' => "/из \d+ предложений/"
                            ],
                        'start_url' => ParsingConfiguration::RuleOfUrl($config, 1)

                    ];

                    break;
                }

            case 2:
                {
                    return [
                        'data_container' => 'search-results__serp_type_offers',
                        'container' => 'div.serp-item__columns',
                        'items_per_page' => 20,
                        'total_count' =>
                            [
                                'div' => 'div.FiltersFormField__counter-submit',
                                'pattern' => "/оказать \d+ объявлен/"
                            ],
                        'start_url' => ParsingConfiguration::RuleOfUrl($config, 1)

                    ];
                    break;
                }
            case 3:
                {
                    return [
                        'data_container' => 'catalog-content',
                        'container' => 'div.item',
                        'items_per_page' => 50,
                        'total_count' =>
                            [
                                'div' => 'span.breadcrumbs-link-count',
                                'pattern' => ""
                            ],
                        'start_url' => ParsingConfiguration::RuleOfUrl($config, 1)

                    ];
                    break;
                }
            case 5:
                {
                    return [
                        'data_container' => 'offer-container--2MrIy',
                        'container' => 'div.offer-container--2MrIy',
                        'items_per_page' => 25,
                        'total_count' =>
                            [

                                'div' => 'div._93444fe79c-totalOffers--yZcBn',
                                'pattern' => ""
                            ],
                        'start_url' => ParsingConfiguration::RuleOfUrl($config, 1)

                    ];
                    break;
                }


        }


    }

    public function sleeping()
    {
        $seconds_to_sleep = rand(ParsingConfiguration::ONE_PAGE_WAITING_PERIOD / 2, ParsingConfiguration::ONE_PAGE_WAITING_PERIOD);
        info("SLEEPING ..." . $seconds_to_sleep . " seconds");
        sleep($seconds_to_sleep);
    }

    /*
     * update успешной конфигурации
     * */
    public function UpdateAndSave($page_stop, $pages, $countItems)
    {
// получаем мой ip
//        $html = my_curl_response('https://2ip.ru');
//        $pq = phpQuery::newDocument($html);
//        $ip = $pq->find('.ip')->find('big')->text();
//        $this->last_ip = $ip;
        // смотрим дошли ли мы до конца пагинации если да, то обновляем время и счетчик страниц
        info( "page_stop = " . $page_stop . " pages =" . $pages);
        if (($page_stop >= $pages) or ($countItems == 0)) {
            $this->last_timestamp = time();
            // это означает что прошли до конца
            $this->success_stop = 0;
        } else $this->success_stop = $page_stop;
        info("stopped on page " . $page_stop);
        $this->save();
    }

    public function getSource()
    {
        return $this->hasOne(Sources::className(), ['id' => 'id_sources']);
    }


    public static function RuleOfUrl($config, $i)
    {

        switch ($config->id_sources) {
            case 1:
                {
                    if ($i == 1) $url = $config->start_link;
                    else   $url = $url = preg_replace("/{pager}/", "page$i", $config->non_start_link);
                    break;
                }
            case 2:
                {
                    if ($i == 1) $url = $config->start_link;
                    else {
                        // в яндексе нумерация как в массивах начинается с 0!
                        $i--;
                        $url = preg_replace("/{pager}/", "page=$i", $config->non_start_link);
                    }

                    break;
                }
            case 3:
                {
                    if ($i == 1) $url = $config->start_link;
                    else {
                        $url = preg_replace("/{pager}/", "p=$i", $config->non_start_link);
                    }
                    break;
                }
            case 5:
                {
                    // подумать еще !!!
                    if ($i == 1) $url = $config->start_link;
                    else  $url = preg_replace("/{pager}/", "p=$i&", $config->non_start_link);
                    break;
                }


        }

        return $url;

    }

}
