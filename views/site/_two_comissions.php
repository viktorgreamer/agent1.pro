<?php
?>

<div class="my-5 container">

    <!-- Section heading -->
    <h2 class="h1-responsive font-weight-bold text-center my-5">Как легко зарабатывать двойную комиссию?</h2>
    <!-- Section description -->
    <p class="text-center h3-responsive w-responsive mx-auto mb-5">
        Вместо тысячи слов - пару минут видео... </p>
    <!-- Grid row -->

    <section class="mb-r">

        <div class="embed-responsive embed-responsive-16by9">
            <iframe class="embed-responsive-item"
                    src="https://www.youtube.com/embed/IQFDjWYEI3g?enablejsapi=1&amp;origin=https%3A%2F%2Fmdbootstrap.com"
                    allowfullscreen=""></iframe>
        </div>

    </section>
    <div class="container text-center">
        <?php echo \yii\helpers\Html::a('Узнать больше', Yii::$app->getHomeUrl(), ['class' => 'btn btn-success btn-rounded']); ?>
    </div>

</div>

<?= $this->render('_intro'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_stats'); ?>
<hr class="between-sections mb-5 mt-5 pb-3">
<?= $this->render('_footer'); ?>
