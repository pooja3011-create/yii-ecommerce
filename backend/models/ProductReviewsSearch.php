<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProductReviews;

/**
 * ProductReviewsSearch represents the model behind the search form about `app\models\ProductReviews`.
 */
class ProductReviewsSearch extends ProductReviews
{
	public $productName;
    public $vendorName;
    public $reviewRating;
    public $consumerName;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'variation_id', 'user_id'], 'integer'],
            [['productName', 'vendorName', 'reviewRating', 'consumerName'], 'safe'],
            [['rating'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
    	$query = (new \yii\db\Query())
                ->select("product_reviews.id as prId, products.featured_image as pImage, products.name as pName, vendors.shop_name as vShopName, product_reviews.rating as prRating, product_reviews.review as prReview, CONCAT_WS(' ', `user`.`first_name`, `user`.`last_name`) as uName, product_reviews.review_date as prDate")
                ->from('product_reviews ')
                ->join('JOIN', 'products', 'product_reviews.product_id = products.id')
                ->join('JOIN', 'vendors', 'vendors.id = products.id')
                ->join('JOIN', 'user', ' product_reviews.user_id = user.id')
                ->where(['product_reviews.status' => '1'])
                ->orderBy(['prDate'=>SORT_DESC])
                ;
                
        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['defaultOrder'=>['prDate'=>SORT_DESC],'attributes' =>['prDate','consumerName','productName','vendorName','reviewRating']],
           
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        
        if (isset($_GET['ProductReviewsSearch']['productName']) && trim($_GET['ProductReviewsSearch']['productName']) != '') {
            $query->andWhere('products.name LIKE "%' . trim($_GET['ProductReviewsSearch']['productName']) . '%"');
        }
        
        if (isset($_GET['ProductReviewsSearch']['vendorName']) && trim($_GET['ProductReviewsSearch']['vendorName']) != '') {
            $query->andWhere('vendors.shop_name LIKE "%' . trim($_GET['ProductReviewsSearch']['vendorName']) . '%"');
        }
        
        if (isset($_GET['ProductReviewsSearch']['reviewRating']) && trim($_GET['ProductReviewsSearch']['reviewRating']) != '') {
            $query->andWhere('product_reviews.rating = "' . trim($_GET['ProductReviewsSearch']['reviewRating']) . '"');
        }
        
        if (isset($_GET['ProductReviewsSearch']['consumerName']) && trim($_GET['ProductReviewsSearch']['consumerName']) != '') {
            $query->andWhere('CONCAT_WS(" ", user.first_name, user.last_name) LIKE "%' . trim($_GET['ProductReviewsSearch']['consumerName']) . '%"');
        }

        return $dataProvider;
    }
}
