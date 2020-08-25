<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Vendors;
use app\models\VendorPayment;
use app\models\OrderProducts;
use common\models\Helper;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vendor_id
 * @property integer $product_id
 * @property string $order_date
 * @property string $amount
 * @property string $estimate_delivery_date
 * @property string $actual_delivery_date
 * @property string $status
 * @property string $updated_date
 * @property string $guest_checkout
 * @property string $billing_address1
 * @property string $billing_address2
 * @property string $billing_city
 * @property string $billing_state
 * @property integer $billing_country
 * @property string $billing_zip
 * @property string $billing_phone
 * @property string $shipping_address1
 * @property string $shipping_address2
 * @property string $shipping_city
 * @property string $shipping_state
 * @property integer $shipping_country
 * @property string $shipping_zip
 * @property string $shipping_phone
 * @property string $promocode
 * @property string $invoice_id
 * @property string $invoice_date
 * @property integer $updated_by
 */
class Orders extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'order_date', 'amount'], 'required'],
            [['user_id', 'billing_country', 'shipping_country', 'updated_by'], 'integer'],
            [['order_date', 'estimate_delivery_date', 'actual_delivery_date', 'updated_date', 'invoice_date'], 'safe'],
            [['amount'], 'number'],
            [['status', 'guest_checkout'], 'string'],
            [['billing_address1', 'billing_address2', 'billing_city', 'billing_state', 'shipping_address1', 'shipping_address2', 'shipping_city', 'shipping_state', 'promocode', 'invoice_id'], 'string', 'max' => 255],
            [['billing_zip'], 'string', 'max' => 50],
            [['billing_phone', 'shipping_zip', 'shipping_phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'order_date' => 'Order Date',
            'amount' => 'Amount',
            'estimate_delivery_date' => 'Estimate Delivery Date',
            'actual_delivery_date' => 'Actual Delivery Date',
            'status' => 'Status',
            'updated_date' => 'Updated Date',
            'guest_checkout' => 'Guest Checkout',
            'billing_address1' => 'Billing Address1',
            'billing_address2' => 'Billing Address2',
            'billing_city' => 'Billing City',
            'billing_state' => 'Billing State',
            'billing_country' => 'Billing Country',
            'billing_zip' => 'Billing Zip',
            'billing_phone' => 'Billing Phone',
            'shipping_address1' => 'Shipping Address1',
            'shipping_address2' => 'Shipping Address2',
            'shipping_city' => 'Shipping City',
            'shipping_state' => 'Shipping State',
            'shipping_country' => 'Shipping Country',
            'shipping_zip' => 'Shipping Zip',
            'shipping_phone' => 'Shipping Phone',
            'promocode' => 'Promocode',
            'invoice_id' => 'Invoice ID',
            'invoice_date' => 'Invoice Date',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendors() {
        return $this->hasOne(Vendors::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderProducts() {
        return $this->hasMany(OrderProducts::className(), ['order_id' => 'id']);
    }

    public function orderDetail($id) {
        $helper = new Helper();

        $query = (new \yii\db\Query())
                ->select("orders.*,user.first_name, user.phone,user.email,country.name as billing_country_name,c1.name as shipping_country_name")
                ->from('orders')
                ->join('LEFT JOIN', 'user', 'user.id=orders.user_id')
                ->join('LEFT JOIN', 'country', 'country.id=orders.billing_country')
                ->join('LEFT JOIN', 'country c1', 'c1.id=orders.shipping_country')
                ->where(['orders.id' => $id]);
        $command = $query->createCommand();
        $results = $command->queryOne();

        $productQuery = (new \yii\db\Query())
                ->select("products.id as product_id, products.name as product_name,product_variation.display_price as product_price, vendors.name as vendor_name, order_products.price as order_price, order_products.qty as order_qty, order_products.shipment_status, order_products.shipped_date,order_products.traking_number,order_products.shipment_history,order_products.order_cancel_note")
                ->from('products')
                ->join('LEFT JOIN', 'order_products', 'products.id=order_products.product_id')
                ->join('LEFT JOIN', 'product_variation', 'product_variation.id=order_products.variation_id')
                ->join('LEFT JOIN', 'vendors', 'vendors.id=order_products.vendor_id')
                ->where(['order_products.order_id' => $id]);
        $productCommand = $productQuery->createCommand();
        $products = $productCommand->queryAll();
        $results['products'] = $products;

        return $results;
    }

    function cancelOrder($id) {
        Yii::$app->db->createCommand()->update('orders', ['status' => '4'], 'id=' . $id)->execute();
        Yii::$app->session->setFlash('success', Yii::$app->params['cancelOrder']);
        return true;
    }

    function completeOrder($id) {
        $helper = new Helper();
        $config = $helper->getConfiguration();
        $tax = (isset($config['tax_rate']) && $config['tax_rate'])?$config['tax_rate']:10;
        $order = (new \yii\db\Query())
                ->select(['invoice_id'])
                ->from('orders')
                ->orderBy(['invoice_id' => SORT_DESC])
                ->one();
        $invID = (isset($order['invoice_id'])) ? $order['invoice_id'] + 1 : '1';
        $invoiceId = str_pad($invID, 4, '0', STR_PAD_LEFT);
        $saveArr['status'] = '1';
        $saveArr['invoice_date'] = date('Y:m:d H:i:s');
        $saveArr['actual_delivery_date'] = date('Y:m:d H:i:s');
        $saveArr['invoice_id'] = $invoiceId;
        Yii::$app->db->createCommand()->update('orders', $saveArr, 'id=' . $id)->execute();
        $orderProducts = (new \yii\db\Query())
                ->select(['id','price'])
                ->from('order_products')
                ->where(['order_id' => $id])
                ->all();
        foreach ($orderProducts as $product) {
            $commission = ($product['price'] * $tax)/100;
            $payment = $product['price'] - $commission;
            Yii::$app->db->createCommand()->update('order_products', ['vendor_commission' => $commission, 'vendor_payment'=>$payment], 'id="' . $product['id'] . '"')->execute();
        }
        Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => '1'], 'shipment_status="4" and order_id="' . $id . '"')->execute();
        $this->sendOrderConfirmation($id);
        Yii::$app->session->setFlash('success', Yii::$app->params['compleateOrder']);
        return true;
    }

    function editOrder($id) {

//        $saveArr['status'] = isset($_POST['cmbStatus']) ? $_POST['cmbStatus'] : '';
        $saveArr['billing_address1'] = isset($_POST['txtBillingAdd1']) ? $_POST['txtBillingAdd1'] : '';
        $saveArr['billing_address2'] = isset($_POST['txtBillingAdd2']) ? $_POST['txtBillingAdd2'] : '';
        $saveArr['billing_city'] = isset($_POST['txtBillingCity']) ? $_POST['txtBillingCity'] : '';
        $saveArr['billing_zip'] = isset($_POST['txtBillingZip']) ? $_POST['txtBillingZip'] : '';
        $saveArr['billing_country'] = isset($_POST['cmbBillingCountry']) ? $_POST['cmbBillingCountry'] : '';
        $saveArr['billing_phone'] = isset($_POST['txtBillingPhone']) ? $_POST['txtBillingPhone'] : '';
        $saveArr['shipping_address1'] = isset($_POST['txtShippingAdd1']) ? $_POST['txtShippingAdd1'] : '';
        $saveArr['shipping_address2'] = isset($_POST['txtShippingAdd2']) ? $_POST['txtShippingAdd2'] : '';
        $saveArr['shipping_city'] = isset($_POST['txtShippingCity']) ? $_POST['txtShippingCity'] : '';
        $saveArr['shipping_country'] = isset($_POST['cmbShippingCountry']) ? $_POST['cmbShippingCountry'] : '';
        $saveArr['shipping_zip'] = isset($_POST['txtShippingZip']) ? $_POST['txtShippingZip'] : '';
        $saveArr['shipping_phone'] = isset($_POST['txtShippingPhone']) ? $_POST['txtShippingPhone'] : '';
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = Yii::$app->user->id;

        $returnAmount = 0;
        if (isset($_POST['shipmentStatus']) && !empty($_POST['shipmentStatus'])) {
            foreach ($_POST['shipmentStatus'] as $key => $val) {
                if ($val != '') {
                    if (($val == '6' || $val == '7') && (isset($_POST['orderNote'][$key]) && $_POST['orderNote'][$key] != '')) {
                        Yii::$app->db->createCommand()->update('order_products', ['order_cancel_note' => $_POST['orderNote'][$key]], ['product_id' => $key, 'order_id' => $id])->execute();
                    }
                    $data = (new \yii\db\Query())
                            ->select(['delivered_date', 'shipment_history', 'price', 'qty'])
                            ->from('order_products')
                            ->where(['order_id' => $id])
                            ->andWhere(['product_id' => $key])
                            ->one();
                    if ($val == '2' || $val == '3' || $val == '9') {
                        if ($data['delivered_date'] == '' OR ( $data['shipment_history'] != '' && $val == '3')) {
                            Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => $val, 'delivered_date' => date('Y:m:d H:i:s')], ['product_id' => $key, 'order_id' => $id])->execute();
                        } else {
                            Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => $val], ['product_id' => $key, 'order_id' => $id])->execute();
                        }
                    } else {
                        Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => $val], ['product_id' => $key, 'order_id' => $id])->execute();
                    }

                    if ($val == '6' || $val == '7' || $val == '8') {
                        $returnAmount = $returnAmount + $data['price'];
                    }
                }
            }
        }

        if ($returnAmount > 0) {
            $orderArr = (new \yii\db\Query())
                    ->select(['amount', 'subtotal', 'shipping_rate', 'tax_rate', 'tax_paid', 'coupon_discount', 'total_paid', 'return_cancel_amount', 'return_cancel_tax_amount'])
                    ->from('orders')
                    ->where(['id' => $id])
                    ->one();
            $taxRate = ($returnAmount * $orderArr['tax_rate']) / 100;
            $updateArr['return_cancel_amount'] = $returnAmount;
            $updateArr['return_cancel_tax_amount'] = $taxRate;
            $updateArr['amount'] = $orderArr['total_paid'] - ($returnAmount + $taxRate );
            Yii::$app->db->createCommand()->update('orders', $updateArr, ['id' => $id])->execute();
        }

        Yii::$app->db->createCommand()->update('orders', $saveArr, 'id=' . $id)->execute();
        Yii::$app->session->setFlash('success', Yii::$app->params['editOrder']);
        return true;
    }

    /**
     * return list of admin user roles and vendor 
     *      */
    function shipmentBy($order_id) {
        $vendorIds = array();
        $data = (new \yii\db\Query())
                ->select(['vendor_id'])
                ->from('order_products')
                ->where(['order_id' => $order_id])
                ->andWhere("`traking_number` = ''  OR `traking_number` IS NULL OR shipment_status='3'")
                ->all();
        foreach ($data as $vendor) {
            $vendorIds[] = $vendor['vendor_id'];
        }
        $ids = implode(',', $vendorIds);
        $query1 = (new \yii\db\Query())
                ->select("id,name")
                ->from('user_roles')
                ->where(['status' => '1'])
                ->andWhere(['!=', 'id', '2'])
                ->all();
        $result = array();
        foreach ($query1 as $role) {
            $result['R_' . $role['id']] = $role['name'];
        }

        if ($ids != '') {
            $query2 = (new \yii\db\Query())
                    ->select("`id`,CONCAT_WS('-',`vendor_code`,`shop_name`) as name")
                    ->from('vendors')
                    ->where("id in (" . $ids . ")")
                    ->all();
            foreach ($query2 as $vendor) {
                $result[$vendor['id']] = $vendor['name'];
            }
        }




        return $result;
    }

    function getShipmentDetail($id) {

        if (isset($_GET['shippedBy']) && $_GET['shippedBy'] == 'Administrator') {
            $shipmentArr = (new \yii\db\Query())
                    ->select(['orders.carrier', 'orders.traking_number', 'orders.shipped_date', 'orders.shipment_from', 'orders.shipment_to', 'orders.shipment_note', 'orders.status as shipment_status', 'order_products.product_id', 'products.name'])
                    ->from('orders')
                    ->join('LEFT JOIN', 'order_products', 'orders.id=order_products.order_id')
                    ->join('LEFT JOIN', 'products', 'products.id=order_products.product_id')
                    ->where(['orders.traking_number' => $id])
                    ->all();
        } else {
            $shipmentArr = (new \yii\db\Query())
                    ->select(['order_products.carrier', 'order_products.traking_number', 'order_products.shipped_date', 'order_products.shipment_from', 'order_products.shipment_to', 'order_products.shipment_note', 'order_products.shipment_status', 'order_products.product_id', 'products.name'])
                    ->from('order_products')
                    ->join('LEFT JOIN', 'products', 'products.id=order_products.product_id')
                    ->where(['order_products.traking_number' => $id])
                    ->all();
        }

        return $shipmentArr;
    }

    function updateShipment($trackNo, $order_id) {
        $saveArr['carrier'] = isset($_POST['carrier']) ? $_POST['carrier'] : '';
        $saveArr['shipped_date'] = (isset($_POST['shipped_date']) && $_POST['shipped_date'] != '') ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_POST['shipped_date']))) : '';
        $saveArr['shipment_note'] = isset($_POST['shipment_note']) ? $_POST['shipment_note'] : '';
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = Yii::$app->user->id;
        $saveArr['traking_number'] = isset($_POST['traking_number']) ? $_POST['traking_number'] : '';

        if (isset($_GET['shippedBy']) && $_GET['shippedBy'] == 'Administrator') {
            $count = (new \yii\db\Query())
                    ->from('orders')
                    ->where(['traking_number' => $saveArr['traking_number']])
                    ->andWhere(['id' => $order_id])
                    ->andWhere(['carrier' => $saveArr['carrier']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateTrackingNo']);
                return false;
            }
            Yii::$app->db->createCommand()->update('orders', $saveArr, 'traking_number="' . $trackNo . '"')->execute();
        } else {
            $count = (new \yii\db\Query())
                    ->from('order_products')
                    ->where(['traking_number' => $saveArr['traking_number']])
                    ->andWhere(['order_id' => $order_id])
                    ->andWhere(['carrier' => $saveArr['carrier']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateTrackingNo']);
                return false;
            }
            Yii::$app->db->createCommand()->update('order_products', $saveArr, 'traking_number="' . $trackNo . '"')->execute();
        }

        Yii::$app->session->setFlash('success', Yii::$app->params['editShipment']);
        return true;
    }

    function orderDelivered($trackNo) {
        if (isset($_GET['shippedBy']) && $_GET['shippedBy'] == 'Administrator') {
            Yii::$app->db->createCommand()->update('orders', ['delivered_date' => date('Y:m:d H:i:s'), 'status' => '1'], 'traking_number="' . $trackNo . '"')->execute();
        } else {
            Yii::$app->db->createCommand()->update('order_products', ['delivered_date' => date('Y:m:d H:i:s'), 'shipment_status' => '1'], 'traking_number="' . $trackNo . '"')->execute();
        }

        Yii::$app->session->setFlash('success', 'Shipment delivered successfully.');
        return true;
    }

    function orderProducts($id, $vendorId = '') {
        if ($vendorId != '' && $vendorId != 'admin') {
            $productArr = (new \yii\db\Query())
                    ->select(['order_products.product_id', 'products.name'])
                    ->from('order_products')
                    ->join('LEFT JOIN', 'products', 'products.id=order_products.product_id')
                    ->where(['order_products.order_id' => $id])
                    ->andWhere(['order_products.vendor_id' => $vendorId])
                    ->andWhere("`order_products`.`traking_number` = ''  OR `order_products`.`traking_number` IS NULL Or shipment_status='3'")
                    ->all();
        } else {
            $productArr = (new \yii\db\Query())
                    ->select(['order_products.product_id', 'products.name'])
                    ->from('order_products')
                    ->join('LEFT JOIN', 'products', 'products.id=order_products.product_id')
                    ->where(['order_products.order_id' => $id])
//                    ->andWhere("`order_products`.`traking_number` = ''  OR `order_products`.`traking_number` IS NULL")
                    ->all();
        }
        $products = (new \yii\db\Query())
                ->select(['order_products.product_id'])
                ->from('order_products')
                ->where(['order_products.order_id' => $id])
                ->andWhere("`order_products`.`traking_number` != '' ")
                ->all();
        $ids = array();
        foreach ($products as $product) {
            array_push($ids, $product['product_id']);
        }
        $i = 0;
        foreach ($productArr as $data) {
            if (in_array($data['product_id'], $ids)) {
                $productArr[$i]['selected'] = 'yes';
            } else {
                $productArr[$i]['selected'] = 'no';
            }
            $i++;
        }
        return $productArr;
    }

    function saveShipment($order_id, $shippedFromArr) {
        $shipment_from = isset($_POST['shipment_from']) ? ($_POST['shipment_from']) : '';
        $pos = explode('_', $shipment_from);

        if (count($pos) == 2) {
            $productArr = array();
            $saveArr['carrier'] = isset($_POST['carrier']) ? $_POST['carrier'] : '';
            $saveArr['traking_number'] = isset($_POST['traking_number']) ? $_POST['traking_number'] : '';
            $count = (new \yii\db\Query())
                    ->from('orders')
                    ->where(['traking_number' => $saveArr['traking_number']])
                    ->andWhere(['id' => $order_id])
                    ->andWhere(['carrier' => $saveArr['carrier']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateTrackingNo']);
                return false;
            }
            $saveArr['shipped_date'] = (isset($_POST['shipped_date']) && $_POST['shipped_date'] != '') ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_POST['shipped_date']))) : '';
            $saveArr['shipment_note'] = isset($_POST['shipment_note']) ? $_POST['shipment_note'] : '';
            $saveArr['shipment_to'] = isset($_POST['shipment_to']) ? $_POST['shipment_to'] : '';
            $saveArr['updated_date'] = date('Y-m-d H:i:s');
            $saveArr['delivered_date'] = date('Y-m-d H:i:s');
            $saveArr['updated_by'] = Yii::$app->user->id;
            $saveArr['status'] = '2';

            $saveArr['shipment_from'] = isset($shippedFromArr[$shipment_from]) ? $shippedFromArr[$shipment_from] : 'Administrator';

            $productArr = (new \yii\db\Query())
                    ->select("products.id, products.name")
                    ->from('products')
                    ->join('LEFT JOIN', 'order_products', 'products.id=order_products.product_id')
                    ->where(['order_products.order_id' => $order_id])
                    ->all();

            Yii::$app->db->createCommand()->update('orders', $saveArr, ['id' => $order_id])->execute();
            Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => '4'], ['order_id' => $order_id, 'shipment_status' => '2'])->execute();
            $query = (new \yii\db\Query())
                    ->select("user.first_name, user.phone,user.email")
                    ->from('orders')
                    ->join('LEFT JOIN', 'user', 'user.id=orders.user_id')
                    ->where(['orders.id' => $order_id]);

            $command = $query->createCommand();
            $results = $command->queryOne();

            $mailFrom = Yii::$app->params['adminEmail'];
            $mailTo = trim($results['email']);
            $subject = 'Order Shipment Information';
            $params = $saveArr;
            $params['order_id'] = $order_id;
            $params['name'] = $results['first_name'];
            $params['products'] = $productArr;

            if ($mailTo != '') {
                \Yii::$app->mailer->compose('/orders/sendShipment', ['params' => $params])
                        ->setFrom($mailFrom)
                        ->setTo($mailTo)
                        ->setSubject($subject)
                        ->send();
            }

            Yii::$app->session->setFlash('success', Yii::$app->params['saveShipment']);
            return true;
        } else {
            $productArr = array();
            $saveArr['carrier'] = isset($_POST['carrier']) ? $_POST['carrier'] : '';
            $saveArr['traking_number'] = isset($_POST['traking_number']) ? $_POST['traking_number'] : '';
            $count = (new \yii\db\Query())
                    ->from('order_products')
                    ->where(['traking_number' => $saveArr['traking_number']])
                    ->andWhere(['order_id' => $order_id])
                    ->andWhere(['carrier' => $saveArr['carrier']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateTrackingNo']);
                return false;
            }
            $saveArr['shipped_date'] = (isset($_POST['shipped_date']) && $_POST['shipped_date'] != '') ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_POST['shipped_date']))) : '';
            $saveArr['shipment_note'] = isset($_POST['shipment_note']) ? $_POST['shipment_note'] : '';
            $saveArr['shipment_to'] = isset($_POST['shipment_to']) ? $_POST['shipment_to'] : '';
            $saveArr['updated_date'] = date('Y-m-d H:i:s');
            $saveArr['updated_by'] = Yii::$app->user->id;
            $saveArr['shipment_status'] = '5';

            $saveArr['shipment_from'] = isset($shippedFromArr[$shipment_from]) ? $shippedFromArr[$shipment_from] : 'Administrator';
            $productId = isset($_POST['productId']) ? $_POST['productId'] : array();

            $ids = '';
            foreach ($productId as $key => $val) {
                $ids .= ($ids != '') ? ',' . $val : $val;
                $productArr[] = (new \yii\db\Query())
                        ->select(['id', 'name'])
                        ->from('products')
                        ->where(['id' => $val])
                        ->one();
                $shipmentArr = (new \yii\db\Query())
                        ->select(['id', 'shipment_status', 'carrier', 'traking_number', 'shipped_date', 'delivered_date', 'shipment_from', 'shipment_to', 'shipment_note', 'shipment_history'])
                        ->from('order_products')
                        ->where(['order_id' => $order_id])
                        ->andWhere(['product_id' => $val])
                        ->one();
                if (isset($shipmentArr['shipment_status']) && $shipmentArr['shipment_status'] == '3') {
                    $historyArr = array();
                    if (isset($shipmentArr['shipment_history']) && $shipmentArr['shipment_history'] != '') {
                        $historyArr = unserialize($shipmentArr['shipment_history']);
                    }
                    unset($shipmentArr['shipment_history']);
                    $historyArr[] = $shipmentArr;
                    $shipmentHistory = serialize($historyArr);
                    Yii::$app->db->createCommand()->update('order_products', ['shipment_history' => $shipmentHistory], 'product_id="' . $val . '" and order_id="' . $order_id . '"')->execute();
                }
            }

            Yii::$app->db->createCommand()->update('order_products', $saveArr, 'product_id in (' . $ids . ')  and order_id="' . $order_id . '"')->execute();

            $query = (new \yii\db\Query())
                    ->select("email")
                    ->from('user')
                    ->where(['id' => '1']);

            $command = $query->createCommand();
            $results = $command->queryOne();

            $mailFrom = Yii::$app->params['adminEmail'];
            $mailTo = trim($results['email']);
            $subject = 'Order Shipment Information';
            $params = $saveArr;
            $params['order_id'] = $order_id;
            $params['name'] = 'Administrator';
            $params['products'] = $productArr;

            if ($mailTo != '') {
                \Yii::$app->mailer->compose('/orders/sendShipment', ['params' => $params])
                        ->setFrom($mailFrom)
                        ->setTo($mailTo)
                        ->setSubject($subject)
                        ->send();
            }


            Yii::$app->session->setFlash('success', Yii::$app->params['saveShipment']);
            return true;
        }
    }

    function sendInvoice($id) {
        $helper = new Helper();

        $query = (new \yii\db\Query())
                ->select("orders.*,user.first_name, user.phone,user.email,country.name as billing_country_name,c1.name as shipping_country_name")
                ->from('orders')
                ->join('LEFT JOIN', 'user', 'user.id=orders.user_id')
                ->join('LEFT JOIN', 'country', 'country.id=orders.billing_country')
                ->join('LEFT JOIN', 'country c1', 'c1.id=orders.shipping_country')
                ->where(['orders.id' => $id]);
        $command = $query->createCommand();
        $results = $command->queryOne();

        $productQuery = (new \yii\db\Query())
                ->select("products.name as product_name,product_variation.display_price as product_price, vendors.name as vendor_name, order_products.price as order_price, order_products.qty as order_qty, order_products.shipment_status, order_products.shipped_date")
                ->from('products')
                ->join('LEFT JOIN', 'order_products', 'products.id=order_products.product_id')
                ->join('LEFT JOIN', 'product_variation', 'product_variation.id=order_products.variation_id')
                ->join('LEFT JOIN', 'vendors', 'vendors.id=order_products.vendor_id')
                ->where(['order_products.order_id' => $id]);
        $productCommand = $productQuery->createCommand();
        $products = $productCommand->queryAll();
        $results['products'] = $products;

        $helper = new Helper();
        $orderStatus = $helper->getOrderStatus();
        $results['order_status'] = $orderStatus[$results['status']];
        $config = $helper->getConfiguration();
        $results['config'] = $config;
        $mailFrom = Yii::$app->params['adminEmail'];
        $mailTo = trim($results['email']);
        $subject = 'Order Invoice ' . $results['invoice_id'];

        if ($mailTo) {
            \Yii::$app->mailer->compose('/orders/sendInvoice', ['params' => $results])
                    ->setFrom($mailFrom)
                    ->setTo($mailTo)
                    ->setSubject($subject)
                    ->send();
        }

        Yii::$app->session->setFlash('success', Yii::$app->params['invoiceEmailSend']);
        return TRUE;
    }

    function sendOrderConfirmation($id) {
        $query = (new \yii\db\Query())
                ->select("user.first_name, user.phone,user.email")
                ->from('orders')
                ->join('LEFT JOIN', 'user', 'user.id=orders.user_id')
                ->where(['orders.id' => $id]);

        $command = $query->createCommand();
        $results = $command->queryOne();

        $mailFrom = Yii::$app->params['adminEmail'];
        $mailTo = trim($results['email']);
        $subject = 'Order confirmation ';
        $params['order_id'] = $id;
        $params['name'] = $results['first_name'];

        if ($mailTo) {
            \Yii::$app->mailer->compose('/orders/sendOrderConfirmation', ['params' => $params])
                    ->setFrom($mailFrom)
                    ->setTo($mailTo)
                    ->setSubject($subject)
                    ->send();
        }

        Yii::$app->session->setFlash('success', Yii::$app->params['orderCompleateSend']);
        return TRUE;
    }

    function shipmentHistory($order_id, $product_id) {
        $shipmentArr = (new \yii\db\Query())
                ->select(['shipment_history'])
                ->from('order_products')
                ->where(['order_id' => $order_id])
                ->andWhere(['product_id' => $product_id])
                ->one();
        $data = unserialize($shipmentArr['shipment_history']);
        return $data;
    }

    function productList($product_id) {
        $data = (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('products')
                ->where('id in (' . $product_id . ')')
                ->all();
        return $data;
    }

}
