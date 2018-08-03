<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "parsingcontrol".
 *
 * @property integer $id
 * @property integer $date_start
 * @property integer $date_finish
 * @property string $type
 * @property integer $id_sources
 * @property integer $status
 * @property integer $config_id
 * @property integer $params
 */
class ControlParsing extends \yii\db\ActiveRecord
{
    const STATUS = [
        1 => 'ACTIVE',
        2 => 'SUCCESS',
        3 => 'BROKEN',
        4 => 'FULL_BROKEN',
        5 => 'UNKNOWN_ERROR'
    ];
    const STATUS_COLORS = [
        1 => 'success',
        2 => 'primary',
        3 => 'danger',
        4 => 'black',
        5 => 'warning'
    ];
    const ACTIVE = 1;
    const SUCCESS = 2;
    const BROKEN = 3;
    const FULL_BROKEN = 4;
    const UNKNOWN_ERROR = 5;

    const PARSING_TYPES_OF_CONTROL = ['PARSING_AVITO_PHONES', 'DETAILED_PARSING', 'PARSING_NEW', 'PARSING_SYNC'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parsingcontrol';
    }

    public function formName()
    {
        return '';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function deleteMissedControllers($duration)
    {
        $Controllers = ControlParsing::find()
            ->where(['OR',
                ['<', 'date_check', time() - $duration],
                ['IS', 'date_check', NULL]
            ])
            ->andWhere(['status' => ControlParsing::ACTIVE])
            ->all();
        if ($Controllers) {
            info(" THERE ARE MISSED PARSING CONTROLLER OVER " . $duration . " sec.");
            foreach ($Controllers as $controller) {
                $controller->status = ControlParsing::BROKEN;
                $controller->save();
            }
        } else   info(" THERE ARE NO MISSED PARSING CONTROLLER OVER " . $duration . " sec.", 'PRIMARY');


    }

    public static function deleteOverControllersNew($status = 0, $duration = 84600)
    {
        if ($status) {
            ControlParsing::deleteAll(['AND',
                    ['<', 'date_start', (time() - $duration)],
                    ['status' => $status]]
            );
        }
    }
    public static function closeBroken() {
          $interval = 15;
        $brokenControls = ControlParsing::find()
            ->select(['id','type','date_start','ids','ids_sources'])
            ->where(['in', 'status', [ControlParsing::BROKEN]])
            ->andWhere([">", 'date_start', (time() - $interval * 60)])
            // ->groupBy('type')
            ->asArray()
            ->all();

       // my_var_dump($brokenControls);
        foreach (Control::mapTypesControls() as $key => $control) {
            if ($array = array_filter($brokenControls, function ($item) use ($key) {
                return $item['type'] == $key;
            })) {
                if (count($array) > 5) {
                    $message = " CONTROL ".$control." IS BROKEN ".count($array)." РАЗ ЗА ПОСЛЕДНИЕ ".$interval." МИНУТ";
                    info($message);
                    Notifications::VKMessage($message);
                    if ($ids_broken = ArrayHelper::getColumn($array,'id')) {
                        $ids = [];
                        foreach ($ids_broken as $item) {

                            array_push($ids,intval($item));
                        }

                    }


                    my_var_dump($ids_broken = array_values ($ids_broken));
                    ControlParsing::updateAll(['status' => ControlParsing::FULL_BROKEN],['in','id',$ids]);
                   // AgentPro::throwError();

                }
              }


        }

        info("COUNT = " . count($brokenControls));
    }



    public static function deleteOverControllers($status = 0, $duration = 5000, $limit = 20, $field = 'date_start')
    {
        if (in_array($status, [1, 2, 3, 4, 5])) {
            $Controllers = ControlParsing::find()
                ->select('id')
                ->where(['status' => $status])
                ->andwhere(['>', $field, time() - $duration])
                ->limit($limit)
                ->orderBy(['date_start' => SORT_DESC])
                ->column();
            $toDeleteControllers = ControlParsing::find()
                ->where(['status' => $status])
                ->andWhere(['not in', 'id', $Controllers])
                ->all();
            foreach ($toDeleteControllers as $deleteController) {
                $deleteController->delete();
            }
        }
    }

    public static function changeControllers($status = 0, $new_status = 0, $duration = 5000, $limit = 20)
    {
        if (in_array($status, [1, 2, 3, 4, 5])) {
            $Controllers = ControlParsing::find()
                ->select('id')
                ->where(['status' => $status])
                ->andwhere(['>', 'date_start', time() - $duration])
                ->limit($limit)
                ->orderBy(['date_start' => SORT_DESC])
                ->column();
            $toChangeControllers = ControlParsing::find()
                ->where(['status' => $status])
                ->andWhere(['not in', 'id', $Controllers])
                ->all();
            foreach ($toChangeControllers as $Controller) {
                $Controller->status = $new_status;
                $Controller->save();
            }
        }
    }

    public static function resetParsingController($type = 'DEFAULT', $params = [])
    {
        // удаление лишних успешных контролерров
        // ControlParsing::deleteOverControllers(self::SUCCESS, 360, 20);
        // удаление сстарынх контролееров
        $oldControllers = ControlParsing::find()->andWhere(['<', 'date_start', time() - 48 * 60 * 60])->orderBy(['date_start' => SORT_DESC])->all();

        foreach ($oldControllers as $item) {
            //  info("удалили старый контроллер", 'alert');
            $item->delete();
        }


    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_start', 'date_finish', 'id_sources', 'status', 'config_id','type'], 'integer'],
            [['params'], 'string', 'max' => 10000],
        ];
    }

    public static function controlBroken()
    {
        // фиксирование сломанной конфигурации 300 секундной давности
        $brokenControllers_300sec = ControlParsing::find()->where(['status' => 3])->andWhere(['>', 'date_start', time() - 500]);
        $query_brokenControllers_300sec = clone $brokenControllers_300sec;
        if ($brokenControllers_300sec->count() > 4) {
            foreach ($brokenControllers_300sec->all() as $item) {
                info("<br> ПОСТАВИЛИ НА УЧЕТ" . $item->type . " статус" . $item->status . " 300 сек давности", 'alert');
                $item->status = 4;
                $item->save();
            }
        }
        $delete_brokenControllers_not_in_500 = ControlParsing::find()
            ->where(['status' => 3])
            ->andWhere(['not in', 'id', $query_brokenControllers_300sec->select('id')->column()])
            ->all();
        foreach ($delete_brokenControllers_not_in_500 as $item) {
            info(" удалили" . $item->type . " статус" . $item->status, 'alert');
            $item->delete();
        }

    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'type' => 'Type',
            'id_sources' => 'Id Sources',
            'status' => 'Status',
            'config_id' => 'Config ID',
        ];
    }

    /**
     * @inheritdoc
     * @return ParsingcontrolQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ParsingcontrolQuery(get_called_class());
    }

    public static function getBusyIds($type, $ip = null)
    {

            $ids_row = ArrayHelper::getColumn(
                ControlParsing::find()
                    ->where(['type' => $type])
                    ->andwhere(['status' => self::ACTIVE])
                    ->andwhere(["IS NOT", 'ids', null])
                    ->andFilterWhere(['ip' => $ip])
                    ->all(),
                'ids');
            //  my_var_dump($ids_row);

            $ids = [];
            if ($ids_row) {
                foreach ($ids_row as $row) {
                    foreach (preg_split("/,/", $row) as $id) {
                        array_push($ids, $id);
                    }


                }
            }
            return $ids;


//        else {
//            $id = ControlParsing::find()
//                ->select('id')
//                ->where(['type' => $type])
//                ->andwhere(['status' => Self::ACTIVE])
//                ->andFilterWhere(['ip' => $ip])
//                ->column();
//           if ($id) return [$id];
        //   }
    }

    public static function getBusyIdConfigs($type, $ip = null)
    {
        $id = ControlParsing::find()
            ->select('ids')
            ->where(['type' => $type])
            ->andwhere(['status' => Self::ACTIVE])
            ->andFilterWhere(['ip' => $ip])
            ->column();
        if ($id) return $id;
        else return [];
    }

    public static function getBusySources($type, $ip = null)
    {
        if (in_array($type, self::PARSING_TYPES_OF_CONTROL)) {
            $ids_row = ArrayHelper::getColumn(
                ControlParsing::find()
                    ->where(['in', 'type', self::PARSING_TYPES_OF_CONTROL])
                    ->andwhere(['status' => Self::ACTIVE])
                    ->andwhere(['IS NOT', 'ids_sources', null])
                    ->andwhere(['<>', 'ids_sources', ''])
                    ->andFilterWhere(['ip' => $ip])
                    ->all(),
                'ids_sources');
            $ids = [];
            if ($ids_row) {
                foreach ($ids_row as $row) {
                    if (is_array($row)) $ids = array_merge($ids, $row);
                    else array_push($ids, $row);
                }
            }
            // info('busy id_sources');
            // info(my_implode(array_unique($ids)));
            return $ids;

        } else return [];
    }

    public static function create($type, $sales = 0, $ip = '')
    {
        $parsingController = new ControlParsing();
        $module = \Yii::$app->params['module'];
        $server = \Yii::$app->params['server'];
        //$busies = ArrayHelper::toArray($sales);
        // my_var_dump($busies);
        if ($sales) {
            if ($sales instanceof ParsingConfiguration) {
                $busy_id_sources = $sales['id_sources'];
                $parsingController->ids_sources = $busy_id_sources;
                $parsingController->ids = $sales->id;
                info("busy_id_sources = " . $busy_id_sources);
            } else {
                //  my_var_dump($sales);
                $busy_id_sources = array_unique(ArrayHelper::getColumn($sales, 'id_sources'));
                if (!empty($busy_id_sources)) $parsingController->ids_sources = implode(",", $busy_id_sources);
                info("busy_id_sources = " . implode(',', $busy_id_sources));
            }
            $busy_ids = array_diff(ArrayHelper::getColumn($sales, 'id'), array(0, null, ''));
            // my_var_dump(array_unique(ArrayHelper::getColumn($busies,'id_sources')));
            info("busy_ids '" . implode(",", $busy_ids) . "'");

        }


        $parsingController->date_start = time();
        $parsingController->server = $server;
        $parsingController->type = $type;
        $parsingController->module = $module->region;
        if ($ip) $parsingController->ip = $ip;
        // ставим активный статус
        $parsingController->status = Self::ACTIVE;
        if (!empty($busy_ids)) $parsingController->ids = implode(",", $busy_ids);

//        if ((!empty($params['busy_ids'])) or (empty($params['busy_id_sources']))) {
//            $parsingController->params = serialize($params);
//        } else $parsingController->params = '';
        if (!$parsingController->save()) my_var_dump($parsingController->getErrors());
        \Yii::$app->params['session_id'] = $parsingController->id;

        return $parsingController->id;


    }

    public static function updating($id, $status = 2, $error = '')
    {
        $parsingController = ControlParsing::findOne($id);
        if ($parsingController) {
            $parsingController->date_finish = time();
            $parsingController->status = $status;
            $parsingController->params = $error;
            if (!$parsingController->save()) my_var_dump($parsingController->errors);
            info("updatingTime&ClOSING",SUCCESS);
        }


    }

    public static function updatingTime($id,$options = [])
    {
        $parsingController = ControlParsing::findOne($id);
        if ($parsingController) {

            if (($options['id_session']) and ($options['id_session'] != $parsingController->id_session)) {
                info(" UPDATE ID_SESSION",SUCCESS);
                $parsingController->id_session = $options['id_session'];
            }
            if (($options['ip']) and ($options['ip'] != $parsingController->ip)) {
                info(" UPDATE IP",SUCCESS);
                $parsingController->ip = $options['ip'];
            }
            $parsingController->date_check = time();
            $parsingController->status = ControlParsing::ACTIVE;
           if (!$parsingController->save()) my_var_dump($parsingController->errors);
            info("updatingTime",SUCCESS);
        }


    }
}
