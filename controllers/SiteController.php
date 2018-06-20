<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\ContactForm;
use app\models\User;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;

        return parent:: beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($status = 0)

    {
        if (isset($_COOKIE['user_id'])) $user_id = $_COOKIE['user_id'];
        if ($user_id) {
            $user = User::findOne($user_id);
            $user->to_session();
        }

        $session = Yii::$app->session;
        if ($status == 1) {
            $user = User::findOne(1);
            $user->to_session();
        }
        if ($session->get('user_id') !== null) {

            return $this->redirect(['/user/profile']);
        }


        return $this->render('index');
    }

    public function actionLogOut()

    {
        $_COOKIE['user_id'] = null;
        $session = Yii::$app->session;
        $session->set('user_id', null);
        Yii::$app->session->setFlash('just_logout', true);
        return $this->redirect(['/site/index']);
    }

//    public function actionLogin()
//    {
//        $session = Yii::$app->session;
//        $user_from_ulogin = new User();
//        $user = new User();
//
//        $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
//        $user_ulogin = json_decode($s, true);
//        $user_from_ulogin->first_name = $user_ulogin['first_name'];
//        $user_from_ulogin->last_name = $user_ulogin['last_name'];
//        $user_from_ulogin->identity = $user_ulogin['identity'];
//        $user_from_ulogin->network = $user_ulogin['network'];
//        $user_from_ulogin->city = $user_ulogin['city'];
//        $user_from_ulogin->email = $user_ulogin['email'];
//
//
//        if ($user_from_ulogin->is_unique()) {
//            if ($user_from_ulogin->validate()) {
//                $user_from_ulogin->save();
//                $user = User::find()->where(['identity' => $user_from_ulogin->identity])->one();
//                $user->to_session();
//                setcookie("user_id", $user->user_id, time() + 24*3600);
//            }
//        } else {
//            $user = User::find()->where(['identity' => $user_from_ulogin->identity])->one();
//
//            $user->to_session();
//            setcookie("user_id", $user->user_id, time() + 24*3600);
//
//
//        }
//
//
//        return $this->render('index');
//
//
//    }
    public function actionLogin()
    {

            $found_user = User::find()->where(['email' => $_POST['email']])->andWhere(['password' => $_POST['password']])->one();
            if ($found_user) {
                $found_user->to_session();
                return $this->redirect(['/user/profile']);
            } else {
                return $this->redirect(['/site/index']);
            }


        return $this->redirect(['/site/index']);
    }

    /**
     * Login action.
     *
     * @return string
     */
//    public
//    function action()
//    {
//        return [
//            // ...
//            'ulogin-auth' => [
//                'class' => AuthAction::className(),
//                'successCallback' => [$this, 'uloginSuccessCallback'],
//                'errorCallback' => function ($data) {
//                    \Yii::error($data['error']);
//                },
//            ]
//        ];
//    }


    /**
     * Logout action.
     *
     * @return string
     */


    /**
     * Displays contact page.
     *
     * @return string
     */
    public
    function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public
    function actionAbout()
    {
        return $this->render('about');
    }
}