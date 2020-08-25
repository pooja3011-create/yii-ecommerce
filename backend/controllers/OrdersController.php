<?php

namespace backend\controllers;

use Yii;
use app\models\Orders;
use backend\models\OrdersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Helper;
use app\models\Country;
use yii\helpers\ArrayHelper;
use backend\models\OrderProductsSearch;
use kartik\mpdf\Pdf;

/**
 * OrdersController implements the CRUD actions for Orders model.
 */
class OrdersController extends Controller {

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
     * Lists all Orders models.
     * @return mixed
     */
    public function actionIndex() {
        
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('list', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->orderSearch(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Orders model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Orders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Orders();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Orders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('view', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $model = new Orders();
        if (isset($_GET['compareData']) && $_GET['compareData'] != '') {
            $message = '';
            
            $orderArrNew = $model->orderDetail($id);
            $ordrerArrOld = Yii::$app->session->get('orderArr');
            $productArrOld = Yii::$app->session->get('productArr');            
            foreach ($ordrerArrOld as $key => $val) {
                if ($val != $orderArrNew[$key]) {
                    $message = "This operation can't be completed as the order details have been updated by some other user. The page will be reloaded now.";
                }
            }
            foreach ($productArrOld as $key => $val) {
                foreach ($orderArrNew['products'] as $product){
                    if($product['product_id'] == $key && $product['shipment_status'] != $val){
                        $message ="This operation can't be completed as the order details have been updated by some other user. The page will be reloaded now.";
                    }
                }
            }
            $jsonArr = array('message' => $message);
            echo json_encode($jsonArr);
            exit;
        }
        if (isset($_POST['txtBillingAdd1']) && $_POST['txtBillingAdd1'] != '') {
            if ($model->editOrder($id)) {
                return $this->redirect(['order-detail', 'id' => $id]);
            }
        }
        $products = $model->orderProducts($id);
        $orderArr = $model->orderDetail($id);

        $config = $helper->getConfiguration();
        $orderStatus = $helper->getOrderStatus();
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $countryArr = ArrayHelper::map($countries, 'id', 'name');

        $fromShipment = Yii::$app->getRequest()->getQueryParam('fromShipment', "0");
        $shipmentSearchModel = new OrderProductsSearch();
        $shipmentDataProvider = $shipmentSearchModel->search(Yii::$app->request->queryParams, $id);

        $fromVendor = Yii::$app->getRequest()->getQueryParam('fromVendor', "0");
        $fromUser = Yii::$app->getRequest()->getQueryParam('fromUser', "0");

        return $this->render('update', [
                    'model' => $model,
                    'fromShipment' => $fromShipment,
                    'orderArr' => $orderArr,
                    'config' => $config,
                    'orderStatus' => $orderStatus,
                    'countryArr' => $countryArr,
                    'shipmentSearchModel' => $shipmentSearchModel,
                    'shipmentDataProvider' => $shipmentDataProvider,
                    'productArr' => $products,
                    'fromVendor' => $fromVendor,
                    'fromUser' => $fromUser,
        ]);
    }

    /*     * *
     * oreder detail
     * * */

    public function actionOrderDetail($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('view', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $orderArr = $model->orderDetail($id);
        $config = $helper->getConfiguration();
        $orderStatus = $helper->getOrderStatus();
        $products = $model->orderProducts($id);
        $shipmentSearchModel = new OrderProductsSearch();
        $shipmentDataProvider = $shipmentSearchModel->search(Yii::$app->request->queryParams, $id);
        $fromVendor = Yii::$app->getRequest()->getQueryParam('fromVendor', "0");
        $fromShipment = Yii::$app->getRequest()->getQueryParam('fromShipment', "0");
        $fromUser = Yii::$app->getRequest()->getQueryParam('fromUser', "0");
        return $this->render('order-detail', [
                    'model' => $model,
                    'fromShipment' => $fromShipment,
                    'orderArr' => $orderArr,
                    'config' => $config,
                    'orderStatus' => $orderStatus,
                    'shipmentSearchModel' => $shipmentSearchModel,
                    'shipmentDataProvider' => $shipmentDataProvider,
                    'productArr' => $products,
                    'fromVendor' => $fromVendor,
                    'fromUser' => $fromUser,
        ]);
    }

    /**
     * cancel order
     * * */
    public function actionCancelOrder($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('edit', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $model->cancelOrder($id);
        return $this->redirect(['order-detail', 'id' => $id]);
    }

    /**
     * complete order
     * * */
    public function actionCompleteOrder($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('edit', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $model->completeOrder($id);
        return $this->redirect(['order-detail', 'id' => $id]);
    }

    /**
     * add order shipment detail
     * * */
    public function actionAddShipment($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('edit', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $orderDate = (new \yii\db\Query())
                ->from('orders')
                ->select(['order_date'])
                ->where(['id' => $id])
                ->one();
        $shippedFromArr = $model->shipmentBy($id);
        $products = $model->orderProducts($id);
        if (isset($_GET['vendor_id']) && $_GET['vendor_id'] != '') {
            $vendorProducts = $model->orderProducts($id, $_GET['vendor_id']);
            echo json_encode($vendorProducts);
            exit;
        }

        if (isset($_POST['carrier']) && $_POST['carrier'] != '') {
            if ($model->saveShipment($id, $shippedFromArr)) {
                return $this->redirect(['order-detail', 'id' => $id, 'fromShipment' => 1]);
            } else {
                return $this->render('add-shipment', [
                            'model' => $model,
                            'shippedFromArr' => $shippedFromArr,
                            'productArr' => $products,
                            'orderDate' => $orderDate,
                ]);
            }
        }
        return $this->render('add-shipment', [
                    'model' => $model,
                    'shippedFromArr' => $shippedFromArr,
                    'productArr' => $products,
                    'orderDate' => $orderDate,
        ]);
    }

    /**
     * view and update order shipment detail
     * * */
    public function actionShipmentUpdate($id, $order_id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('view', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $model = new Orders();
        $orderDate = (new \yii\db\Query())
                ->from('orders')
                ->select(['order_date'])
                ->where(['id' => $id])
                ->one();
        $shippedFromArr = $model->shipmentBy($order_id);
        $shipmentArr = $model->getShipmentDetail($id);
        if (isset($_POST['carrier']) && $_POST['carrier'] != '') {
            if ($model->updateShipment($id, $order_id)) {
                return $this->redirect(['order-detail', 'id' => $order_id, 'fromShipment' => 1]);
            }
        }
        return $this->render('shipment-update', [
                    'model' => $model,
                    'shippedFromArr' => $shippedFromArr,
                    'shipmentArr' => $shipmentArr,
                    'orderDate' => $orderDate,
        ]);
    }

    /** mark shipment as delivered * */
    function actionDelivered($id, $order_id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('edit', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $model->orderDelivered($id);
        return $this->redirect(['order-detail', 'id' => $order_id, 'fromShipment' => 1]);
    }

    /**
     * List all order invoices
     * @return mixed values
     * * */
    public function actionInvoices() {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['invoices']) || !in_array('list', $rolePermitions['invoices']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('list', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $searchModel = new OrdersSearch();
        $dataProvider = $searchModel->invoiceSearch(Yii::$app->request->queryParams);

        return $this->render('invoices', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * send order invoice
     * @return mixed values
     * * */
    public function actionSendInvoice($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['invoices']) || !in_array('view', $rolePermitions['invoices']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('list', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $model = new Orders();
        $model->sendInvoice($id);
        $fromOrders = Yii::$app->getRequest()->getQueryParam('fromOrders', "0");
        if ($fromOrders > 0) {
            return $this->redirect(['order-detail', 'id' => $id]);
        } else {
            return $this->redirect(['invoices']);
        }
    }

    function actionShipmentHistory($order_id, $product_id) {
        $this->layout = FALSE;
        $model = new Orders();
        $shipmentArr = $model->shipmentHistory($order_id, $product_id);

        return $this->render('shipment-history', [
                    'shipmentArr' => $shipmentArr,
        ]);
    }

    function actionCancellationNote($product_id) {
        $this->layout = FALSE;
        $model = new Orders();

        $productArr = $model->productList($product_id);
        return $this->render('cancellation-note', [
                    'productArr' => $productArr,
        ]);
    }

    /**
     * send order invoice
     * @return mixed values
     * * */
    public function actionSendOrderConfirmation($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('list', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $model = new Orders();
        $model->sendOrderConfirmation($id);
        return $this->redirect(['order-detail', 'id' => $id]);
    }

    /**
     * send order invoice
     * @return mixed values
     * * */
    public function actionDownloadInvoice($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['invoices']) || !in_array('view', $rolePermitions['invoices']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }

        $model = new Orders();
        $orderArr = $model->orderDetail($id);

        $fileName = $orderArr['invoice_id'] . '-' . date('dmY', strtotime($orderArr['invoice_date']));
        $helper = new Helper();
        $orderStatus = $helper->getOrderStatus();
        $orderArr['order_status'] = $orderStatus[$orderArr['status']];
        $config = $helper->getConfiguration();
        $orderArr['config'] = $config;
        // get your HTML raw content without any layouts or scripts
        $content = $this->renderPartial('_invoice', [
            'orderArr' => $orderArr
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_DOWNLOAD,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px} .rightSection{width:40%;float:right;} .clearfix{clear: both;} .col-sm-3{width: 20%;float: left;} .col-sm-4{width: 25%;float: left;} .col-sm-6{width: 45%;float: left;} .text-right{text-align: right;} .text-center{text-align: center;} .well{   background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
    margin-bottom: 20px;
    min-height: 20px;
    padding: 19px;} .m-t{ margin-top: 15px;} .m-b{ margin-bottom: 15px;} .line{ background-color: transparent;
    border-width: 1px 0 0;
    font-size: 0;
    height: 2px;
    margin: 10px 0;
    overflow: hidden;} 
    .table{margin-bottom: 20px;max-width: 100%;width: 100%;} 
    table{ background-color: transparent;  border-collapse: collapse;border-spacing: 0;}
    .table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td{border-top: 0 none;} 
    .table > thead > tr > th{border-bottom: 2px solid #ddd;vertical-align: bottom;} 
    th{text-align: left;}
   td {border-top: 1px solid #ddd;line-height: 1.42857;padding: 8px;vertical-align: top;}
   ',
            'options' => ['title' => 'Order Invoice'],
            'filename' => $fileName . '.pdf',
            'methods' => [
                'SetHeader' => ['Order Invoice'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf->render();
    }

    /**
     * Deletes an existing Orders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * generate orders excel sheet
     */
    public function actionExportOrders() {

        $where = '';
        if (isset($_GET['OrdersSearch']) && !empty($_GET['OrdersSearch'])) {
            $postParams = $_GET['OrdersSearch'];
            if (isset($postParams['user_code']) && $postParams['user_code'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'user_code = "' . $postParams['user_code'] . '"';
            }
            if (isset($postParams['first_name']) && $postParams['first_name'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'first_name LIKE "%' . $postParams['first_name'] . '%"';
            }
            if (isset($postParams['email']) && $postParams['email'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where = 'email LIKE "%' . $postParams['email'] . '%"';
            }
            if (isset($postParams['phone']) && $postParams['phone'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'phone LIKE "%' . $postParams['phone'] . '%"';
            }

            if (isset($postParams['created_at']) && $postParams['created_at'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $date = date('Y-m-d', strtotime(str_replace('/', '-', $postParams['created_at'])));
                $where .= 'DATEDIFF( DATE_FORMAT( created_at, "%Y-%m-%d" ) , "' . $date . '" ) >= 0';
            }
        }

        $query = (new \yii\db\Query())
                ->select("orders.id, orders.order_date, orders.amount, orders.status, orders.estimate_delivery_date, orders.updated_date, orders.guest_checkout,user.first_name, user.phone ")
                ->from('orders')
                ->join('LEFT JOIN', 'user', 'user.id=orders.user_id')
                ->where($where)
                ->orderBy(['id' => SORT_DESC]);
        $command = $query->createCommand();
        $results = $command->queryAll();

        $helper = new Helper();
        $orderStatus = $helper->getOrderStatus();

        $filename = 'Ordres-' . time() . '.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=" . $filename);
        echo '<table border="1" width="100%">        
        <thead>
            <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Consumer</th>
            <th>Consumer Phone Number</th>
            <th>Grand Total</th>
            <th>Estimate Delivery Date</th>
            <th>Last Status Update</th>
            <th>Guest Checkout?</th>
            <th>Status</th>
            </tr>
        </thead>';

        if (!empty($results)) {
            echo '<tbody>';
            foreach ($results as $result) {
                $guest_checkout = ($result['guest_checkout'] == "0") ? "No" : "Yes";
                echo '<tr>
                    <td>' . $result['id'] . '</td>
                    <td>' . date('d/m/Y h:i A', strtotime($result['order_date'])) . '</td>                                    <td>' . $result['first_name'] . '</td>
                    <td>' . $result['phone'] . '</td>
                    <td>S$' . $result['amount'] . '</td>
                    <td>' . date('d/m/Y', strtotime($result['estimate_delivery_date'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($result['updated_date'])) . '</td>
                    <td>' . $guest_checkout . '</td>
                    <td>' . $orderStatus[$result['status']] . '</td>
                     </tr>';
            }
            echo '</tbody>';
        }
        echo '</table>';
    }

    
    function actionPrintInvoice($id) {
        $helper = new Helper();
        $rolePermitions = $helper->getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['orders']) || !in_array('edit', $rolePermitions['orders']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new Orders();
        $model->printinvoice($id);
        return $this->redirect(['order-detail', 'id' => $id]);
    }
}
