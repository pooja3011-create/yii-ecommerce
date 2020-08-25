<?php

namespace backend\controllers;

use Yii;
use app\models\Products;
use backend\models\ProductsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;
use common\models\Helper;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller {

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
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['products']) || !in_array('list', $rolePermitions['products']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        if (isset($_GET['product_id']) && $_GET['product_id'] != '') {
            $ids = $_GET['product_id'];
            $status = isset($_GET['action']) ? $_GET['action'] : '';
            $reason = isset($_GET['reason']) ? $_GET['reason'] : '';
            $saveArr['status'] = $status;
            if ($reason != '') {
                $saveArr['disapprove_reason'] = $reason;
            }
            $model = new Products();
            Yii::$app->db->createCommand()->update('products', $saveArr, 'id in (' . $ids . ')')->execute();
            exit;
        }
        $searchModel = new ProductsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $countQuery = $searchModel->searchQuery(Yii::$app->request->queryParams);
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'pages' => $pages
        ]);
    }

    /**
     * Displays a single Products model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['products']) || !in_array('view', $rolePermitions['products']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $productModel = new Products();
        $helper = new Helper();
        $model = $this->findModel($id);

        if (isset($_POST['color_name']) && $_POST['color_name'] != '') {
            if (isset($_FILES['other_color_images']['name']) && $_FILES['other_color_images']['name'][0] != '') {
                if (count($_FILES["other_color_images"]["tmp_name"]) > 3) {
                    Yii::$app->session->setFlash('error', 'Sorry, you can upload only 3 images for othor image.');
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
            $productModel->editColors($id);

            return $this->redirect(['view', 'id' => $id]);
        }
        if (isset($_GET['size']) && $_GET['size'] != '') {
            $productModel->changeSize($id);
            exit;
        }

        if (isset($_GET['remove_color']) && $_GET['remove_color'] != '') {
            $model->removeColor($_GET['remove_color'], $id);
            exit;
        }
        if (isset($_GET['id']) && isset($_GET['imgName']) && $_GET['imgName'] != '' && ($model->featured_image != "" )) {
            $imageName = $model->$_GET['imgName'];
            $model->delImage($_GET['id'], $_GET['imgName']);
            if (file_exists(Yii::$app->basePath . '/web/images/products/' . $imageName)) {
                unlink(Yii::$app->basePath . '/web/images/products/' . $imageName);
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['productImgDelete']);
            echo "done";
            exit;
        }

        $productSize = $productModel->getProductSize($id);
        $categorySize = $productModel->getCategorySize($model->category_id);
        $attributesSize = $productModel->getCategoryFits($model->category_id);
        $productColorArr = $productModel->getProductColor($id);
        $productColor = $productColorArr['colors'];
        $colorImgs = $productColorArr['images'];
        $productVariation = $productModel->getProductVariations($id);
        $productCategory = $productModel->getProductCategory();
        $featuredImg = $model->featured_image;
        $vendorArr = $productModel->getVendorDetail($model->vendor_id);


        if (isset($_POST['product_code']) && $_POST['product_code'] != '') {
            if ($productModel->editProduct($id, $featuredImg)) {
                if (isset($_POST['save']) && $_POST['save'] == '1') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['view', 'id' => $id]);
                }
                return $this->redirect(['index']);
            } else {
                return $this->render('view', [
                            'model' => $model,
                            'vendorArr' => $vendorArr,
                            'productSize' => $productSize,
                            'categorySize' => $categorySize,
                            'attributesSize' => $attributesSize,
                            'productColor' => $productColor,
                            'productVariation' => $productVariation,
                            'productCategory' => $productCategory,
                            'colorImgs' => $colorImgs
                ]);
            }
        } else {
            return $this->render('view', [
                        'model' => $model,
                        'vendorArr' => $vendorArr,
                        'productSize' => $productSize,
                        'categorySize' => $categorySize,
                        'attributesSize' => $attributesSize,
                        'productColor' => $productColor,
                        'productVariation' => $productVariation,
                        'productCategory' => $productCategory,
                        'colorImgs' => $colorImgs
            ]);
        }
    }

    /**
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['products']) || !in_array('add', $rolePermitions['products']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Products();
        $productCategory = $model->getProductCategory();
        $vendorArr = $model->getVendorList();
        if (isset($_POST['name']) && $_POST['name'] != '') {

            if ($productId = $model->saveProduct()) {
                if (isset($_POST['save'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $productId]);
                }
            } else {
                return $this->render('create', [
                            'model' => $model,
                            'productCategory' => $productCategory,
                            'vendorArr' => $vendorArr
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'productCategory' => $productCategory,
                        'vendorArr' => $vendorArr
            ]);
        }
    }

    /**
     * Updates an existing Products model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['products']) || !in_array('view', $rolePermitions['products']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $productModel = new Products();
        $helper = new Helper();
        $model = $this->findModel($id);

        if (isset($_GET['id']) && isset($_GET['colorImg']) && $_GET['colorImg'] != '') {
            $imageName = $_GET['colorImg'];

            $model->delColorImage($_GET['p_id']);
            if (file_exists(Yii::$app->basePath . '/web/images/products/' . $imageName)) {
                unlink(Yii::$app->basePath . '/web/images/products/' . $imageName);
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['colorImgDelete']);
            echo "done";
            exit;
        }
        if (isset($_GET['id']) && isset($_GET['imgName']) && $_GET['imgName'] != '' && ($model->featured_image != "" )) {
            $imageName = $model->$_GET['imgName'];
            $model->delImage($_GET['id'], $_GET['imgName']);
            if (file_exists(Yii::$app->basePath . '/web/images/products/' . $imageName)) {
                unlink(Yii::$app->basePath . '/web/images/products/' . $imageName);
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['productImgDelete']);
            echo "done";
            exit;
        }
        if (isset($_GET['remove_color']) && $_GET['remove_color'] != '') {
            $model->removeColor($_GET['remove_color'], $id);
            exit;
        }
        if (isset($_GET['size']) && $_GET['size'] != '') {
            $productModel->changeSize($id);
            exit;
        }

        $fromProductInfo = Yii::$app->getRequest()->getQueryParam('fromProductInfo', "0");
        $fromVariation = Yii::$app->getRequest()->getQueryParam('fromVariation', "0");
        $fromVendor = Yii::$app->getRequest()->getQueryParam('fromVendor', "0");

        $productSize = $productModel->getProductSize($id);
        $categorySize = $productModel->getCategorySize($model->category_id);
        $attributesSize = $productModel->getCategoryFits($model->category_id);
        $productColorArr = $productModel->getProductColor($id);
        $productColor = $productColorArr['colors'];
        $colorImgs = $productColorArr['images'];
        $productVariation = $productModel->getProductVariations($id);
        $productCategory = $productModel->getProductCategory();
        $featuredImg = $model->featured_image;
        $vendorArr = $productModel->getVendorDetail($model->vendor_id);

        if (isset($_POST['formType']) && $_POST['formType'] == 'productUpdate') {
            if ($productModel->editProduct($id, $featuredImg)) {
                if (isset($_POST['save']) && $_POST['save'] == '1') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $id]);
                }
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                            'model' => $model,
                            'vendorArr' => $vendorArr,
                            'productSize' => $productSize,
                            'categorySize' => $categorySize,
                            'attributesSize' => $attributesSize,
                            'productColor' => $productColor,
                            'productVariation' => $productVariation,
                            'productCategory' => $productCategory,
                            'colorImgs' => $colorImgs,
                            'fromVariation' => $fromVariation,
                            'fromVendor' => $fromVendor
                ]);
            }
        } else if (isset($_POST['formType']) && $_POST['formType'] == 'productVariation') {

            if ((isset($_POST['saveQtyAndPrice']) && $_POST['saveQtyAndPrice'] != 'no') || (isset($_POST['saveSizeAndFits']) && $_POST['saveSizeAndFits'] != 'no')) {
                $productModel->saveVariation($id);
                return $this->redirect(['update', 'id' => $id, 'fromVariation' => $fromVariation]);
            }
            if (isset($_POST['color_name'])) {
                if ($_POST['color_name'] == '') {
                    Yii::$app->session->setFlash('error', 'Please enter color.');
                    return $this->redirect(['update', 'id' => $id, 'fromVariation' => $fromVariation]);
                }
                $colorArr = (new \yii\db\Query())
                        ->from('product_variation')
                        ->where(['color' => $_POST['color_name']])
                        ->andWhere(['product_id' => $id])
                        ->count();
                if ($colorArr > 0) {
                    Yii::$app->session->setFlash('error', 'Color already exist.');
                    return $this->redirect(['update', 'id' => $id, 'fromVariation' => $fromVariation]);
                }
                if (isset($_FILES['other_color_images']['name']) && $_FILES['other_color_images']['name'][0] != '') {
                    if (count($_FILES["other_color_images"]["tmp_name"]) > 3) {
                        Yii::$app->session->setFlash('error', 'Sorry, you can upload only 3 images for othor image.');
                        return $this->redirect(['update', 'id' => $id, 'fromVariation' => $fromVariation]);
                    }
                }
                $productModel->editColors($id);
                return $this->redirect(['update', 'id' => $id, 'fromVariation' => $fromVariation]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'vendorArr' => $vendorArr,
                        'productSize' => $productSize,
                        'categorySize' => $categorySize,
                        'attributesSize' => $attributesSize,
                        'productColor' => $productColor,
                        'productVariation' => $productVariation,
                        'productCategory' => $productCategory,
                        'colorImgs' => $colorImgs,
                        'fromVariation' => $fromVariation,
                        'fromVendor' => $fromVendor
            ]);
        }
    }

    /**
     * Deletes an existing Products model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Products::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDisapproved() {
        $this->layout = FALSE;
        return $this->render('disapproved');
    }

    public function actionVendorCategory($id) {
        $this->layout = FALSE;
        $model = new Products();
        $category = $model->getVendorCategory($id);
        echo json_encode($category);
        exit;
    }

}
