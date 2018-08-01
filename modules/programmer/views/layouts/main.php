<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use app\components\NavWidget;
use rmrevin\yii\ulogin\ULogin;


\app\assets\MdbAsset::register($this);
// \app\assets\MyAppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>
<body>
<div class="container">
    <?php $this->beginBody() ?>

</div>
<main>

    <div class="container">

        <?= $content ?>
    </div>

</main>
<?php

?>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<?php

?>
<?php
