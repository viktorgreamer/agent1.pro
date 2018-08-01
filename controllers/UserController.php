<?php

namespace app\controllers;

use app\models\Notifications;
use app\models\Renders;
use app\models\UserModels\SignInForm;
use Yii;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller

{


    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionProfile()
    {
        $session = Yii::$app->session;
        $id = $session->get('user_id');

        return $this->render('profile', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionView($id = 0)
    {
        if ($id) {
            if (Yii::$app->user->can('admin')) {
                $model = $this->findModel($id);
            } else   $model = $this->findModel(Yii::$app->user->id);
        } else {
            $model = $this->findModel(Yii::$app->user->id);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {

        if ($_POST['email']) {
            if ($user = User::find()->where(['email' => $_POST['email']])->one()) {
                try {
                    Notifications::Mail(" Вы только-что запросили восстановление пароля с вашего аккаунта на mirs.pro. \r\n
             Пароль: " . $user->password, $user->email);
                    Renders::toAlert("Пароль отправлен вам на email");
                } catch (\Exception  $exception) {
                    if ($exception instanceof \Swift_TransportException) {
                        Notifications::Mail(" Вы только-что запросили восстановление пароля с вашего аккаунта на mirs.pro. \r\n
             Пароль: " . $user->password, $user->email);
                        Renders::toAlert("Пароль отправлен вам на email");
                    }
                }

            } else {
                Renders::toAlert(" Пользователь с таким email незарегистрирован в системе", DANGER);

            }

        }
    }


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {


        $model = $this->findModel($id);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionChange($id, $moderated_mode = false)
    {
        $session = Yii::$app->session;
        $session->set('user_id', $id);
        $_SESSION['moderated_mode'] = true;

        $user = User::find()->where(['id' => $id])->one();
        $user->to_session();


        return $this->render('profile', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionRegistration()
    {
        $model = New User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->setDefaultSettings();
            $model->setDefaultRoles();
            $model->save();

            return $this->redirect(['sign-in']);
        } else {
            return $this->render('_registration', [
                'user' => $model,
            ]);
        }
    }

    public function actionSignIn()
    {
        $model = new SignInForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {

                    return $this->goHome();
                }
            }
        }

        return $this->render('_signin_form', [
            'model' => $model,
        ]);

    }

    public function actionSignOut()
    {
        $user = Yii::$app->getUser();
        if ($user) {
            $user->logout();
            return $this->goHome();
        }
    }


    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
