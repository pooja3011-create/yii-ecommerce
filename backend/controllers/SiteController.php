<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\ForgotPassword;
use backend\models\ResetPasswordForm;
use common\models\Helper;
use common\models\User;
use app\models\UserRoles;

/**
 * Site controller
 */
class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && !isset($rolePermitions['dashboard'])) {
            return $this->redirect(['unauthorized-access']);
        }
        return $this->render('index');
    }

    public function actionDashboard() {


        $this->layout = 'dashboard';
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {
        $this->layout = 'login';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            $userArr = User::find()
                    ->where(['email' => $_POST['LoginForm']['email']])
                    ->one();

            if (!empty($userArr)) {
                if ($userArr->status == '0') {
                    Yii::$app->session->setFlash('error', 'Your account is inactive. Please contact administrator.');
                    return $this->render('login', [
                                'model' => $model,
                    ]);
                }
                $count = UserRoles::find()
                        ->where(['id'=>$userArr->user_role])
                        ->andWhere(['status'=>'1'])
                        ->count();
                if($count <= 0){
                    Yii::$app->session->setFlash('error', 'There was some problem with your account permissions, please contact administrator.');
                    return $this->render('login', [
                                'model' => $model,
                    ]);
                }
                
            }

            if ($model->login()) {
                $user = User::find()->where(['id' => Yii::$app->user->id])->One();
                if (!Yii::$app->session->isActive) {
                    Yii::$app->session->open();
                }
                Yii::$app->session->set('userArr', $user);

                $rolePermitions = Helper::getRolePermission($user->user_role);

                $models = array_keys($rolePermitions);

                if (Yii::$app->user->id == '1' || in_array('dashboard', $models) && in_array('view', $rolePermitions['dashboard'])) {
                    return $this->goBack();
                } else {
                    $i = 0;
                    foreach ($rolePermitions as $key => $val) {
                        if (in_array('list', $val)) {
                            $action = $key;
                            if ($key == 'consumers') {
                                $action = 'user';
                            }
                            if ($key == 'admin_users') {
                                $action = 'configuration';
                            }
                            if ($key == 'vendor_payments') {
                                $action = 'vendors/payments';
                            }
                            return $this->redirect(['/' . str_replace('_', '-', $action)]);
                        }
                    }
                    Yii::$app->user->logout();
                    return $this->redirect([ 'login']);
                }
            } else {
                return $this->render('login', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Display forgot password page.
     *
     * @return mixed
     */
    public function actionForgotPassword() {
        $this->layout = 'login';
        $model = new ForgotPassword();

        if (isset($_POST['email1']) && $_POST['email1'] != '') {
            $model->requestResetPassword($_POST['email1']);
        }
        return $this->render('forgotPassword');
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        $this->layout = 'login';

        $userCount = (new \yii\db\Query())
                ->from('user')
                ->where(['password_reset_token' => $token])
                ->count();
        if ($userCount <= 0) {
            Yii::$app->session->setFlash('error', 'Your password reset link is expired.');
            return $this->redirect(['login']);
        }
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if (isset($_POST['newPassword']) && $_POST['newPassword'] != '') {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'Your Password reset successfully. Please login with new password.');
                return $this->redirect(['login']);
            }
        }

        return $this->render('resetPassword', [
                    'model' => $model,
        ]);
    }

    function actionUnauthorizedAccess() {
        return $this->render('unauthorized-access');
    }

}
