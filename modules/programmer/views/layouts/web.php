<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\helpers\Url;

use app\components\NavWidget;
use rmrevin\yii\ulogin\ULogin;


\app\assets\MdbAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
   <?php  $this->registerLinkTag(['rel' => 'icon', 'href' => 'icon.png']); ?>
</head>
<body class="indigo-skin">

<?php $this->beginBody() ?>
<div class="container">
    <?php echo \app\components\MDBNavbar::widget(); ?>
</div>


<main>
    <div class="container">
        <?= $content ?>
    </div>
</main>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
