<?php
?>

<!-- Material form register -->
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-12 col-sm-12">
        <div class="card">

            <h5 class="card-header info-color white-text text-center py-4">
                <strong>Регистрация</strong>
            </h5>

            <!--Card content-->
            <div class="card-body px-lg-5 pt-0">

                <!-- Form -->
                <form class="text-center" style="color: #757575;" method="post"
                      action="<?= \yii\helpers\Url::to('/user/registration'); ?>">

                    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                    <br>
                    <div class="form-row">
                        <div class="col">
                            <!-- First name -->
                            <div class="md-form">
                                <?php echo \app\components\Mdb::ActiveTextInput($user, 'first_name', ['id' => 'FormFirstName']); ?>
                            </div>
                        </div>
                        <div class="col">
                            <!-- Last name -->
                            <div class="md-form">
                                <?php echo \app\components\Mdb::ActiveTextInput($user, 'last_name', ['id' => 'FormLasttName']); ?>

                            </div>
                        </div>
                    </div>

                    <!-- E-mail -->
                    <div class="md-form mt-0">
                        <?php echo \yii\helpers\Html::activeInput('email', $user, 'email', ['id' => 'materialRegisterFormEmail',
                            'class' => "form-control", 'label' => '']); ?>
                        <label for="materialRegisterFormEmail">E-mail</label>
                    </div>
                    <!-- Password -->

                    <div class="md-form">
                        <?php echo \yii\helpers\Html::activeInput('password', $user, 'password', ['id' => 'materialRegisterFormPassword',
                            'class' => "form-control", 'label' => '', 'aria-describedby' => "materialRegisterFormPasswordHelpBlock"]); ?>
                        <label for="materialRegisterFormPassword">Пароль</label>
                        <small id="materialRegisterFormPasswordHelpBlock" class="form-text text-muted mb-4">
                           Буквы или цифры
                        </small>
                    </div>

                    <!-- Phone number -->
                    <div class="md-form">
                        <?php echo \yii\helpers\Html::activeTextarea($user, 'phone', [
                                'id' => 'materialRegisterFormPhone',
                            'class' => "form-control",
                            'label' => '',
                            'aria-describedby' => "materialRegisterFormPhoneHelpBlock",
                            'rows' => 5]);  ?>
                        <label for="materialRegisterFormPhone">Номера телефонов</label>
                        <small id="materialRegisterFormPhoneHelpBlock" class="form-text text-muted mb-4">
                            Укажите номера всех ваших телефонов через запятую. <br>(88162565656,89116755564)
                        </small>
                    </div>

                    <?/* // echo \himiklab\yii2\recaptcha\ReCaptcha::widget([
                        'name' => 'reCaptcha',
                        'siteKey' => 'your siteKey',
                       // 'widgetOptions' => ['class' => 'col-sm-offset-3']
                    ]) */?>

                    <!-- Sign up button -->
                    <button class="btn btn-outline-info btn-rounded btn-block my-4 waves-effect z-depth-0"
                            type="submit">Зарегистрироваться
                    </button>

                    <hr>

                    <!-- Terms of service -->
                    <p> Регистрируясь вы подтверждаете <a href="" data-toggle="modal" data-target="#useragreementModal" > согласие на  обработку персональных данных </a>
                         и <a href=""  data-toggle="modal" data-target="#userrulesModal" >согласие с правилами пользования сервисом</a>. </p>

                </form>
                <!-- Form -->

            </div>

        </div>
    </div>
</div>

<?= $this->render('_user_agreement'); ?>
<?= $this->render('_user_rules'); ?>



