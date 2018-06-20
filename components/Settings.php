<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 11.06.2018
 * Time: 20:14
 */

namespace app\components;


use app\models\AgentPro;
use app\models\ParsingConfiguration;
use yii\base\Component;

class Settings extends Component

{
    public $id_sources = "1,2,3,4,5";
    public $period_check = ParsingConfiguration::PERIOD_OF_CHECK;
    public $period_check_new = ParsingConfiguration::PERIOD_OF_CHECK_NEW;
    public $pages_limit = ParsingConfiguration::PAGES_LIMIT;

    public function load()
    {
        /* @var $agentpro AgentPro */
        $agentpro = \Yii::$app->params['agent_pro'];
        $this->period_check = $agentpro->period_of_check;
        $this->period_check_new = $agentpro->period_of_check_new;
    }


}