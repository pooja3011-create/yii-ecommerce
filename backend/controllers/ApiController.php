<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\Api;
use yii\helpers\Url;

/**
 * Api controller
 */
class ApiController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     *  vendor login
     * */
    public function actionLogin() {

        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['email']) && $_REQUEST['email'] != '' && isset($_REQUEST['password']) && $_REQUEST['password'] != '' && isset($_REQUEST['device_type']) && isset($_REQUEST['device_id'])) {
            try {
                $email = $_REQUEST['email'];
                $password = $_REQUEST['password'];
                $model->login($email, $password);
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => $status, 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => $status, 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => $status, 'message' => $message));
        }
    }

    /**
     *  forgot password
     * */
    public function actionForgotPassword() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['email']) && $_REQUEST['email'] != '') {
            try {
                $email = $_REQUEST['email'];
                $model->forgotPassword($email);
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     *  Vendor detail
     * */
    public function actionVendor() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['type'])) {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->vendorDetail($_REQUEST['user_id'], $_REQUEST['type']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Edit Vendor detail
     * */
    public function actionVendorEdit() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['type'])) {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->vendorEdit($_REQUEST['user_id'], $_REQUEST['type']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Product list and search
     * */
    public function actionProducts() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->products($_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Product detail
     * */
    public function actionProductDetail() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->productDetail($_REQUEST['id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Add Product
     * */
    public function actionProductAdd() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['name']) && $_REQUEST['name'] != '' && isset($_REQUEST['sku']) && $_REQUEST['sku'] != '' && isset($_REQUEST['category']) && $_REQUEST['category'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->productAdd($_REQUEST);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Add Product
     * */
    public function actionCategory() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->category($_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Add Product
     * */
    public function actionProductEdit() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->productEdit($_REQUEST);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Category Size and Attributes
     * */
    public function actionCategoryAttributes() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['category_id']) && $_REQUEST['category_id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->categoryAttributes($_REQUEST['category_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * Country List
     * */
    public function actionCountry() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->countryList();
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * get vendor order list
     *       */
    public function actionOrders() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->orders($_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * get order Status
     *       */
    public function actionOrderStatus() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->orderStatus();
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * get order detail
     *       */
    public function actionOrderDetail() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->orderDetail($_REQUEST['order_id'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * get shipment products
     * * */
    public function actionShipmentProducts() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->shipmentProducts($_REQUEST['order_id'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * add product shipment * */
    public function actionAddShipment() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->addShipment($_REQUEST['order_id'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * add product shipment * */
    public function actionEditShipment() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '' && isset($_REQUEST['productId']) && $_REQUEST['productId'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->editShipment($_REQUEST['order_id'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    
    /**
     * cancel order or product * */
    public function actionCancelOrder() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['order_id']) && $_REQUEST['order_id'] != '' && isset($_REQUEST['type']) && $_REQUEST['type']!= '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->cancelOrder($_REQUEST['order_id'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }
    
    /**
     * get reports
     *       */
    public function actionReports() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['type']) && $_REQUEST['type'] != '') {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->reports($_REQUEST['type'], $_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }

    /**
     * delete notification 
     *       */
    public function actionDeleteNotification() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' && isset($_REQUEST['notification_id']) && $_REQUEST['notification_id'] != '' ) {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->deleteNotification($_REQUEST['notification_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }
 
    /**
     * get notification list
     *       */
    public function actionNotifications() {
        $model = new Api();
        $error = $model->getStatusCodeMessage();
        $message = '';
        $status = '0';

        if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['access_token']) && $_REQUEST['access_token'] != '' ) {
            try {
                if ($model->validateUser($_REQUEST['user_id'], $_REQUEST['access_token'])) {
                    $model->notifications($_REQUEST['user_id']);
                } else {
                    $message = $error[40];
                    $model->getJson(array('status' => '40', 'message' => $message));
                }
            } catch (yii\db\Exception $e) {
                $message = $error[20];
                $model->getJson(array('status' => '20', 'message' => $message));
            } catch (yii\base\UnknownMethodException $e) {
                $message = $error[30];
                $model->getJson(array('status' => '30', 'message' => $message));
            }
        } else {
            $message = $error[10];
            $model->getJson(array('status' => '10', 'message' => $message));
        }
    }
}
