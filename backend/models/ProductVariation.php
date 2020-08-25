<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_variation".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $color
 * @property string $size
 * @property string $display_price
 * @property string $display_currency
 * @property integer $qty
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 * @property string $status
 */
class ProductVariation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_variation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'qty', 'updated_by'], 'integer'],
            [['display_price'], 'number'],
            [['created_date', 'updated_date'], 'safe'],
            [['status'], 'string'],
            [['color', 'size', 'display_currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'color' => 'Color',
            'size' => 'Size',
            'display_price' => 'Display Price',
            'display_currency' => 'Display Currency',
            'qty' => 'Qty',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }
    
    public function getProductVariation()
    {
        return $this->display_currency . $this->display_price;
    }
}
