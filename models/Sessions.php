<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sessions".
 *
 * @property int $id
 * @property string $id_session
 * @property int $status
 * @property int $datetime_start
 * @property int $datetime_check
 * @property string $cookies
 */
class Sessions extends \yii\db\ActiveRecord
{

    const ACTIVE = 1;
    const FREE = 2;
    const LOST = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sessions';
    }

    public static function getColors($status = 0)
    {
        $colors = [
            0 => 'table-black',
            1 => 'table-success',
            2 => 'table-warning',
            3 => 'table-danger'];
        return $colors[$status];

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_session', 'status', 'datetime_check'], 'required'],
            [['status', 'datetime_start', 'datetime_check'], 'integer'],
            [['id_session'], 'string', 'max' => 256],
        ];
    }

    public static function create($id_session, $ip='')
    {
        $session = new Sessions();
        info("СОЗДАЕМ СЕССИЮ");
        $session->status = 1;
        $session->id_session = $id_session;
        $session->datetime_check = time();
        $session->datetime_start = time();
        if (!$ip) info(" CREATING SESSION WITHOUT IP ",DANGER);
        else info(" CREATING SESSION WITH IP ".$ip,SUCCESS);
        $session->ip = $ip;
        $session->id_server = Yii::$app->params['server'];
        if (!$session->save()) my_var_dump($session->getErrors());
    }


    public static function updateSession($id_session, $url = '')
    {
        $session = Sessions::find()
            ->where(['id_session' => $id_session])
            ->andFilterWhere(['id_server' => Yii::$app->params['server']])
            ->one();

        if ($session) {
            //   info("НАШЛИ СЕССИЮ");
            $session->datetime_check = time();
            $session->current_url = $url;
            $session->status = 1;
            $session->save();
        }
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_session' => 'Id Session',
            'status' => 'Status',
            'datetime_start' => 'Datetime Start',
            'datetime_check' => 'Datetime Check',
            'cookies' => 'Cookies',
        ];
    }


    public function isExists($id_session)
    {
        return Sessions::find()->where(['id_session' => $id_session])->exists();

    }

    public static function check() {
        $free_sessions = Sessions::find()->Where(['<', 'datetime_check', (time() - \app\models\ChromeDriver\MyChromeDriver::SESSION_LOST_TIMEOUT)])->orderBy('datetime_check')->all();
        foreach ($free_sessions as $key => $web_session) {
                $web_session->status = Sessions::FREE;
                $web_session->save();
            // info(" SESSION " . $web_session->id_session . " IS FREE FROM " . \Yii::$app->formatter->asRelativeTime($web_session->datetime_check), 'success');
        }
    }


}
