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

    

    <?php
    $session = Yii::$app->session;
    $id = $session->get('user_id');

    if (($id == 0) and (!Yii::$app->session->getFlash('just_logout'))) { ?>
        <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-notify modal-success" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="Modal">autorization</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body center-block">
                     <form action="<?= Url::toRoute(['site/login']); ?>" method="post">
                         <div class="md-form form-group">
                             <i class="fa fa-envelope prefix"></i>
                             <input type="email" id="form91" name="email" class="form-control validate">

                         </div>
<br>
                         <div class="md-form form-group">
                             <i class="fa fa-lock prefix"></i>
                             <input type="password" id="form92" name="password" class="form-control validate">

                         </div>
                         <?= Html::submitButton('Submit', ['class' => 'btn btn-success']); ?>
                     </form>


                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
        <?

        $this->registerJs("$('#Modal').modal('show');", \yii\web\View::POS_READY);


    }
    ?>


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
