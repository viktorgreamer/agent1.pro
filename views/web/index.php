<?php
use yii\widgets\ActiveForm;
use app\models\SaleLists;

echo $this->render('_counters');
/* @var $this yii\web\View */
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
$body = 'Хотите узнать не пропустить выгодное предложение Великом Новгороде?';
?>

<div class="row">
    <div class="col-md-8 ">
        <h3> Компания «Недвижимость53» поможет вам произвести любые действия с любой недвижимостью, в том числе:
        </h3>

        <blockquote class="blockquote bq-success">
            <ol>
                <li> подбор объекта недвижимости в зависимости от пожеланий Клиента, оформление перехода права
                    собственности на выбранный объект недвижимости, юридическое сопровождение сделки;
                </li>
                <li> помощь в продаже вашего объекта и подбор вам альтернативного объекта, полное юридическое
                    сопровождение;
                </li>
                <li> оформление сделок с использованием ипотечных кредитов субсидий, жилищных сертификатов и т.д.;
                </li>
                <li> консультации по программам: «материнский капитал», «предоставление жилья военнослужащим»;
                </li>
                <li> содействие в получении ипотечных кредитов, субсидий, жилищных сертификатов и т.д.;
                </li>
                <li> помощь в приватизации, вступлении в наследство и любое другое оформление права собственности;
                </li>
                <li> отдел строящейся недвижимости подберет вам Объект, основываясь на ваших предпочтениях.
                </li>
            </ol>
        </blockquote>

    </div>


    <div class="col-md-4">
        <div class="sticky">
            <a href="#" type="button" class="badge badge-danger animated flash " style="font-size: larger" data-toggle="modal"
               data-target="#modalContactForm"> Оставить заявку на просмотр </a>
        </div>
        <?
        $JS = <<<JS
 $(function () {
            $(".sticky").sticky({
        topSpacing: 90
        , zIndex: 2
        , stopper: "#YourStopperId"
    });
});    
JS;
        $this->registerJs($JS, \yii\web\View::POS_READY);

        ?>

        <?php
        $salelists = SaleLists::find()->where(['type' => 2])->all();
        foreach ($salelists as $salelist1) {
            $salelist1 = \app\models\SaleLists::findOne($salelist1->id);
            ?>
            <div class="row">
                <div class="col-12">

                    <a href="<?= \yii\helpers\Url::to(['web/search-by', 'id' => $salelist1->id]); ?>">
                        <h5><span class="badge cyan animated pulse "><?php echo $salelist1->name; ?> </span></h5>
                    </a>
                    <h6><?php echo $salelist1->komment; ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-9">
                    от <?php echo $salelist1->getMinPrice(); ?>
                </div>
                <div class="col-3"><span
                            class="badge badge-pill pink"><?php echo $salelist1->getCount(); ?>
                        ШТ.</span>
                </div>

            </div>

            <?
        }
        ?>
        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4]); ?>
        </div>
        ?>


    </div>
</div>




