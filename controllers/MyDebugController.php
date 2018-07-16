<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\controllers;

use app\models\Actions;
use app\models\ChromeLog;
use app\models\Methods;

use app\models\SynchronizationQuery;
use Curl\Curl;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use app\components\Mdb;
use app\components\MdbActiveSelect;
use app\components\SaleWidget;
use app\components\TagsWidgets;
use app\models\AddressesSearch;
use app\models\AddressImport;
use app\models\Bla;
use app\models\ControlParsing;
use app\models\MyChromeDriver;
use app\models\ParsingConfiguration;
use app\models\ParsingExtractionMethods;
use app\models\Renders;
use app\models\RenderSalefilters;
use app\models\SaleAnalitics;
use app\models\SaleAnaliticsAddress;
use app\models\SaleSimilar;
use app\models\Synchronization;
use app\models\Tags;
use Facebook\WebDriver\WebDriverDimension;
use phpDocumentor\Reflection\DocBlock\Tag;
use yii\db\Command;
use yii;
use app\models\Agents;
use app\models\Sale;
use app\models\SaleHistory;
use yii\web\Controller;
use app\models\ParsingControl;
use app\models\SaleFilters;
use app\models\SaleSearch;
use app\models\Addresses;
use app\models\User;
use phpQuery;
use yii\helpers\ArrayHelper;
use yii\db\Migration;
use app\models\Control;
use app\models\SaleAnaliticsSameAddress;
use app\models\Parsing;
use app\models\Geocodetion;
use yii\db\Expression;
use yii\helpers\Html;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use app\models\Proxy;
use app\models\SaleQuery;

/* @var $sale Sale */
class MyDebugController extends Controller
{

    public function actionGeocodation($id = 0)
    {

        if ($id == 0) {
            $sale = Synchronization::find()
                ->where(['geocodated' => 9])
                ->andwhere(['<>', 'disactive', 1])
                ->orderBy(new Expression('rand()'))
                ->one();
        } else $sale = Synchronization::findOne($id);


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

        //  if (!$sale->save()) my_var_dump($sale->getErrors());

        return $this->render('debug');
    }

    public function actionTestAutocomplete()
    {
        return $this->render('debug');
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
        /* @var $sale Sale*/
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
                echo "<br>".Html::a('manual', yii\helpers\Url::to(['my-debug/auto-set-similar', 'id' => $sale->id]), ['target' => '_blank']);
                echo "<br>".$sale->renderLong_title();
                $sale->similarCheck();
                $sale->save();
            }

        }

        return $this->render('debug');
    }

    public function actionSessions() {

           \app\models\ChromeDriver\MyChromeDriver::getFreeSession();

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

    public function actionWidgets() {


        return $this->render('widgets');
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


}