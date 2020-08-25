<?php

namespace app\models;

use Yii;
use common\models\Helper;

/**
 * This is the model class for table "vendors".
 *
 * @property integer $id
 * @property string $vendor_code
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $gender
 * @property string $password
 * @property string $status
 * @property string $shop_name
 * @property integer $country_id
 * @property string $shop_description
 * @property string $tax_vat_number
 * @property string $commission_type
 * @property string $commission_rate
 * @property string $product_approval
 * @property string $shop_banner_image
 * @property string $shop_logo_image
 * @property string $bank_name
 * @property string $account_number
 * @property string $account_holder_name
 * @property string $swift_code
 * @property string $account_notes
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 * @property string $device_type
 * @property string $device_id
 * @property string $access_token
 */
class Vendors extends \yii\db\ActiveRecord {

    public $imageFiles;
    public $imageFiles2;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'vendors';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            [['vendor_code'], 'required'],
//            ['vendor_code', 'filter', 'filter' => 'trim'],
//            ['vendor_code', 'required', 'message' => 'Please enter vendor code.'],
//            ['name', 'filter', 'filter' => 'trim'],
//            ['name', 'required', 'message' => 'Please enter vendor name.'],
//            ['phone', 'filter', 'filter' => 'trim'],
//            ['phone', 'required', 'message' => 'Please enter phone number.'],
//            ['email', 'filter', 'filter' => 'trim'],
//            ['email', 'required', 'message' => 'Please enter vendor email.'],
//            ['email', 'email', 'message' => 'Please enter valid vendor email.'],
            [['gender', 'status', 'shop_description', 'commission_rate', 'account_notes'], 'string'],
            [['country_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['vendor_code', 'name', 'email', 'phone', 'password', 'shop_name', 'tax_vat_number', 'commission_type', 'product_approval', 'shop_banner_image', 'shop_logo_image', 'bank_name', 'account_holder_name', 'swift_code', 'device_type', 'device_id', 'access_token', 'address1', 'address2', 'city', 'state'], 'string', 'max' => 255],
            [['account_number'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'vendor_code' => 'Vendor Code',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'gender' => 'Gender',
            'password' => 'Password',
            'password_repeat' => 'Password',
            'status' => 'Status',
            'shop_name' => 'Shop Name',
            'country_id' => 'Country ID',
            'shop_description' => 'Shop Description',
            'tax_vat_number' => 'Tax Vat Number',
            'commission_type' => 'Commission Type',
            'commission_rate' => 'Commission Rate',
            'product_approval' => 'Product Approval',
            'shop_banner_image' => 'Shop Banner Image',
            'shop_logo_image' => 'Shop Logo Image',
            'bank_name' => 'Bank Name',
            'account_number' => 'Account Number',
            'account_holder_name' => 'Account Holder Name',
            'swift_code' => 'Swift Code',
            'account_notes' => 'Account Notes',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'device_type' => 'Device Type',
            'device_id' => 'Device ID',
            'access_token' => 'Access Token',
        ];
    }

    function saveVendor($postParam) {
        if (isset($postParam['email']) && $postParam['email'] != '') {
            $count = Vendors::find()
                    ->where(['email' => $postParam['email']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateEmail']);
                return FALSE;
            }
            $helper = new Helper();
            $vendorCode = $helper->generateVendorCode();
            $params['name'] = $postParam['name'];
            $params['email'] = $postParam['email'];
            $params['password'] = $postParam['password'];
            $postParam['password'] = $helper->encryptIt($postParam['password']);
            $postParam['created_date'] = date('Y-m-d H:i:s');
            $postParam['updated_date'] = date('Y-m-d H:i:s');
            $postParam['updated_by'] = Yii::$app->user->id;
            $postParam['vendor_code'] = $vendorCode;

            unset($postParam['password_repeat']);
            Yii::$app->db->createCommand()->insert('vendors', $postParam)->execute();
            $vendorId = Yii::$app->db->getLastInsertID();

            if (isset($_POST['category'])) {
                $category = array();
                $i = 0;
                foreach ($_POST['category'] as $key => $val) {
                    $category[$i] = array($vendorId, $val, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), Yii::$app->user->id);
                    $i++;
                }
                Yii::$app->db->createCommand()->batchInsert('vendor_assigned_category', ['vendor_id', 'category_id', 'created_date', 'updated_date', 'updated_by'], $category)->execute();
            }

            $mailTo = $params['email'];
            $mailFrom = Yii::$app->params['adminEmail'];
            $subject = 'New vendor account.';
            \Yii::$app->mailer->compose('/vendors/sendVendor', ['params' => $params])
                    ->setFrom($mailFrom)
                    ->setTo($mailTo)
                    ->setSubject($subject)
                    ->send();

            Yii::$app->session->setFlash('success', Yii::$app->params['saveVendor']);
            return $vendorId;
        }
    }

    function editVendor($id, $postParam) {
        if (isset($postParam['vendor_code']) && $postParam['vendor_code'] != '') {
            $count = Vendors::find()
                    ->where(['email' => $postParam['email']])
                    ->andWhere(['!=', 'id', $id])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateEmail']);
                return FALSE;
            }
            $helper = new Helper();
            if ($postParam['password'] != '' && $postParam['password_repeat'] != '') {
                $postParam['password'] = $helper->encryptIt($postParam['password']);
            } else {
                unset($postParam['password']);
            }
            $postParam['updated_date'] = date('Y-m-d H:i:s');
            $postParam['updated_by'] = Yii::$app->user->id;

            unset($postParam['password_repeat']);
            unset($postParam['shop_banner_image']);
            unset($postParam['shop_logo_image']);
            Yii::$app->db->createCommand()->update('vendors', $postParam, ['id' => $id])->execute();
            if (isset($_POST['category'])) {
                Yii::$app->db->createCommand()->delete('vendor_assigned_category', ['vendor_id' => $id])->execute();
                $category = array();
                $i = 0;
                foreach ($_POST['category'] as $key => $val) {
                    $category[$i] = array($id, $val, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), Yii::$app->user->id);
                    $i++;
                }
                Yii::$app->db->createCommand()->batchInsert('vendor_assigned_category', ['vendor_id', 'category_id', 'created_date', 'updated_date', 'updated_by'], $category)->execute();
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['editVendor']);
            return true;
        } else if (isset($postParam['shop_name']) && $postParam['shop_name'] != '') {
            $helper = new Helper();

            $postParam['updated_date'] = date('Y-m-d H:i:s');
            $postParam['updated_by'] = Yii::$app->user->id;

            unset($postParam['shop_banner_image']);
            unset($postParam['shop_logo_image']);
            Yii::$app->db->createCommand()->update('vendors', $postParam, ['id' => $id])->execute();
            if (isset($_POST['category'])) {
                Yii::$app->db->createCommand()->delete('vendor_assigned_category', ['vendor_id' => $id])->execute();
                $category = array();
                $i = 0;
                foreach ($_POST['category'] as $key => $val) {
                    $category[$i] = array($id, $val, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), Yii::$app->user->id);
                    $i++;
                }
                Yii::$app->db->createCommand()->batchInsert('vendor_assigned_category', ['vendor_id', 'category_id', 'created_date', 'updated_date', 'updated_by'], $category)->execute();
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['editVendor']);
            return true;
        } else if (isset($postParam['bank_name']) && $postParam['bank_name'] != '') {

            $helper = new Helper();

            $postParam['updated_date'] = date('Y-m-d H:i:s');
            $postParam['updated_by'] = Yii::$app->user->id;

            Yii::$app->db->createCommand()->update('vendors', $postParam, ['id' => $id])->execute();
            Yii::$app->session->setFlash('success', Yii::$app->params['editVendor']);
            return true;
        }
    }

    public function delImage($id, $imgName) {
        Yii::$app->db->createCommand(
                'UPDATE vendors SET ' . $imgName . '="" WHERE id="' . $id . '"')->execute();

        return true;
    }

    function getVendorCategory($id) {
        $category = (new \yii\db\Query())
                ->from('vendor_assigned_category')
                ->select(['category_id'])
                ->where(['vendor_id' => $id])
                ->all();
        return $category;
    }

    function getCountry($id) {
        $country = (new \yii\db\Query())
                ->from('country')
                ->select(['name'])
                ->where(['id' => $id])
                ->one();
        return $country;
    }

    function getAddress($id) {
        $address = (new \yii\db\Query())
                ->from('vendors')
                ->select(['address1', 'address2', 'city', 'state'])
                ->where(['id' => $id])
                ->one();
        $str = $address['address1'];
        $str .= ($str != '' && $address['address2'] != '') ? ', ' . $address['address2'] : $address['address2'];
        $str .= ($str != '' && $address['city']) ? ', ' . $address['city'] : $address['city'];
        $str .= ($str != '' && $address['state']) ? ', ' . $address['state'] : $address['state'];
        return $str;
    }

    public function getcommissionrate($type, $rate, $vendor_price) {
        $rate = $rate != '' ? $rate : '10';
        if ($type == 'percentage') {
            $commition = ($vendor_price * $rate) / 100;
            return 'S$' . $commition;
        } else {
            return 'S$' . $rate;
        }
    }

    public function getvendorpayment($type, $rate, $vendor_price) {
        $rate = $rate != '' ? $rate : '10';
        if ($type == 'percentage') {
            $commition = ($vendor_price * $rate) / 100;
        } else {
            $commition = $rate;
        }
        $payment = $vendor_price - $commition;
        return 'S$' . $payment;
    }

    public function addPaymentInfo() {

        $orderId = '';
        $vendorId = '';
        $date = date('Y-m-d H:i:s', strtotime($_GET['date']));
        $note = isset($_GET['note']) ? urldecode($_GET['note']) : '';
        $refNum = isset($_GET['refNum']) ? urldecode($_GET['refNum']) : '';
        $created_date = date('Y-m-d H:i:s');
        $updated_date = date('Y-m-d H:i:s');
        $updated_by = Yii::$app->user->id;
        $saveArr = array();
        $idArr = explode(',', $_GET['order_id']);
        foreach ($idArr as $key => $val) {
            $ids = explode('_', $val);

            $orderId = $ids[0];
            $vendorId = $ids[1];
            $saveArr[] = array($orderId, $vendorId, $date, $refNum, $note, $created_date, $updated_date, $updated_by);
        }

        Yii::$app->db->createCommand()->batchInsert('vendor_payment', ['order_id', 'vendor_id', 'payment_date', 'payment_ref_number', 'notes', 'created_date', 'updated_date', 'updated_by'], $saveArr)->execute();
    }

    public function getVendorPaymentInformation($order_id, $vendor_id) {
        $query = (new \yii\db\Query())
                ->select("orders.id as order_id, vendors.id as vendor_id, orders.order_date, orders.actual_delivery_date, vendors.shop_name as vendor_name, vendors.commission_rate, sum(order_products.price) as order_sum, sum(order_products.vendor_commission) as vendor_commission, sum(order_products.vendor_payment) as vendor_payment,vendors.commission_type")
                ->from('orders ')
                ->join('LEFT JOIN', 'order_products', 'order_products.order_id = orders.id')
                ->join('LEFT JOIN', 'vendors', ' vendors.id = order_products.vendor_id')
                ->where(['order_products.order_id' => $order_id])
                ->andWhere(['order_products.vendor_id' => $vendor_id])
                ->andWhere(['order_products.shipment_status' => '1'])
                ->one();

        $payment = (new \yii\db\Query())
                ->from('vendor_payment')
                ->select(['payment_date', 'payment_ref_number', 'notes'])
                ->where(['order_id' => $order_id])
                ->andWhere(['vendor_id' => $vendor_id])
                ->one();
        $query['notes'] = $payment['notes'];
        $query['payment_date'] = $payment['payment_date'];
        $query['payment_ref_number'] = $payment['payment_ref_number'];

        return $query;
    }

}
