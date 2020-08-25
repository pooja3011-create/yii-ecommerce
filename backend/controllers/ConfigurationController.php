<?php

namespace backend\controllers;

use Yii;
use app\models\Configuration;
use backend\models\ConfigurationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\UserRoles;
use backend\models\UserRolesSearch;
use app\models\User;
use backend\models\UserSearch;
use common\models\Helper;

/**
 * ConfigurationController implements the CRUD actions for Configuration model.
 */
class ConfigurationController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
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
     * Lists all Configuration models.
     * @return mixed
     */
    public function actionIndex() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && ((!isset($rolePermitions['configuration']) || !in_array('view', $rolePermitions['configuration'])) && (!isset($rolePermitions['admin_users']) || !in_array('list', $rolePermitions['admin_users'])))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Configuration();

        if (isset($_POST['configuration'])) {
            $postData = Yii::$app->request->post();
            $model->saveConfig($postData['configuration']);
            return $this->redirect(['index']);
        }
        $modelArr = Configuration::find()->all();

        $roleSearchModel = new UserRolesSearch();
        $roleDataProvider = $roleSearchModel->search(Yii::$app->request->queryParams);

        $userSearchModel = new UserSearch();
        $userDataProvider = $userSearchModel->search(Yii::$app->request->queryParams);

        $fromUsers = Yii::$app->getRequest()->getQueryParam('fromUsers', "0");
        $fromRoles = Yii::$app->getRequest()->getQueryParam('fromRoles', "0");
        return $this->render('index', [
                    'model' => $modelArr,
                    'fromUsers' => $fromUsers,
                    'fromRoles' => $fromRoles,
                    'roleDataProvider' => $roleDataProvider,
                    'roleSearchModel' => $roleSearchModel,
                    'userDataProvider' => $userDataProvider,
                    'userSearchModel' => $userSearchModel,
        ]);
    }

    /**
     * Creates a new User Role.
     * @return mixed
     */
    public function actionUserRole() {
        $model = new Configuration();
        $modules = $model->getModels();
        $modelArr = array();
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $modelArr = $model->getUserRole($_GET['id']);
        }
        if (isset($_POST['UserRolesSearch'])) {
            if ($id = $model->saveUserRole()) {
                return $this->redirect(['index', 'fromRoles' => 1]);
            }
        }
        return $this->render('user-role', [
                    'model' => $modelArr,
                    'modules' => $modules
        ]);
    }

    /**
     * Creates a new Admin user.
     * @return mixed
     */
    public function actionAdminUser($id = '') {
        $rolePermitions = Helper::getRolePermission();
        if ($id == '' && Yii::$app->user->id != '1' && (!isset($rolePermitions['admin_users']) || !in_array('add', $rolePermitions['admin_users']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        if ($id != '' && Yii::$app->user->id != '1' && (!isset($rolePermitions['admin_users']) || !in_array('view', $rolePermitions['admin_users']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Configuration();
        $userRole = $model->userRole();
        $modelArr = array();
        if ($id != '') {
            $modelArr = $model->getUser($id);
        }
        if (isset($_POST['User'])) {
            if ($id != '') {
                if ($model->UpdateAdminUser($id, $_POST['User'])) {
                    return $this->redirect(['index', 'fromUsers' => 1]);
                }
            } else {
                if ($model->saveAdminUser($_POST['User'])) {
                    return $this->redirect(['index', 'fromUsers' => 1]);
                }
            }
        }
        return $this->render('admin-user', [
                    'userRole' => $userRole,
                    'model' => $modelArr
        ]);
    }

    /**
     * Deletes an existing admin role.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteRole($id) {
        $model = new Configuration();
        if ($model->deleteRole($id)) {
            return $this->redirect(['index', 'fromRoles' => 1]);
        } else {
            return $this->redirect(['user-role', 'id' => $id]);
        }
    }

    /**
     * Deletes an existing admin user.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteUser($id) {
        $model = new Configuration();
        $model->deleteUser($id);
        return $this->redirect(['index', 'fromUsers' => 1]);
    }

    /**
     * Displays a single Configuration model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Configuration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Configuration();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Configuration model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Configuration model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Configuration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Configuration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Configuration::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
