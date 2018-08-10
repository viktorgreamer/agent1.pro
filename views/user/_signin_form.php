<?php
?>
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12 col-sm-12">
            <!-- Material form login -->
            <div class="card">

                <h5 class="card-header info-color white-text text-center py-4">
                    <strong>Авторизация</strong>
                </h5>
                <br>

                <!--Card content-->
                <div class="card-body px-lg-5 pt-0">

                    <!-- Form -->
                    <form class="text-center" style="color: #757575;" method="post"
                          action="<?= \yii\helpers\Url::to('/user/sign-in'); ?>">
                        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>
                        <?php if (Yii::$app->session->getFlash('JUST_REGESTERED'))  { ?>
                        <h1 class="text-success">Теперь вы можете войти.</h1>
                        <?php } ?>

                        <!-- Email -->
                        <div class="md-form mt-0">
                            <?php echo \yii\helpers\Html::activeInput('email', $model, 'email', ['id' => 'materialRegisterFormEmail',
                                'class' => "form-control", 'label' => '']); ?>
                            <label for="materialRegisterFormEmail">E-mail</label>
                        </div>
                        <!-- Password -->

                        <div class="md-form">
                            <?php echo \yii\helpers\Html::activeInput('password', $model, 'password', ['id' => 'materialRegisterFormPassword',
                                'class' => "form-control", 'label' => '', 'aria-describedby' => "materialRegisterFormPasswordHelpBlock"]); ?>
                            <label for="materialRegisterFormPassword">Пароль</label>

                        </div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <!-- Remember me -->
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="materialLoginFormRemember">
                                    <label class="form-check-label" for="materialLoginFormRemember">Remember me</label>
                                </div>
                            </div>
                            <div>
                                <!-- Forgot password -->

                            </div>
                        </div>

                        <!-- Sign in button -->
                        <button class="<?= CLASS_BUTTON; ?> my-4 waves-effect z-depth-0"
                                type="submit">Войти
                        </button>
                        <?= \yii\helpers\Html::button('Я забыл пароль!', ['class' => CLASS_BUTTON_DANGER, 'id' => 'idforgotpassword']); ?>


                        <!-- Register -->
                        <p>Еще не зарегиcтрированы ?
                            <a href="<?= \yii\helpers\Url::to(['/user/registration']); ?>">Регистрация</a>
                        </p>


                    </form>
                    <!-- Form -->

                </div>

            </div>
        </div>
    </div>

<?php
$script = <<< JS
// ручное добавление id_address
$('#idforgotpassword').on('click', function (e) {
e.preventDefault();
var email = $("input[name='email']").val();
var _csrf = $("input[name='_csrf']").val();


$.ajax({
url: '/user/forgot-password',
data: {email: email,_csrf:_csrf},
type: 'post',
success: function (res) {
    this.disabled = true;
// toastr.success(res);
},

error: function () {
alert('error')
}
});
this.disabled = true;
});

JS;

//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);


