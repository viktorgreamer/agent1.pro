<?php

namespace app\modules\admin\controllers;

use app\models\Agents;
use app\models\ChromeDriver\MyChromeDriver;
use app\models\ParsingConfiguration;
use app\models\SaleFilters;
use app\models\SaleQuery;
use app\utils\MyCurl;
use yii\helpers\Inflector;
use yii\helpers\VarDumper;
use Yii;
use app\models\Synchronization;
use yii\db\Expression;
use app\models\Geocodetion;
use app\components\TagsWidgets;
use yii\helpers\Html;

use app\models\Renders;
use app\models\Sale;

class TestsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $result = [];
        $this->getRouteRecrusive(Yii::$app, $result);

        // VarDumper::dump($result, 10, true);
        $allactions = $result;

        return $this->render('index', compact('allactions'));
    }

    public function init()
    {

        $this->layout = "@app/modules/admin/views/layouts/main.php";
        parent::init();

        // custom initialization code goes here
    }


    public function actionTestChromeFreeSessions()
    {
        Yii::$app->params['server'] = 'server2';
        $freesession = MyChromeDriver::getFreeSession();
        if ($freesession) info("FREE SESSION IS " . $freesession, 'primary');
        else info(" NO FREE SESSIONS",'danger');
//        $driver = MyChromeDriver::Open();
//        $driver->get("https://www.yandex.ru/");
//        sleep(5);
//        $driver->get("https://www.google.com/");
        return $this->render('test');
    }
    public function actionTestChromeProxy()
    {
        Yii::$app->params['server'] = 'server2';
          $driver = MyChromeDriver::Open();
        $driver->get("https://www.yandex.ru/");
        sleep(5);
        $driver->get("https://www.google.com/");
        return $this->render('test');
    }


    public function actionTagsAutoLoad($id = 0)
    {
        //  $_SESSION['id'] = 1;
        info("lost = " . Synchronization::find()->where(['IS', 'tags_id', NULL])->andFilterWhere(['tags_autoload' => 0])->count(), 'alert');

        if ($id == 0) {
            $sale = Synchronization::find()
                //  ->where(['tags_id' => ''])
                ->where(['IS', 'tags_id', NULL])
                //->orderBy(new Expression('rand()'))
                ->limit(100)
                ->andFilterWhere(['tags_autoload' => 0])
                ->all();
            if (!$sale) return $this->render('debug');
        } else $sale = Synchronization::findOne($id);
        if (count($sale) > 1) {
            foreach ($sale as $item) {
                echo $item->renderLong_title();

                echo $item->renderSource() . " tags-auto-load " . $item->description;
                $item->AutoLoadTags();
                echo "<br>" . TagsWidgets::widget(['tags' => $item->tags]);
                $item->tags_autoload = 1;
                $item->sync = 2;
                $item->save();
            }
        } else {
            echo $sale->renderLong_title();

            echo $sale->renderSource() . " tags-auto-load " . $sale->description;
            $sale->AutoLoadTags();
            echo "<br>" . TagsWidgets::widget(['tags' => $sale->tags]);
            $sale->sync = 2;
            $sale->save();
        }


        return $this->render('debug');
    }

    public function actionAutoModerate($id = 0)
    {
        //  $_SESSION['id'] = 1;
        info("lost to moderate = " . Sale::find()->where(['moderated' => 2])->andwhere(['IS NOT', 'tags_id', NULL])->andwhere(['<>', 'similar_ids', ''])
                ->andwhere(['IS NOT', 'similar_ids', NULL])->count(), 'alert');

        if ($id == 0) {
            $sale = Sale::find()
                ->from(['s' => Sale::tableName()])
                ->joinWith(['agent AS agent'])
                ->joinWith(['addresses AS address'])
                //  ->where(['tags_id' => ''])
                ->where(['s.moderated' => 2])
                ->andwhere(['IS NOT', 's.tags_id', NULL])
                ->andwhere(['IS NOT', 's.similar_ids', NULL])
                ->andwhere(['<>', 's.similar_ids', ''])
                ->orderBy(new Expression('rand()'))
                ->limit(20)
                ->all();

        } else $sale = Synchronization::findOne($id);
        if (count($sale) > 1) {
            foreach ($sale as $item) {
                echo Mdb::ProgresBar();
                $simitar_sales = explode(",", $item->similar_ids);
                //  my_var_dump($simitar_sales);
                $simitar_sales = Sale::find()->where(['in', 'id', $simitar_sales])->all();
                $common_tags = [];
                $common_tags = array_merge($common_tags, $item->tags);
                foreach ($simitar_sales as $simitar_sale) {
                    $common_tags = array_merge($common_tags, $simitar_sale->tags);
                    echo "<br>description->" . $simitar_sale->description;
                    echo "<br>TAGS ->" . TagsWidgets::widget(['tags' => $simitar_sale->tags]);
                }
                echo "<br>COMMON_TAGS -><br>" . TagsWidgets::widget(['tags' => array_unique($common_tags)]);
                echo Yii::$app->view->render('@app/views/sale/_sale-table', ['model' => $item]);
                $item->tags_id = Tags::convertToString(array_unique($common_tags));
                echo "<br>COMMON_TAGS -><br>" . TagsWidgets::widget(['tags' => $item->all_tags]);


                $item->sync = 2;

                // $item->save();
            }
        }
        //else {
//            echo $sale->renderLong_title();
//
//            echo $sale->renderSource() . " tags-auto-load " . $sale->description;
//            $sale->AutoLoadTags();
//            echo "<br>" . TagsWidgets::widget(['tags' => $sale->tags]);
//            $sale->sync = 2;
//            $sale->save();
//        }


        return $this->render('debug');
    }

    public function actionAutoSetSimilar($id = 0)
    {
        /* @var $sale Sale */
        //  $_SESSION['id'] =


        if ($id == 0) {
            $query = SynchronizationQuery::find(READY_FOR_SALESIMILAR_CHECK);

            $count = $query->count();

            info("lost = " . $count, 'alert');
            $sales = $query
                // ->andWhere(['not in', 'disactive', [1, 2]])
                // ->orderBy(new Expression('rand()'))
                ->limit(20)
                // ->andFilterWhere(['tags_autoload' => 0])
                ->all();
            if (!$sales) return $this->render('debug');
        } else {
            $sale = Synchronization::findOne($id);
            info(" id=" . $sale->id);
            echo $sale->renderLong_title();
            $sale->similarCheck();

        }
        if ($sales) {
            foreach ($sales as $sale) {
                info(" id=" . $sale->id);
                echo "<br>" . Html::a('manual', yii\helpers\Url::to(['my-debug/auto-set-similar', 'id' => $sale->id]), ['target' => '_blank']);
                echo "<br>" . $sale->renderLong_title();
                $sale->similarCheck();
                $sale->save();
            }

        }

        return $this->render('debug');
    }

    public function actionResetControllers()
    {

        $controlParsing = new ControlParsing();
        $controlParsing->status = ControlParsing::BROKEN;
        $controlParsing->date_start = time() - 360;
        if (!$controlParsing->save()) my_var_dump($controlParsing->getErrors());
        //  my_var_dump($controlParsing);
        ControlParsing::changeControllers(ControlParsing::BROKEN, ControlParsing::FULL_BROKEN, 500, 500);


        return $this->render('debug');

    }

    public function actionTestCurlAvito()
    {
        $curl = new Curl();
        $curl->setUserAgent("Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.117 Safari/537.36");
        $curl->get("https://www.avito.ru/");
        $cookies = $curl->getResponseCookies();
        sleep(2);
        my_var_dump($cookies);
        $curl->setCookies($cookies);
        $curl->get("https://m.avito.ru/velikiy_novgorod/kvartiry/4-k_kvartira_110_m_56_et._1690325690");
        info(" COOKIE AFTER REQUEST");
        $cookies = $curl->getResponseCookies();
        my_var_dump($cookies);

        $tree = str_get_html($curl->response);
        $domain = "https://m.avito.ru/";
        $href = $tree->find("a.js-action-show-number", 0)->href;
        info("  requets = " . $domain . $href . "?async");

        $curl->setReferrer('https://m.avito.ru/velikiy_novgorod/kvartiry/4-k_kvartira_110_m_56_et._1690325690');
        $curl->setHeader('x-requested-with', 'XMLHttpRequest');
        $curl->setHeader('accept', 'application/json');
        $curl->setHeader('accept-encoding', 'gzip, deflate, br');
        $curl->setHeader('accept-language', 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6');
        $curl->setHeader(':authority', 'm.avito.ru');
        $curl->setHeader(':method', 'GET');
        $curl->setHeader(':path', $href . "?async");
        $curl->setHeader(':scheme', 'https');
        sleep(2);
        $curl->setCookies($cookies);
        my_var_dump($curl->requestHeaders);
        $curl->get($domain . $href . "?async");
        my_var_dump($curl->responseHeaders);
        $cookies = $curl->getResponseCookies();
        my_var_dump($cookies);

        info(" TELEPHONE = " . mb_detect_encoding($curl->response));
        info(" TELEPHONE = " . $curl->response);
        $curl->close();
        echo $curl->response;

        return $this->render('debug');

    }

    public function actionAvitoPhones()
    {
        $querys = Sale::find();
        $querys->where(['phone1' => ''])->select('id')
            ->andwhere(['id_sources' => 3]);

        info(" COUNT SALE LOST = " . $querys->count());
        $query = Synchronization::find();
        $query->where(['OR',
            ['IS', 'phone1', NULL],
            ['phone1' => ''],
        ])
            ->andWhere(['debug_status' => 6])
            ->andwhere(['id_sources' => 3]);

        info(" COUNT Synchronisation LOST = " . $query->count());

        $syncs = $query->limit(200)->all();
        foreach ($syncs as $sync) {
            $sale = Sale::findOne($sync->id);
            info("PHONE1" . $sale->phone1);
            if ($sale->phone1) {
                $sync->phone1 = $sale->phone1;

            }
            $sync->debug_status = 7;
            if (!$sync->save()) my_var_dump($sync->getErrors());
        }

        return $this->render('debug');
    }

    /* @var $similar \app\models\SaleSimilar */
    public function actionManageSimilar()
    {
        $sale = Synchronization::find()->where(['like', 'id_in_source', '5012834758790249217'])->one();
        if ($sale) {
            Sale::find()->where(['like', 'id_in_source', '5012834758790249217'])->one()->delete();
            $similar = $sale->tryToFindSimilar([$sale->id]);
            echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);
            info('IDS');
            echo $similar->similar_ids;
            info('IDS_ALL');
            echo $similar->similar_ids_all;
            $similar->removeID($sale->id);
            info('IDS');
            echo $similar->similar_ids;
            info('IDS_ALL');
            echo $similar->similar_ids_all;
            $similar->save();
            $sale->delete();

        } else info('SALE IS NOT EXISTS');


        return $this->render('debug');

    }


    public function actionTags()
    {

        return $this->render('debug');
    }

    public function actionReorderTags()
    {
        info("ОСТАЛОСЬ " . SaleSimilar::find()->where(['IS', 'debug_status', NULL])->count());
        $similars = SaleSimilar::find()->where(['IS', 'debug_status', NULL])->limit(25)->all();
        foreach ($similars as $similar) {
            echo Mdb::ProgresBar();
            echo "<br>" . $similar->id;
            $new_tags = array_unique(array_merge(Tags::find()
                ->select('id')
                ->where(['in', 'id', $similar->tags])
                ->groupBy('a_type')
                ->column(),
                Tags::find()
                    ->select('id')
                    ->where(['in', 'id', $similar->tags])
                    ->andWhere(['OR',
                            ['IS', 'a_type', NULL],
                            ['a_type' => 0]]
                    )->column()));
            echo TagsWidgets::widget(['tags' => $similar->tags]);
            info('СТАЛО<br>');

            echo TagsWidgets::widget(['tags' => $new_tags]);
            $similar->tags_id = Tags::convertToString($new_tags);
            $similar->debug_status = 1;
            $similar->save();;
        }
        return $this->render('debug');

    }

    public function actionHighlightingTags()
    {
        info("ОСТАЛОСЬ " . SaleSimilar::find()->where(['debug_status' => 1])->count());
        $similars = SaleSimilar::find()->where(['debug_status' => 1])->limit(2)->all();
        foreach ($similars as $similar) {
            echo Mdb::ProgresBar();
            echo " SIMILAR ID =" . $similar->id;
            $sales = Sale::find()->where(['in', 'id', Tags::convertToArray($similar->similar_ids_all)])->all();
            $realtags = [];
            foreach ($sales as $sale) {

                $realtags = array_merge($realtags, $sale->AutoLoadTags(true));

                echo "<br>" . $sale->description;
                $sale_id = $sale->id;
                echo "<hr>";
            }

            foreach (Tags::find()->where(['in', 'id', array_unique($realtags)])->all() as $tag) {
                echo Tags::renderActiveTag($sale_id, $tag, $similar->tags, 'sale', $tag->a_type) . " ";
            }

            echo "<br>";
            echo "<br>" . $similar->id;
            $new_tags = array_unique(array_merge(Tags::find()
                ->select('id')
                ->where(['in', 'id', $similar->tags])
                ->groupBy('a_type')
                ->column(),
                Tags::find()
                    ->select('id')
                    ->where(['in', 'id', $similar->tags])
                    ->andWhere(['OR',
                            ['IS', 'a_type', NULL],
                            ['a_type' => 0]]
                    )->column()));
            echo TagsWidgets::widget(['tags' => $similar->tags]);
            info('СТАЛО<br>');

            echo TagsWidgets::widget(['tags' => $new_tags]);
            $similar->tags_id = Tags::convertToString($new_tags);
            $similar->debug_status = 2;// $similar->save();
            ;
        }
        return $this->render('debug');

    }

    public function actionControlTest()
    {
        info('ПРОВЕРКА ВРЕМЕНИ ПОСЛЕДНЕЙ ПРОВЕРКИ МОДУЛЯ');
        echo "<br>" . date("F j, Y, g:i a", ParsingConfiguration::LAST_TIMESTAMP(1));

        $module = Control::findOne(1);
        Yii::$app->params['module'] = $module;
        Synchronization::CheckLostLinks();

        return $this->render('debug');

    }

    public function actionChromeLog()
    {
        info('проверка работы CHROMEDRIVERLOG');
        $driver = \app\models\ChromeDriver\MyChromeDriver::Open();
        info(" session ID = " . $driver->getSessionID());

        $driver->get('https://yandex.ru/internet/');
        $model = new ChromeLog();
        my_var_dump(Yii::$app->cache->set('driver1', $model));
        //  my_var_dump(Yii::$app->cache->get('driver1'));
        if ($model == Yii::$app->cache->get('driver1')) echo " драйвера равны";
//        $chromelog = new ChromeLog();
//        $chromelog->session_id = $driver->getSessionID();
//        $chromelog->model = serialize($driver);
//        $chromelog->save();
        //  $driver->quit();

        // return $this->render('debug');

    }

    public function actionChromeLog2()
    {
        // info('проверка работы CHROMEDRIVERLOG');
        // $driver = \app\models\ChromeDriver\MyChromeDriver::Open();
        // info (" session ID = ". $driver->getSessionID());
        $driver = (object)Yii::$app->cache->get('driver1');;
        var_dump($driver);
        $driver->get('https://yandex.ru/');

        $driver->quit();

        // return $this->render('debug');

    }

    public function actionProxyTest()
    {
        $count_active_process = 0;
        sleep(5);
        $response = shell_exec('tasklist | find /I "chrome.exe"');
        // my_var_dump($response);
        preg_match_all("/chrome.exe/", $response, $output);
        $count_of_process = count($output[0]) / 6;
        // my_var_dump($output);
        $active_process = ParsingControl::find()->where(['status' => ParsingControl::STATUS_ACTIVE])->andwhere(['in', 'type', ['PARSING_SYNC', 'PARSYNG_NEW', 'DETAILED_PARSING', 'PARSING_AVITO_PHONES']])->all();
        if ($active_process) {
            info('ЕСТЬ АКТИВНЫЕ ПРОЦЕССЫ ПАРСИНГА');
            foreach ($active_process as $process) {
                info(" ACTIVE " . $process->type);
                $count_active_process = count($active_process);
            }

        } else {
            info('НЕТ АКТИВНЫХ ПРОЦЕССОВ ПАРСИНГА', 'alert');
            // info("НО ЕСТЬ " . $active_process . " ПОВИСШИХ ПРОЦЕССА ", 'alert');
            $lost_process = $count_of_process - $count_active_process;
            info(" ЕСТЬ " . $lost_process . " ПОВИСШИХ ПРОЦЕССА ", 'alert');
            if ($lost_process > 0) {
                // $response = shell_exec('taskkill /IM "chrome.exe"');
                info(" УДАЛИЛИ ИХ ", 'alert');
            }


        }
        info(" АКТИВНЫХ ПРОЦЕССОВ  " . $count_of_process, 'alert');
        if ($count_of_process >= 12) {

            $active_controls = Control::find()->where(['status' => 2])->all();
            if ($active_controls) {
                foreach ($active_controls as $control) {
                    $control->parsing_status = Control::STATUS_STOP_PARSING;
                    info("ДЕЛАЕМ ПАУЗУ В ПАРСИНГЕ" . $count_of_process, 'alert');
                    $control->save();
                }
            }
        }
        $lost_process = $count_of_process - $count_active_process;


        info(" count = " . count($output[0]));

        return $this->render('debug');

    }

    public function actionSimilarTest()
    {
        info("COUNTS =" . SaleSimilar::find()->where(['debug_status' => 3])->count());
        $similars = SaleSimilar::find()->where(['debug_status' => 3])->limit(100)->all();
        foreach ($similars as $similar) {
            $similar->id_sources = '';
            echo Mdb::ProgresBar();
            $id_sources = Sale::find()->select('id_sources')->distinct()->where(['in', 'id', array_unique(array_merge(Tags::convertToArray($similar->similar_ids), Tags::convertToArray($similar->similar_ids_all)))])->andwhere(['disactive' => Sale::ACTIVE])->column();
            if (sort($id_sources)) {
                my_var_dump($id_sources);
                foreach ($id_sources as $id_source) {

                    info(" ОБЪЕК АКТИВЕН В " . Sale::ID_SOURCES[$id_source]);
                }
                $similar->id_sources = Tags::convertToString($id_sources);
                $not_id_sources = array_diff([1, 2, 3, 5], $id_sources);
                my_var_dump($not_id_sources);
                foreach ($not_id_sources as $id_source) {
                    info(" ОБЪЕК НЕАКТИВЕН В " . Sale::ID_SOURCES[$id_source], 'alert');
                }
            }
            info(" id_sources" . $similar->id_sources);
            $similar->debug_status = 4;
            if (!$similar->save()) my_var_dump($similar->getErrors());
        }
        return $this->render('debug');


    }

    public function actionActionTest()
    {
        info("ActiondTest");

        $name_lists = Actions::getNameAttribute(Actions::SALEFILTER);
        //  my_var_dump($name_lists);
        $filters = SaleFilters::find()->where(['id' => 186])->all();
        if ($filters) {
            foreach ($filters as $filter) {
                info(" name = " . $filter->name . " ID = " . $filter->id);
                //  echo " <br>IDs = ".$filter->white_list_id;
                foreach ($name_lists as $name_list) {
                    info(" NameAttribute = " . $name_list, 'primary');
                    $ids = Methods::convertToArrayWithBorders($filter[$name_list]);
                    echo " <br>" . span($filter[$name_list]);
                    foreach ($ids as $id) {
                        echo "    " . Html::a($id, yii\helpers\Url::toRoute(['actions/toggle', 'id_parent' => $filter->id, 'id_model' => Actions::SALEFILTER, 'id_attr' => Actions::SALEFILTER_SIMILAR_BLACK_LIST_ID, 'id' => $id]), ['target' => '_blank']);
                    }
                    echo "<hr>";

                }
                info(" OnControls_ids", 'primary');
                $OnControls = $filter->onControls;
                foreach ($OnControls as $item) {
                    info(" sim_id = " . $item->id_similar . " price = " . $item->price);

                }


            }
        }
        info("ActiondTest");
        info("ActiondTest");


        // echo Html::a('text', yii\helpers\Url::toRoute(['actions/remove', 'id_from' => 84,'id_model' => Actions::SALEFILTER, 'id_attr' => Actions::SALEFILTER_WHITE, 'id' => 20294]));

        return $this->render('debug');

    }

    public function actionWidgets()
    {

        return $this->render('widgets');
    }

    public function actionParsingAvitoPhones($limit = 20)
    {
        $type = 'PARSING_AVITO_PHONES';
        $time_start = time();
        // берем объекты
        $sales = Sale::find()->where(['id_sources' => 3])->limit(10)->orderBy(new Expression('rand()'))->all();


        if (!$sales) return false;


        foreach ($sales as $sale) {
            // открываем ссылку ( если нет открывается то выходим)

            $url = $sale->url;
            $url = preg_replace("/www.avito/", "m.avito", $url);

            $message .= "<hr>";
            $message .= "<br>" . Html::a($url, $url);

            $curl = new MyCurl();
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
            // my_var_dump($curl->responseHeaders);
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;
            //  echo  $response;
            $curl->close();

            $response = str_get_html($response);

            if (!preg_match("/js-action-show-number/", $response)) {
                file_put_contents("avito/ERROR_" . str2url($url) . ".html", $response);

                $message .= "<br>PAGE IZ NOT EXISTS";
                continue;
            }

            file_put_contents("avito/" . str2url($url) . ".html", $response);

            $hash = $response->find('.js-action-show-number', 0)->href;

            $message .= "<br> HASH IS " . $hash;

            $url_phone = "https://m.avito.ru/" . $hash . "?async";

            $curl = new MyCurl();

            $headers = [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'DNT' => '1',
                'Referer' => ".$url.",
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
            ];

            sleep(rand(4, 6));


            $curl->setHeaders($headers);
            $curl->getUrlWithCookies($url_phone);
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;
            $response = json_decode($response);
            $curl->close();


            // info(" TELEPHONE = " . $response->phone);

            preg_match("/\d+\s\d{3}\s\d{3}-\d{2}-\d{2}/", $response->phone, $output_array);

            $phone = preg_replace("/\D+/", "", $output_array[0]);
            $phone = preg_replace("/\A7/", "8", $phone);

            $message .= "<br> PHONE IS =" . $phone;

            echo $message;

            sleep(rand(2, 4));
            // if (!$sale->save()) my_var_dump($sale->getErrors());


        }

        file_put_contents("avito/" . time() . ".html", $message);

        return true;

    }

    public function actionMyIp()
    {
        $timeStart = time();
        $n = 1;
        echo "<br>IP=" . json_decode(file_get_contents('https://api.ipify.org?format=json'))->ip;


        echo "<br> TIME=" . (time() - $timeStart) / $n;

    }

    public function actionTestCian()

    {
        $urls = [
            1 => 'https://novgorod.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&p={page}&region=4694&room1=1',
            2 => 'https://novgorod.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&p={page}&region=4694&room2=1',
            3 => 'https://novgorod.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&p={page}&region=4694&room3=1',
            // 4 => 'https://novgorod.cian.ru/cat.php?deal_type=sale&engine_version=2&offer_type=flat&p={page}&region=4694&room4=1',
        ];

        $number = rand(1, 4);

        $start = rand(1, 30);
        $pages = range($start, $start + 20);
        foreach ($pages as $page) {
            $url = preg_replace('/{page}/', $page, $urls[$number]);

            $curl = new MyCurl();
            $headers = [
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'DNT' => '1',
                'Referer' => 'https://novgorod.cian.ru/',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
                'Pragma' => 'akamai-x-cache-on, akamai-x-cache-remote-on, akamai-x-check-cacheable, akamai-x-get-cache-key, akamai-x-get-extracted-values, akamai-x-get-ssl-client-session-id, akamai-x-get-true-cache-key, akamai-x-serial-no, akamai-x-get-request-id,akamai-x-get-nonces,akamai-x-get-client-ip,akamai-x-feo-trace'];
            $curl->setHeaders($headers);
            $curl->getUrlWithCookies($url);
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;
            $curl->close();
            sleep(rand(2, 4));
            file_put_contents("cianru/" . time() . "_" . str2url($url) . ".html", $response);
        }


    }

    public function actionTestCianPage()

    {

        $sales = Sale::find()->where(['id_sources' => 5])->limit(5)->orderBy(new Expression('rand()'))->all();

        foreach ($sales as $sale) {

            $curl = new MyCurl();
            $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
            $curl->setOpt(CURLOPT_PROXY, '91.218.246.12:44141');
            $curl->get("http://ip-api.com/json");
            $ip = ($curl->response)->query;
            echo "<br>IP=" . ($curl->response)->query;

            $curl->close();
            $url = $sale->url;
            $curl = new MyCurl();
            $headers = [
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'DNT' => '1',
                'Referer' => 'https://novgorod.cian.ru/',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
                'Pragma' => 'akamai-x-cache-on, akamai-x-cache-remote-on, akamai-x-check-cacheable, akamai-x-get-cache-key, akamai-x-get-extracted-values, akamai-x-get-ssl-client-session-id, akamai-x-get-true-cache-key, akamai-x-serial-no, akamai-x-get-request-id,akamai-x-get-nonces,akamai-x-get-client-ip,akamai-x-feo-trace'];
            $curl->setHeaders($headers);
            $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
            $curl->setOpt(CURLOPT_PROXY, '91.218.246.12:44141');
            $message = "<br>" . Html::a($url, $url);


            $curl->getUrlWithCookies($url);
            if ($curl->responseHeaders['content-encoding'] == 'gzip') $response = gzdecode($curl->response);
            else $response = $curl->response;
            $curl->close();
            sleep(rand(2, 5));
            file_put_contents("cianru/page_" . time() . "_" . str2url("ip_" . $ip . "_" . $url) . ".html", $response . $message);
        }


    }


    public function actionAgentsCheckPhone()
    {
        /* @var $sale Sale */
        /* @var $sync Synchronization */
        $debug_status = 'drsfghgjfgbc';
        $query = New SaleQuery();
        $salefilter = new SaleFilters();
        $salefilter->id = 564;
        $query->relations();
        //  $query->andWhere(['<>', 's.debug_status', $debug_status]);
        $query->andWhere(['OR',
                //  ['<>','agent.person_type', Agents::PERSON_TYPE_AGENT],
                ['IS', 'agent.person_type', NULL]]
        );

        info("LOST=" . $query->count());
        $sales = $query->limit(20)->all();
        foreach ($sales as $sale) {
            echo $sale->renderLong_title();
            echo $sale->renderContacts();

            $sync = Synchronization::findOne($sale->id);

            $sync->checkForAgents();


            $sync->debug_status = $debug_status;
            //   $sync->save();
        }

        // $sync->updateToSale();


        return $this->render('test');

    }

    public function actionCacheSync()
    {
        $id_category = 9;
        info("PC ID =".ParsingConfiguration::findOne($id_category)->id);
        $cashed_items = Yii::$app->cache->get("CACHED_ID_CATEGORY_" . $id_category);
        if (!$cashed_items) {
            info("GET DATA FROM BASE",DANGER);
            $cashed_items = Synchronization::find()
                ->select('id,id_address, id_in_source, address_line, price,date_of_check,url,date_start,disactive')
                ->where(['id_category' => $id_category])
                //  ->asArray()
               // ->limit(10)
                ->indexBy('id_in_source')
                ->all();
            Yii::$app->cache->set("CACHED_ID_CATEGORY_" . $id_category, $cashed_items, 50);

        } else {
            info(" GET CASHED DATA",SUCCESS);
        }


//        foreach ($cashed_items as $item) {
//
//            Synchronization::updateAll(['original_date' => time()], ['id' => $item['id']]);
//        }


        info("COUNT=" . count($cashed_items));
          my_var_dump($cashed_items['1080751879']->toArray());
        return $this->render('test');
    }

    public function actionCaptcha()
    {
        $driver = \app\models\ChromeDriver\MyChromeDriver::Open("185.233.201.165");
        $driver->get('https://novgorod.cian.ru/kupit-2-komnatnuyu-kvartiru/');
        sleep(2);
        if (preg_match("/cian.ru\/captcha\/?/", $driver->getCurrentURL())) info(" ПОЯВИЛАСЬ RECQPTCHA");
        //  echo $driver->getPageSource();

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

                info(" CONGRATULATIONS!!!!!");

            } else info($response);

        }


    }

    /**
     * Lists all Route models.
     *
     * @return mixed
     */

    public function actionGeocodation($id = 0)
    {

        if ($id == 0) {
            $sale = Synchronization::find()
                ->where(['geocodated' => 9])
                ->andwhere(['<>', 'disactive', 1])
                ->orderBy(new Expression('rand()'))
                ->one();
        } else $sale = Synchronization::findOne($id);

        if ($sale) {
            echo $sale->renderSource() . " пытаемся прогеокодировать адрес: " . $sale->address;
            $sale->AutoLoadTags();
            echo TagsWidgets::widget(['tags' => $sale->tags]);
            echo Html::a('OneGeocodation', ['my-debug/geocodation', 'id' => $sale->id], ['target' => '_blank']);

            // $sale->house_type = 2;
            // $sale->floorcount = 5;
            $sale->geocodate();
            echo $sale->renderLong_title();
            $customurl = "https://yandex.ru/maps/?mode=search&text=" . $sale->coords_x . ", " . $sale->coords_y; //$model->id для AR
            echo \yii\helpers\Html::a('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>', $customurl,
                ['title' => Yii::t('yii', 'yandex maps'),
                    'data-pjax' => '0',
                    'target' => '_blank']);

        }

        //  if (!$sale->save()) my_var_dump($sale->getErrors());

        return $this->render('test');
    }


    /**
     * Get route(s) recrusive
     *
     * @param \yii\base\Module $module
     * @param array $result
     */
    private function getRouteRecrusive($module, &$result)
    {
        try {
            foreach ($module->getModules() as $id => $child) {
                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecrusive($child, $result);
                }
            }
            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result);
            }
            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result);
            $result[] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    /**
     * Get list controller under module
     *
     * @param \yii\base\Module $module
     * @param string $namespace
     * @param string $prefix
     * @param mixed $result
     *
     * @return mixed
     */
    private function getControllerFiles($module, $namespace, $prefix, &$result)
    {
        $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    /**
     * Get list action of controller
     *
     * @param mixed $type
     * @param string $id
     * @param \yii\base\Module $module
     * @param string $result
     */
    private function getControllerActions($type, $id, $module, &$result)
    {
        try {
            /* @var $controller \yii\base\Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $result[] = '/' . $controller->uniqueId . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }

    /**
     * Get route of action
     *
     * @param \yii\base\Controller $controller
     * @param array $result all controller action.
     */
    private function getActionRoutes($controller, &$result)
    {
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $result[] = $prefix . Inflector::camel2id(substr($name, 6));
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
    }


}
