<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product_reviews".
 *
 * @property integer $id
 * @property integer $product_id
 * @property integer $variation_id
 * @property integer $user_id
 * @property string $review_title
 * @property string $review
 * @property double $rating
 * @property string $review_date
 * @property string $status
 */
class ProductReviews extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_reviews';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'variation_id', 'user_id'], 'required'],
            [['product_id', 'variation_id', 'user_id'], 'integer'],
            [['review', 'status'], 'string'],
            [['rating'], 'number'],
            [['review_date'], 'safe'],
            [['review_title'], 'string', 'max' => 255],
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
            'variation_id' => 'Variation ID',
            'user_id' => 'User ID',
            'review_title' => 'Review Title',
            'review' => 'Review',
            'rating' => 'Rating',
            'review_date' => 'Review Date',
            'status' => 'Status',
        ];
    }
}
