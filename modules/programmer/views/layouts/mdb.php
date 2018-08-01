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
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>
<body>
<div class="container">
<?php $this->beginBody() ?>
<? if ((Yii::$app->controller->id !='console') and ((Yii::$app->controller->id !='parsing') )) {
    echo \app\components\MDSideNav::widget();

} ?>
</div>
<main>
<?php
$session = Yii::$app->session;
$id = $session->get('user_id');
if ($id == 0) {
    echo ULogin::widget([
// widget look'n'feel
        'display' => ULogin::D_PANEL,

// required fields
        'fields' => [ULogin::F_FIRST_NAME, ULogin::F_LAST_NAME, ULogin::F_EMAIL, ULogin::F_PHONE, ULogin::F_CITY, ULogin::F_COUNTRY, ULogin::F_PHOTO_BIG],

// optional fields
        'optional' => [ULogin::F_BDATE],

// login providers
        'providers' => [ULogin::P_VKONTAKTE, ULogin::P_FACEBOOK, ULogin::P_GOOGLE, ULogin::P_ODNOKLASSNIKI],

// login providers that are shown when user clicks on additonal providers button
        'hidden' => [],

// where to should ULogin redirect users after successful login
        'redirectUri' => ['site/login'],

// optional params (can be ommited)
// force widget language (autodetect by default)
        'language' => ULogin::L_RU,

// providers sorting ('relevant' by default)
        'sortProviders' => ULogin::S_RELEVANT,

// verify users' email (disabled by default)
        'verifyEmail' => '0',

// mobile buttons style (enabled by default)
        'mobileButtons' => '1',
    ]);
}
?>

<div class="container">

    <?= $content ?>
</div>

</main>



<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

