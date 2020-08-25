<?php

namespace backend\controllers;

use Yii;
use app\models\Slider;
use backend\models\SliderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\Helper;

/**
 * SliderController implements the CRUD actions for Slider model.
 */
class SliderController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Slider models.
     * @return mixed
     */
    public function actionIndex() {
        $model = new Slider();

        if (isset($_GET['listArr']) && $_GET['listArr'] != '') {
            $model->sliderSort($_GET['listArr']);
            exit;
        }
        $sliderArr = $model->sliderList();
        return $this->render('index', [
                    'sliderArr' => $sliderArr,
        ]);
    }

    /**
     * Displays a single Slider model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Slider model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Slider();
        $helper = new Helper();
        if (isset($_POST['Slider'])) {
            if ($id = $model->saveSlider()) {
                if (isset($_FILES['Slider']['name']['image']) && $_FILES['Slider']['name']['image'] != '') {
                    $model->imageFiles = UploadedFile::getInstances($model, 'image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'image', $model->tableName(), $model->imageFiles, $id);
                }
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Slider model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = new Slider();
        $sliderArr = $model->sliderDetail($id);
        $helper = new Helper();
        if (isset($_POST['Slider'])) {
            if ($model->editSlider($id)) {
                if (isset($_FILES['Slider']['name']['image']) && $_FILES['Slider']['name']['image'] != '') {
                    $model->imageFiles = UploadedFile::getInstances($model, 'image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'image', $model->tableName(), $model->imageFiles, $id);
                }
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'sliderArr' => $sliderArr
        ]);
    }

    /**
     * Deletes an existing Slider model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Slider model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Slider the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Slider::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
