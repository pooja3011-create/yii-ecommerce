<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vendor_payment".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $payment_date
 * @property string $payment_ref_number
 * @property string $notes
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 */
class VendorPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vendor_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'payment_date'], 'required'],
            [['order_id', 'updated_by'], 'integer'],
            [['payment_date', 'created_date', 'updated_date'], 'safe'],
            [['notes'], 'string'],
            [['payment_ref_number'], 'string', 'max' => 255],
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
            'payment_date' => 'Payment Date',
            'payment_ref_number' => 'Payment Ref Number',
            'notes' => 'Notes',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
        ];
    }
    
    public function getRefNumber() {
        return $this->payment_ref_number;
//        if($this->payment_ref_number != ''){
//            return $this->payment_ref_number;
//        }
//        else{
//            return 'N/A';
//        }
        
    }
}
