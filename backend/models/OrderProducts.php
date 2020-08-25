<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_products".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $vendor_id
 * @property integer $product_id
 * @property string $price
 * @property string $qty
 * @property string $shipment_status
 * @property string $carrier
 * @property string $traking_number
 * @property string $shipped_date
 * @property string $shipment_from
 * @property string $shipment_to
 * @property string $shipment_note
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 */
class OrderProducts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'vendor_id', 'product_id', 'price'], 'required'],
            [['order_id', 'vendor_id', 'product_id', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['shipment_status', 'shipment_note'], 'string'],
            [['shipped_date', 'created_date', 'updated_date'], 'safe'],
            [['qty'], 'string', 'max' => 100],
            [['carrier', 'traking_number', 'shipment_from', 'shipment_to'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'vendor_id' => 'Vendor ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'qty' => 'Qty',
            'shipment_status' => 'Shipment Status',
            'carrier' => 'Carrier',
            'traking_number' => 'Traking Number',
            'shipped_date' => 'Shipped Date',
            'shipment_from' => 'Shipment From',
            'shipment_to' => 'Shipment To',
            'shipment_note' => 'Shipment Note',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
        ];
    }
}
