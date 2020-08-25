<?php

namespace backend\controllers;

use Yii;
use app\models\Collection;
use backend\models\CollectionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\Helper;

/**
 * CollectionController implements the CRUD actions for Collection model.
 */
class CollectionController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //   'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Collection models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CollectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Collection model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Collection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Collection();
        $helper = new Helper();
        if (isset($_GET['categoryId']) && $_GET['categoryId']) {
            $vendor = $model->getVendors($_GET['categoryId']);
            echo json_encode($vendor);
            exit;
        }

        if (isset($_GET['productId']) && $_GET['productId']) {
            $product = $model->getProductDetail($_GET['productId']);
            echo json_encode($product);
            exit;
        }
        if (isset($_GET['term'])) {
            $category = isset($_GET['productCategory']) ? $_GET['productCategory'] : '';
            $vendor = isset($_GET['productVendor']) ? $_GET['productVendor'] : '';
            $products = $model->getProducts($_GET['term'], $category, $vendor);
            ?>
            <ul class="ui-menu ui-widget" style="border: 1px solid #dddddd;max-height:150px; overflow-y:auto;">
                <?php foreach ($products as $product) {
                    ?> 
                    <li id="<?php echo $product['id']; ?>" onclick="selectProduct('<?php echo $product['name']; ?>', '<?php echo $product['id']; ?>')" class="ui-menu-item"><?php echo $product['name']; ?></li>
                <?php }
                ?>
            </ul>
            <?php
            exit;
        }

        $categoryArr = $model->getProductCategory();

        if (isset($_POST['Collection'])) {

            if ($collectionId = $model->saveCollection()) {
                if (isset($_FILES['Collection']['name']['image']) && $_FILES['Collection']['name']['image'] != '') {
                    $model->imageFiles = UploadedFile::getInstances($model, 'image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'image', $model->tableName(), $model->imageFiles, $collectionId);
                }
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
                    'model' => $model,
                    'categoryArr' => $categoryArr
        ]);
    }

    /**
     * Updates an existing Collection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = new Collection();
        $helper = new Helper();
        $collectionArr = $model->getCollection($id);
        if (isset($_GET['id']) && isset($_GET['imgName']) && $_GET['imgName'] != '') {

            $imageName = $collectionArr[$_GET['imgName']];
            $model->delImage($_GET['id'], $_GET['imgName']);
            if (file_exists(Yii::$app->basePath . '/web/images/collection/' . $imageName)) {
                unlink(Yii::$app->basePath . '/web/images/collection/' . $imageName);
            }
            Yii::$app->session->setFlash('success', 'Collection image deleted successfully.');
            echo "done";
            exit;
        }
        if (isset($_GET['categoryId']) && $_GET['categoryId']) {
            $vendor = $model->getVendors($_GET['categoryId']);
            echo json_encode($vendor);
            exit;
        }
        if (isset($_GET['productId']) && $_GET['productId']) {
            $product = $model->getProductDetail($_GET['productId']);
            echo json_encode($product);
            exit;
        }
        if (isset($_GET['term'])) {
            $category = isset($_GET['productCategory']) ? $_GET['productCategory'] : '';
            $vendor = isset($_GET['productVendor']) ? $_GET['productVendor'] : '';
            $products = $model->getProducts($_GET['term'], $category, $vendor);
            ?>
            <ul class="ui-menu ui-widget" style="border: 1px solid #dddddd;max-height:150px; overflow-y:auto;">
                <?php foreach ($products as $product) {
                    ?> 
                    <li id="<?php echo $product['id']; ?>" onclick="selectProduct('<?php echo $product['name']; ?>', '<?php echo $product['id']; ?>')" class="ui-menu-item"><?php echo $product['name']; ?></li>
                    <?php }
                    ?>
            </ul>
            <?php
            exit;
        }

        $categoryArr = $model->getProductCategory();

        if (isset($_POST['Collection'])) {

            if ($model->editCollection($id)) {
                if (isset($_FILES['Collection']['name']['image']) && $_FILES['Collection']['name']['image'] != '') {
                    $model->imageFiles = UploadedFile::getInstances($model, 'image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'image', $model->tableName(), $model->imageFiles, $id);
                }
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
                    'model' => $model,
                    'collectionArr' => $collectionArr,
                    'categoryArr' => $categoryArr
        ]);
    }

    /**
     * Deletes an existing Collection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = new Collection();
        $model->deleteCollection($id);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Collection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Collection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Collection::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
