<?php

namespace app\models;

use Yii;

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
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
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
            [['vendor_code'], 'required'],
            [['gender', 'status', 'shop_description', 'commission_rate', 'account_notes'], 'string'],
            [['country_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['vendor_code', 'name', 'email', 'phone', 'password', 'shop_name', 'address1', 'address2', 'tax_vat_number', 'commission_type', 'product_approval', 'shop_banner_image', 'shop_logo_image', 'bank_name', 'account_holder_name', 'swift_code', 'device_type', 'device_id', 'access_token'], 'string', 'max' => 255],
            [['city', 'state', 'account_number'], 'string', 'max' => 100],
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
            'status' => 'Status',
            'shop_name' => 'Shop Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'state' => 'State',
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

    /** get vendor list
     * @return array vendor list
     *       */
    function vendorList() {
        $result = (new \yii\db\Query())
                ->select(['id', 'name', 'vendor_code', 'email', 'phone', 'canonical_url'])
                ->from('vendors')
                ->where(['status' => '1'])
                ->orderBy(['id' => SORT_DESC])
                ->all();
        return $result;
    }

    /** get vendor detail
     * @return array vendor detail
     *       */
    function vendorDetail($vendor) {
        $result = (new \yii\db\Query())
                ->select(['id', 'name', 'vendor_code', 'email', 'phone', 'shop_name', 'country_id', 'shop_description', 'tax_vat_number', 'shop_banner_image', 'shop_logo_image', 'bank_name', 'account_number', 'account_holder_name', 'swift_code', 'account_notes'])
                ->from('vendors')
                ->where(['canonical_url' => $vendor])
                ->one();

        return $result;
    }

    /** get vendor list
     * @return array vendor list
     *       */
    function vendorProducts($vendor) {
        $result = (new \yii\db\Query())
                ->select(['id'])
                ->from('vendors')
                ->where(['canonical_url' => $vendor])
                ->one();

        $vendor_id = $result['id'];
        $pagination = isset(Yii::$app->params['pagination']) ? Yii::$app->params['pagination'] : 10;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '1';
        $start = ($page * $pagination) - $pagination;

        $where = ' WHERE p.vendor_id= "' . $vendor_id . '"';
        $orderBy = ' order by p.id desc';

        $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.sku,p.status,c.id as category,pa.display_price,pa.display_currency,COUNT( op.product_id ) as product_sell
FROM products p
left JOIN category c ON p.category_id = c.id 
LEFT JOIN order_products op ON p.id = op.product_id
'
                . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id' .
                $where . ' Group by p.id  ' . $orderBy . ' limit ' . $start . ',' . $pagination;


        $products = Yii::$app->db->createCommand($query)->queryAll();
        return $products;
    }

}
