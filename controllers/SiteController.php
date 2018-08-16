<?php

namespace app\controllers;


use app\models\Renders;
use Curl\Curl;
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



    public function actionCheckoxy(){


        if (($_POST['proxy'])&& ($_POST['url'])) {
            $curl = new Curl();
            $proxy = preg_split("/:/", $_POST['proxy']);
                $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
                $curl->setOpt(CURLOPT_HTTPPROXYTUNNEL, 1);
                $curl->setOpt(CURLOPT_PROXY, $proxy[0].":".$proxy[1]);
                $curl->setOpt(CURLOPT_PROXYUSERPWD, $proxy[2] . ":" . $proxy[3]);
            $headers = [
                'Upgrade-Insecure-Requests' => '1',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'DNT' => '1',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,es;q=0.6',
                'Pragma' => 'akamai-x-cache-on, akamai-x-cache-remote-on, akamai-x-check-cacheable, akamai-x-get-cache-key, akamai-x-get-extracted-values, akamai-x-get-ssl-client-session-id, akamai-x-get-true-cache-key, akamai-x-serial-no, akamai-x-get-request-id,akamai-x-get-nonces,akamai-x-get-client-ip,akamai-x-feo-trace'
            ];
            $curl->setHeaders($headers);
                $curl->get($_POST['url']);
            if ($curl->error) {
                $message =  'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            } else {
              //  echo 'Response:' . "\n";
                $response = gzdecode($curl->response);
                $message = "УСПЕШНО";
            }



        }

        return $this->render('proxy',compact('message','response'));

    }
    public function actionScreenSize() {
      if ($_POST['screen']) {
          Yii::$app->params['screen-width'] = $_POST['screen'];
      }
    }

    /**
     * @inheritdoc
     */

   /* public function beforeAction($action)
    {
        Renders::StaticView('layouts/_screensize');
        $this->enableCsrfValidation = false;

        return parent:: beforeAction($action);
    }*/



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
    public function actionIndex($show = '')

    {

        $this->layout = 'intro';
       /* if (isset($_COOKIE['user_id'])) $user_id = $_COOKIE['user_id'];
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
*/

        return $this->render('index',compact('show'));
    }
    public function actionTwoComissions()

    {

        $this->layout = 'intro';


        return $this->render('_two_comissions');
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
              //  return $this->redirect(['/site/index']);
                return $this->render('index');

            }


      //  return $this->redirect(['/site/index']);
            return $this->render('index');
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

    public
    function actionAdminCanView()
    {
        return $this->render('about');
    }
}