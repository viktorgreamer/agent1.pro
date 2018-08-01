<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "agent_pro".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $id_error
 * @property integer $time
 * @property integer $status_parsingsync
 * @property integer $status_detailed_parsing
 * @property integer $status_processing
 * @property integer $status_parsing_avito_phones
 * @property integer $status_analizing
 * @property integer $status_parsing_new
 * @property integer $status_sync
 * @property integer $status_geocogetion
 */
class AgentPro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent_pro';
    }


    const DISACTIVE = 0;
    const ACTIVE = 1;
    const  DEBUG_MODE = 1;


    const ERROR_CANNOT_CALCULATE_TOTAL_COUNT_DIV_CLASS_CIAN = 51;
    const ERROR_CANNOT_CALCULATE_PAGINATION_LIS_DIV_CLASS_CIAN = 52;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_STARRED_CLASS_CIAN = 53;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_URL_CLASS_CIAN = 54;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_TIME_CLASS_CIAN = 55;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_CLASS_CIAN = 56;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_TITLE_CLASS_CIAN = 57;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_PRICE_CLASS_CIAN = 58;
    const ERROR_CANNOT_CALCULATE_TABLE_DIV_ADDRESS_CLASS_CIAN = 59;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_TITLE_CLASS_CIAN = 60;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_CLASS_CIAN = 61;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN = 62;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_PHOTORAMA_CLASS_CIAN = 63;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_LINK_COORDS_CLASS_CIAN = 64;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_PERSON_TITLE_CLASS_CIAN = 65;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_PHONE_CLASS_CIAN = 67;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_DESCRIPTION_CLASS_CIAN = 68;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK_CLASS_CIAN = 69;
    const ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK2_CLASS_CIAN = 70;

    public static function mapStatuses()
    {
        return [
            self::ACTIVE => "YES",
            self::DISACTIVE => "NO"
        ];
    }


    public static function ErrorLogs()
    {
        return [
            self::ERROR_CANNOT_CALCULATE_PAGINATION_LIS_DIV_CLASS_CIAN => 'CANNOT_CALCULATE_PAGINATION_LIS_DIV_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TOTAL_COUNT_DIV_CLASS_CIAN => 'CANNOT_CALCULATE_TOTAL_COUNT_DIV_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_STARRED_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_STARRED_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_URL_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_URL_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_TIME_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_TIME_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_TITLE_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_TITLE_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_PRICE_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_PRICE_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_TABLE_DIV_ADDRESS_CLASS_CIAN => 'CANNOT_CALCULATE_TABLE_DIV_ADDRESS_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_TITLE_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_TITLE_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_ADDRESS_ITEM_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_LINK_COORDS_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_LINK_COORDS_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_PERSON_TITLE_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_PERSON_TITLE_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_PHONE_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_PHONE_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK_CLASS_CIAN',
            self::ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO_BLOCK2_CLASS_CIAN => 'ERROR_CANNOT_CALCULATE_PAGE_DIV_INFO2_BLOCK2_CLASS_CIAN',
        ];

    }

    public static function stop($id_error = null)
    {

        $agentpro = self::find()->where(['status' => self::ACTIVE])->one();
        if ($agentpro) {
            $agentpro->status = self::DISACTIVE;
            Notifications::VKMessage(AgentPro::ErrorLogs()[$id_error]);
            if ($id_error) $agentpro->id_error = $id_error;
            $agentpro->time = time();
            $agentpro->save();

        }
    }

    public static function activate($id)
    {

        $agentpro = self::find()->where(['id' => $id])->one();
        $agentpro->time = time();
        $agentpro->status = self::ACTIVE;
        $agentpro->save();


    }

    public static function getActive()
    {
        $agentpro = self::find()->where(['status' => self::ACTIVE])->one();
        if (!$agentpro->status) {
            info("PROGRAMM IS STOPPED", DANGER);
            die();
        }
        info("PROGRAMM IS ACTIVE");
        \Yii::$app->params['agent_pro'] = $agentpro;

        $root = Yii::getAlias('@app') . "/..";
        $server_name_dir = array_shift(array_filter(scandir($root), function ($name) {
            return preg_match("/SERVER_NAME/", $name);
        }));

        $server_name = array_shift(array_filter(scandir($root . "/" . $server_name_dir), function ($name) {
            return preg_match("/SERVER_NAME/", $name);
        }));

        $server_name = preg_replace("/.SERVER_NAME/", "", $server_name);
        if (!$server_name) {
            $error = Errors::findOne(SERVER_MUST_BE_NAMED);
            AgentPro::throwError($error);
            info($error->name, DANGER);

        } else {
            info($server_name, SUCCESS);
            Yii::$app->params['server'] = $server_name;
        }

        return $agentpro;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'required'],
            [['id_sources'], 'string'],
            [['status', 'id_error', 'period_check', 'period_check_new', 'page_limit', 'time', 'status_parsingsync', 'status_detailed_parsing', 'status_processing', 'status_parsing_avito_phones', 'status_analizing', 'status_parsing_new', 'status_sync', 'status_geocogetion'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'id_error' => 'Error',
            'time' => 'Time',
            'status_parsingsync' => 'Parsingsync',
            'status_detailed_parsing' => 'Detailed Parsing',
            'status_processing' => 'Processing',
            'status_parsing_avito_phones' => 'Parsing Avito Phones',
            'status_analizing' => 'Analizing',
            'status_parsing_new' => 'Parsing New',
            'status_sync' => 'Status Sync',
            'status_geocogetion' => 'Geocogetion',
            'id_sources' => 'sources',
        ];
    }

    public static function throwError($error, $pageSource = '')
    {
        if ($pageSource) {
            // info($pageSource);
            $ip = \Yii::$app->params['ip'];
            $time = str2url(date("Y-m-d H:i:s"));
            $dir = Yii::getAlias('@app');
            self::FtpLog($dir . "/web/errors/" . str2url($ip) . "_" . $time . "_error_" . $error->name . ".html", $pageSource);
            // file_put_contents($dir . "/web/errors/" . $ip . "_" . $time . "_error_" . $error->name . ".html", $pageSource);

        }

        info($error->name, 'danger');
        if ($error->fatality == Errors::FATAL_ERROR) {
            info("STOP THE APPLICATION", DANGER);
            AgentPro::stop($error->id);

            if (!self::DEBUG_MODE) die();
        }

    }

    public static function FtpLog($fileName, $filebody)
    {
        $directory = '/./domains/mirs.pro/public_html/web/errors/';

        $ftp_server = '141.8.195.92';
        $ftp_user_name = 'a0086640';
        $ftp_user_pass = 'ucbueptuke';


        file_put_contents("ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $directory . "/" . $fileName, $filebody);


    }

    public static function logPageSource($pageSource)
    {
        if ($pageSource) {
            // info($pageSource);
            $ip = \Yii::$app->params['ip'];
            $time = str2url(date("Y-m-d H:i:s"));
            $dir = Yii::getAlias('@app');
            self::FtpLog($dir . "/web/errors/" . $ip . "_" . $time . ".html", $pageSource);
            // file_put_contents($dir . "/web/errors/" . $ip . "_" . $time . "_error_" . $error->name . ".html", $pageSource);

        }

    }


}
