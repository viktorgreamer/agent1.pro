<?php


use app\models\Actions;
use app\models\SaleSimilar;
use app\models\Geocodetion;
use app\models\Sale;

$sale = Sale::find()->where(['id_similar' => 1000])->orderBy(new \yii\db\Expression('rand()'))->one();
// echo $sale->id; ?>


        <?
        // ставим что SIMILAR MODERATED
        echo Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATED,
            Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED));
        // ставим что SIMILAR MODERATION_ONCALL
        echo Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATION_ONCALL,
            Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATION_ONCALL));
        ?>

