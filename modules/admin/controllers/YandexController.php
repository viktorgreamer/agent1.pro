<?php

namespace app\modules\admin\controllers;


use app\models\ParsingModels\ParsingSync;
use app\models\Selectors;
use app\models\ParsingModels\Parsing;

class YandexController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTestSyncPage()
    {

        $pageSource = file_get_contents('yandexru/test_category.html');
        $id_source = YANDEX_ID_SOURCE;
        $pq_page = \phpQuery::newDocument($pageSource);
        Selectors::loadTableClasses($pageSource, $id_source);
        $div_selector = Selectors::findByAlias('YANDEX_TABLE_CONTAINER_DIV_CLASS')->selector;
        $pq_containers = $pq_page->find("." . $div_selector);
        if ($pq_containers) {
            foreach ($pq_containers as $pq_container) {
                $parsing = new ParsingSync();
                $parsing->extractTableData($id_source, pq($pq_container));
                $parsing->validate();
                my_var_dump($parsing->toArray());
            }
        }


        Selectors::loadStatClasses($pageSource, $id_source);

        if (Parsing::IsInAvailablePages(2, $pq_page, $id_source)) info(" 2 НАХОДИТСЯ В ДИАПАЗОНЕ ДОСТУПНЫХ СТРАНИЦ");
        else {
            info(" 2 НЕ НАХОДИТСЯ В ДИАПАЗОНЕ ДОСТУПНЫХ СТРАНИЦ", 'danger');
        }
        info("TOTAL_COUNT=" . Parsing::getTotalCount($pq_page, $id_source));

        return $this->render('index');
    }

    public function actionTestValidate()
    {
        $parsing = new Parsing();
        $parsing->title = 'dcs';
        $parsing->price = '234343t';
        $parsing->url = 'CSCDCDDCDCD';
        $parsing->date_start = 32353252;

        $parsing->loggedValidate(Parsing::YANDEX_ID_SOURCE);

        return $this->render('index');
    }


}
