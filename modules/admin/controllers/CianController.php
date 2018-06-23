<?php

namespace app\modules\admin\controllers;

use app\models\Cian;
use app\models\Control;
use app\models\ParsingConfiguration;
use app\models\ParsingExtractionMethods;
use app\models\ParsingModels\Parsing;
use app\utils\P;
use app\models\Selectors;
use app\models\ParsingModels\ParsingSync;

class CianController extends \yii\web\Controller
{

    public function beforeAction($action)
    {

        $module = Control::findOne(1);
        \Yii::$app->params['module'] = $module;

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTestSyncPageOld()
    {

        $html = file_get_contents('cianru/test.html');

        $pq = \phpQuery::newDocument($html);

        Cian::loadTableClasses($html);
        $container_div_class = Cian::$tableContainerDivClass;
        $total_count_div_class = Cian::$totalCountDivClass;
        $pagination_list_div_class = Cian::$paginationListDivClass;

        echo "<br>\$container_div_class =" . $container_div_class;
        echo "<br>\$total_count_div_class =" . $total_count_div_class;
        echo "<br>\$total_count =" . P::ExtractNumders($pq->find("." . $total_count_div_class)->text());
        my_var_dump(Cian::getAvailablePages($pq));
        $pq_divs = $pq->find("div." . $container_div_class);

        echo "<br>COUNTS " . count($pq_divs);

        foreach ($pq_divs as $key => $pq_div) {

            //  echo "<br>".pq($pq_div)->html();
            echo "<br> ROW # " . $key;
            $response = Cian::extractTableContainerData(pq($pq_div));
            my_var_dump($response);

        }


        return $this->render('index');
    }

    public function actionTestSyncPage()
    {
        $id_source = CIAN_ID_SOURCE;
        $pageSource = file_get_contents('cianru/test_category.html');
        $pq_page = \phpQuery::newDocument($pageSource);
        //  Selectors::loadTableClasses($pageSource,$id_source);
        //  Selectors::loadStatClasses($pageSource,$id_source);

        $div_selector = Selectors::findByAlias('CIAN_TABLE_CONTAINER_DIV_CLASS')->selector;
        $pq_containers = $pq_page->find("." . $div_selector);
//        if ($pq_containers) {
//            foreach ($pq_containers as $key => $pq_container) {
//                info("NUMBER=" . $key);
//                $parsing = new ParsingSync();
//                $parsing->extractTableData($id_source, pq($pq_container));
//                $parsing->loggedValidate($id_source);
//                my_var_dump($parsing->toArray());
//
//                // break;
//            }
//        }
        if (Parsing::IsInAvailablePages(2, $pq_page, $id_source)) info(" 2 НАХОДИТСЯ В ДИАПАЗОНЕ ДОСТУПНЫХ СТРАНИЦ");
        else {
            info(" 2 НЕ НАХОДИТСЯ В ДИАПАЗОНЕ ДОСТУПНЫХ СТРАНИЦ", 'danger');
        }
       echo Parsing::getTotalCount($pq_page,$id_source);


        return $this->render('index');
    }

    public function actionTestPage()
    {
        $id_source = CIAN_ID_SOURCE;
        $pageSource = file_get_contents('cianru/test_page.html');
        $pq = \phpQuery::newDocument($pageSource);
        Selectors::loadPageClasses($pageSource, $id_source);
        $parsing = new Parsing();
        $parsing->extractPageData($pq, 'sds', $id_source);
        my_var_dump($parsing->toArray());
        $parsing->loggedValidate($id_source);
        return $this->render('index');
    }


}