<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\ControlParsing;
use app\models\Renders;
use app\models\Actions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ControlParsingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Control Parsings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="control-parsing-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= info(" PARSING_SYNC " .\app\models\Methods::convertToString(ControlParsing::getBusySources('PARSING_SYNC'))); ?>
    <?= info(" PARSING_SYNC CONFIGS " .\app\models\Methods::convertToString(ControlParsing::getBusyIdConfigs('PARSING_SYNC'))); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            'module',
            'server',
            'date_start:time',
            'date_check:time',
            [
                'label' => 'type',
                'format' => 'raw',
                'value' => function ($model) {
             return \app\models\Control::mapTypesControls()[$model->type];
    }
            ],
            [
                'label' => 'duration',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->date_finish) return span($model->date_finish - $model->date_start, ControlParsing::STATUS_COLORS[$model->status]);
                    else return span(time() - $model->date_start, ControlParsing::STATUS_COLORS[$model->status]);
                }
            ],
            [
                'label' => 'Статус',
                'format' => 'raw',
                'value' => function ($model) {
                    return span(ControlParsing::STATUS[$model->status], ControlParsing::STATUS_COLORS[$model->status]);
                }
            ],
            [
                'label' => 'ids',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->ids) {
                        $body = Renders::IMPLODE($model->ids, 10);;
                        if (in_array($model->type, ['PARSING_SYNC', 'PARSING_NEW'])) {
                            $parsingconfiguration = \app\models\ParsingConfiguration::findOne($model->ids);
                            return Html::a(span($parsingconfiguration->name, ControlParsing::STATUS_COLORS[$model->status]), \yii\helpers\Url::toRoute(['parsing-configuration/view', 'id' => $model->ids]), ['target' => '_blank']);
                        }
                        if (strlen($body) > 40) return Renders::toModal(count(explode(",", $model->ids)), span($body, ControlParsing::STATUS_COLORS[$model->status]));
                        else return span($body, ControlParsing::STATUS_COLORS[$model->status]);
                    }
                }
            ],
            [
                'label' => 'sources',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->ids_sources) {
                        $body = '';
                        foreach (explode(",", $model->ids_sources) as $id) {
                            $body .= " " . Renders::renderSources($id);
                        }
                        return $body;
                    }

                }
            ],
            [
                'label' => 'response',
                'format' => 'raw',
                'value' => function ($model) {
                    $params = unserialize($model->params);
                    if (is_array($params)) {
                        $body = '<table>';
                        foreach ($params as $key => $param) {
                            $body .= "<tr style='padding-top: 0px;padding-bottom: 0px;'><td style='padding-top: 0px;padding-bottom: 0px;'>" . $key . "</td> <td style='padding-top: 0px;padding-bottom: 0px;'>" . $param . "</td></tr>";
                        }
                        $body .= '</table>';
                        return $body;
                    }

                }
            ],

            [
                'class' => \yii\grid\ActionColumn::className(),
                'buttons' => [

                    'delete' => function ($url, $model, $key) {
                        return Actions::renderAction($model->id, Actions::PARSING_CONTROL, Actions::DELETE, Actions::DELETE_ICON);
                    },

                ],

            ],
            'ip',
            'id_session'

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
