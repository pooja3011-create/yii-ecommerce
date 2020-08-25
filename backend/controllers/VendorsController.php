<?php

namespace backend\controllers;

use Yii;
use app\models\Vendors;
use app\models\Products;
use app\models\Orders;
use backend\models\VendorsSearch;
use backend\models\ProductsSearch;
use backend\models\OrdersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Helper;
use app\models\Country;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * VendorsController implements the CRUD actions for Vendors model.
 */
class VendorsController extends Controller {

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
     * Lists all Vendors models.
     * @return mixed
     */
    public function actionIndex() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['vendors']) || !in_array('list', $rolePermitions['vendors']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Vendors();
        $searchModel = new VendorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countryArr' => $listData
        ]);
    }

    /**
     * Displays a single Vendors model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vendors model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['vendors']) || !in_array('add', $rolePermitions['vendors']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Vendors();
        $helper = new Helper();
        $randomString = $helper->generateRandom('vendors', 'vendor_code', 4);
        $categoryTree = $helper->fetchCategoryTreeList();
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');



        if (isset($_POST['Vendors'])) {

            $postParam = $_POST['Vendors'];
            if ($vendorId = $model->saveVendor($postParam)) {
                if (isset($_FILES['Vendors']['name']['shop_banner_image']) && $_FILES['Vendors']['name']['shop_banner_image'] != '') {

                    $model->imageFiles = UploadedFile::getInstances($model, 'shop_banner_image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'shop_banner_image', $model->tableName(), $model->imageFiles, $vendorId);
                }
                if (isset($_FILES['Vendors']['name']['shop_logo_image']) && $_FILES['Vendors']['name']['shop_logo_image'] != '') {

                    $model->imageFiles = UploadedFile::getInstances($model, 'shop_logo_image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'shop_logo_image', $model->tableName(), $model->imageFiles2, $vendorId);
                }
                if (isset($_POST['save']) && $_POST['save'] == '1') {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $vendorId, 'fromShop' => '1']);
                }
            } else {
                return $this->render('create', [
                            'model' => $model,
                            'randomString' => $randomString,
                            'listData' => $listData,
                            'categoryTree' => $categoryTree,
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'randomString' => $randomString,
                        'listData' => $listData,
                        'categoryTree' => $categoryTree,
            ]);
        }
    }

    /**
     * Updates an existing Vendors model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['vendors']) || !in_array('view', $rolePermitions['vendors']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = $this->findModel($id);

        // load product item model for product grid  
        $productSearchModel = new ProductsSearch();
        $productDataProvider = $productSearchModel->search(Yii::$app->request->queryParams, $id);

        // load order item model for order grid  
        $orderSearchModel = new OrdersSearch();
        $orderDataProvider = $orderSearchModel->search(Yii::$app->request->queryParams, $id);

        $helper = new Helper();
        $vendorCat = $model->getVendorCategory($id);
        $categoryArr = array();
        if (count($vendorCat) > 0) {
            foreach ($vendorCat as $cat1) {
                array_push($categoryArr, $cat1['category_id']);
            }
        }
        $categoryTree = $helper->fetchCategoryTreeList($categoryArr);

        $fromShop = Yii::$app->getRequest()->getQueryParam('fromShop', "0");
        $fromAccount = Yii::$app->getRequest()->getQueryParam('fromAccount', "0");
        $fromProducts = Yii::$app->getRequest()->getQueryParam('fromProducts', "0");
        $fromOrders = Yii::$app->getRequest()->getQueryParam('fromOrders', "0");

        if (isset($_GET['id']) && isset($_GET['imgName']) && $_GET['imgName'] != '' && ($model->shop_banner_image != "" || $model->shop_logo_image != "")) {
            $imageName = $model->$_GET['imgName'];
            $model->delImage($_GET['id'], $_GET['imgName']);
            if (file_exists(Yii::$app->basePath . '/web/images/vendor/' . $imageName)) {
                unlink(Yii::$app->basePath . '/web/images/vendor/' . $imageName);
            }
            Yii::$app->session->setFlash('success', 'Vendor image deleted successfully.');
            echo "done";
            exit;
        }
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');
        $bannerImg = $model->shop_banner_image;
        $logoImg = $model->shop_logo_image;
        if (isset($_POST['Vendors'])) {

            $postParam = $_POST['Vendors'];
            if ($model->editVendor($id, $postParam)) {
                if (isset($_FILES['Vendors']['name']['shop_banner_image']) && $_FILES['Vendors']['name']['shop_banner_image'] != '') {
                    if ($bannerImg != "") {
                        $model->delImage($id, "shop_banner_image");
                        if (file_exists(Yii::$app->basePath . '/web/images/vendor/' . $bannerImg)) {
                            unlink(Yii::$app->basePath . '/web/images/vendor/' . $bannerImg);
                        }
                    }
                    $model->imageFiles = UploadedFile::getInstances($model, 'shop_banner_image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'shop_banner_image', $model->tableName(), $model->imageFiles, $id);
                }
                if (isset($_FILES['Vendors']['name']['shop_logo_image']) && $_FILES['Vendors']['name']['shop_logo_image'] != '') {

                    if ($logoImg != "") {
                        $model->delImage($id, "shop_logo_image");
                        if (file_exists(Yii::$app->basePath . '/web/images/vendor/' . $logoImg)) {
                            unlink(Yii::$app->basePath . '/web/images/vendor/' . $logoImg);
                        }
                    }
                    $model->imageFiles2 = UploadedFile::getInstances($model, 'shop_logo_image');
                    $helper->upload(Yii::$app->basePath . '/web/images/', 'shop_logo_image', $model->tableName(), $model->imageFiles2, $id);
                }
                if (isset($_POST['save']) && $_POST['save'] == '1') {
                    return $this->redirect(['index']);
                } else {
                    $redirectArr = ['update', 'id' => $id];
                    if ($fromShop == '1') {
                        $redirectArr = ['update', 'id' => $id, 'fromAccount' => 1];
                    } else if ($fromAccount == '1') {
                        $redirectArr = ['update', 'id' => $id, 'fromProducts' => 1];
                    } else if ($fromShop != 1 && $fromAccount != 1 && $fromOrders != 1 && $fromProducts != 1) {
                        $redirectArr = ['update', 'id' => $id, 'fromShop' => 1];
                    }

                    return $this->redirect($redirectArr);
                }
            } else {
                return $this->render('update', [
                            'model' => $model,
                            'listData' => $listData,
                            'categoryTree' => $categoryTree,
                            'productSearchModel' => $productSearchModel,
                            'productDataProvider' => $productDataProvider,
                            'orderSearchModel' => $orderSearchModel,
                            'orderDataProvider' => $orderDataProvider,
                            'fromShop' => $fromShop,
                            'fromAccount' => $fromAccount,
                            'fromProducts' => $fromProducts,
                            'fromOrders' => $fromOrders
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'listData' => $listData,
                        'categoryTree' => $categoryTree,
                        'productSearchModel' => $productSearchModel,
                        'productDataProvider' => $productDataProvider,
                        'orderSearchModel' => $orderSearchModel,
                        'orderDataProvider' => $orderDataProvider,
                        'fromShop' => $fromShop,
                        'fromAccount' => $fromAccount,
                        'fromProducts' => $fromProducts,
                        'fromOrders' => $fromOrders
            ]);
        }
    }

    /**
     * Deletes an existing Vendors model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Vendors model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vendors the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Vendors::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPayments() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['vendor_payments']) || !in_array('list', $rolePermitions['vendor_payments']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Vendors();
        if (isset($_GET['refNum']) && $_GET['refNum'] != '') {
            $model->addPaymentInfo();
            exit;
        }
        // load order item model for order grid  
        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->paymentSearch(Yii::$app->request->queryParams, '1');
        return $this->render('payments', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPaymentDetail($id, $vendor_id) {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['vendor_payments']) || !in_array('view', $rolePermitions['vendor_payments']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Vendors();
        $data = $model->getVendorPaymentInformation($id, $vendor_id);
        return $this->render('payment-detail', [
                    'data' => $data,
        ]);
    }

    public function actionPaymentInfo() {
        $this->layout = FALSE;
        $model = new Vendors();
        return $this->render('payment-info');
    }

    public function actionExportVendors() {


        $where = '';
        if (isset($_GET['VendorsSearch']) && !empty($_GET['VendorsSearch'])) {
            $postParams = $_GET['VendorsSearch'];
            if (isset($postParams['vendor_code']) && $postParams['vendor_code'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' vendor_code = "' . $postParams['vendor_code'] . '"';
            }
            if (isset($postParams['name']) && $postParams['name'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' vendors.name  like"%' . $postParams['name'] . '%"';
            }
            if (isset($postParams['email']) && $postParams['email'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' email  like"%' . $postParams['email'] . '%"';
            }
            if (isset($postParams['phone']) && $postParams['phone'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' phone like"%' . $postParams['phone'] . '%"';
            }
            if (isset($postParams['phone']) && $postParams['phone'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' phone like"%' . $postParams['phone'] . '%"';
            }
            if (isset($postParams['country_id']) && $postParams['country_id'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' country_id ="' . $postParams['country_id'] . '"';
            }
            if (isset($postParams['status']) && $postParams['status'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= ' status ="' . $postParams['status'] . '"';
            }

            if (isset($postParams['created_date']) && $postParams['created_date'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $date = date('Y-m-d', strtotime(str_replace('/', '-', $postParams['created_date'])));
                $where .= 'DATEDIFF( DATE_FORMAT( created_date, "%Y-%m-%d" ) , "' . $date . '" ) >= 0';
            }
        }
        $results = (new \yii\db\Query())
                ->select('vendors.*,country.name as country_name')
                ->from('vendors')
                ->join('LEFT JOIN', 'country', 'country.id=vendors.country_id')
                ->where($where)
                ->orderBy(['id' => SORT_DESC])
                ->all();

        $filename = 'Vendors-' . time() . '.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=" . $filename);
        echo '<table border="1" width="100%">        
        <thead>
            <tr>
            <th>Vendor ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Country</th>
            <th>Vendor Since</th>
            <th>Status</th>
            </tr>
        </thead>';
        if (!empty($results)) {
            echo '<tbody>';
            foreach ($results as $result) {
                $status = ($result['status'] == '1') ? "Active" : "Inactive";
                echo '<tr>
                    <td>' . $result['vendor_code'] . '</td>
                    <td>' . $result['name'] . '</td>
                    <td>' . $result['email'] . '</td>
                    <td>' . $result['phone'] . '</td>
                    <td>' . $result['country_name'] . '</td>
                    <td>' . date('d/m/Y', strtotime($result['created_date'])) . '</td>
                    <td>' . $status . '</td>
                                       </tr>';
            }
            echo '</tbody>';
        }
        echo '</table>';
    }

}
