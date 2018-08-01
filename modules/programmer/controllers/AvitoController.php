<?php

namespace app\modules\programmer\controllers;

use app\models\Selectors;
use app\models\ParsingModels\ParsingSync;
use app\models\ParsingModels\Parsing;

class AvitoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTestSyncPage()
    {
        $id_source = AVITO_ID_SOURCE;
        $pageSource = file_get_contents('avitoru/test_category.html');
        $pq_page = \phpQuery::newDocument($pageSource);
        //  Selectors::loadTableClasses($pageSource,$id_source);
      //  Selectors::loadStatClasses($pageSource, $id_source);
        $div_selector = Selectors::findByAlias('AVITO_TABLE_CONTAINER_DIV_CLASS')->selector;
        $pq_containers = $pq_page->find("." . $div_selector);
//        if ($pq_containers) {
//            foreach ($pq_containers as $key=>$pq_container) {
//                info("NUMBER=".$key);
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

        echo Parsing::getTotalCount($pq_page, $id_source);

        return $this->render('index');
    }


}
