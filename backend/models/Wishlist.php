<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wishlist".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $product_id
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 * @property string $status
 */
class Wishlist extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'wishlist';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'product_id'], 'required'],
            [['user_id', 'product_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'product_id' => 'Product ID',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts() {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }
    
}
