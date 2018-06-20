<?php

namespace app\controllers;

use app\models\Addresses;
use app\models\Agents;
use app\models\ControlParsing;
use app\models\Renders;
use app\models\Sale;
use app\models\SaleSimilar;
use app\models\Synchronization;
use app\models\Tags;
use Yii;
use yii\web\Controller;
use app\models\SaleFilters;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class StatusesController extends Controller
{
    const AttributeAndModels = [

    ];

    public function actionSet($id, $modelname, $attrname, $value)
    {
        switch ($modelname) {
            case 'sale':
                $model = Sale::findOne($id);
                $statusWas = $model[$attrname];
                $model[$attrname] = $value;
                $model->save();
                return " Успешно изменили " . $modelname . ":" . $attrname . " c " . $statusWas . " на '" . $value . "'";
            case 'sync':
                $model = Synchronization::findOne($id);
                $statusWas = $model[$attrname];
                $model[$attrname] = $value;
                if (!$model->save()) return $model->getErrors();
                return " Успешно изменили " . $modelname . ":" . $attrname . " c " . $statusWas . " на '" . $value . "'";
            case 'address':
                $model = Addresses::findOne($id);
                if ($attrname != 'delete') {
                    $statusWas = $model[$attrname];
                    $model[$attrname] = $value;
                    $model->save();
                    return " Успешно изменили " . $modelname . ":" . $attrname . " c " . $statusWas . " на '" . $value . "'";
                } else {
                    if ($model->delete()) return "удалили " . $modelname . " id=" . $id;
                }
                break;
            case 'controlparsing':
                $model = ControlParsing::findOne($id);
                if ($attrname != 'delete') {
                    $statusWas = $model[$attrname];
                    $model[$attrname] = $value;
                    $model->save();
                    return " Успешно изменили " . $modelname . ":" . $attrname . " c " . $statusWas . " на '" . $value . "'";
                } else {
                    if ($model->delete()) return "удалили " . $modelname . " id=" . $id;
                }
                break;

            case 'TEMPLATE':
                $sale = Sale::findOne($id);
                $salefilter = SaleFilters::findOne($value); // выюираем id фильтра
                return $salefilter->addToTemplate($attrname, $sale);
            case 'similar':
                $sale = SaleSimilar::findOne($id);
              //  echo "<br> similsr_id = ".$sale->id."<br>";
                $ids = Tags::convertToArray($sale->similar_ids_all);
                if ($attrname == 'moderated') {
                    $sale->moderated = $value;
                    $sale->save();
                 if ($value == SaleSimilar::MODERATED)   $response = [
                        'type' => $modelname, 'message' => "успешно промодерировали " . count($ids) . " записей", 'selector' => ".sim_id_" . $sale->id, 'color' => '#e8f5e9'
                    ];
                 else if ($value == SaleSimilar::MODERATION_ONCALL) $response = [
                     'type' => $modelname, 'message' => "успешно промодерировали " . count($ids) . " записей", 'selector' => ".sim_id_" . $sale->id, 'color' => '#ffcc80'
                 ];
                } else if ($attrname == 'status') {
                    $sale->status = SaleSimilar::SOLD;
                    $sale->save();
                    $response = [
                        'type' => $modelname, 'message' => "успешно отметили удаленными " . count($ids) . " записей", 'selector' => ".sim_id_" . $sale->id, 'color' => '#616161'
                    ];
                }
                return json_encode($response);

            case 'agent':
                $agent = Agents::find()->where(['phone' => $id])->one();
                if (!$agent) {
                    $agent = new Agents();
                    $agent->phone = $id;
                    $agent->updateCounts();
                    $agent[$attrname] = $value;
                    if (!$agent->save()) return my_var_dump($agent->getErrors());
                } else {
                    $agent[$attrname] = $value;
                    if (!$agent->save()) return my_var_dump($agent->getErrors());
                }

                return " Присвоили телефон " . $id . " как агента";


        }


    }

    public function actionText()
    {
        return $this->render('index');

    }
}