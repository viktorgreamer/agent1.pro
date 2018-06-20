<?php
use yii\db\Expression;
?>

<div class="row">
    <?php $Query_advices = \app\models\Advices::find()->where(['<>','title', '']);
    if ($type) $Query_advices->andFilterWhere(['type' => $type]);
    if ($not_q) $Query_advices->andFilterWhere(['<>','q', $not_q]);
    $advices = $Query_advices->orderBy(new Expression('rand()'))->limit($count)->all()?>
    <?php foreach ($advices as $advice) {
        if ($advice->type == 1) $color = 'success'; else $color = 'info';
        if ($advice->type == 1) $header_title = "Совет"; else $header_title = 'Бесплатная консультация';
        ?>
        <div class="col-sm-12">
            <div class="card text-center">
                <div class="card-header <?= $color; ?>-color white-text" style="padding-top: 5px;padding-bottom: 4px;">
                    <h4><i class="fa fa-check" aria-hidden="true"></i><?= $header_title ?></h4>
                </div>
                <div class="card-body">
                    <h4 class="card-title"><?= $advice->title; ?></h4>
                    <a class="btn btn-<?= $color; ?> btn-sm"
                       HREF="<?= \yii\helpers\Url::to("advices?q=" . $advice['q']); ?>">Получить</a>
                </div>
            </div>
            <br>
        </div>

    <?php } ?>
</div>

