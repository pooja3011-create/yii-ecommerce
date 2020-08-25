<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ShoppingCart;

/**
 * ShoppingCartSearch represents the model behind the search form about `app\models\ShoppingCart`.
 */
class ShoppingCartSearch extends ShoppingCart {

    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'user_id', 'product_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date', 'status'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $userId) {
//        $query = ShoppingCart::find();

        $query = (new \yii\db\Query())
                ->select(['`products`.`id` as product_id, `products`.`product_code`, `products`.`name` as product_name, `products`.`featured_image`, `products`.`sku`, `products`.`vendor_id`, `category`.`name` as category_name,`vendors`.`name` as vendor_name,`vendors`.`shop_name` as shop_name,`vendors`.`vendor_code` as vendor_code,product_variation.`display_price`,product_variation.`display_currency`'])
                ->from('shopping_cart')
                ->join('LEFT JOIN', 'products', 'products.id=shopping_cart.product_id')
                ->join('LEFT JOIN', 'category', 'products.category_id=category.id')
                ->join('LEFT JOIN', 'vendors', 'products.vendor_id=vendors.id')
                ->join('LEFT JOIN', 'product_variation', 'product_variation.id=shopping_cart.variation_id');

        // add conditions that should always apply here
        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => ['defaultOrder'=>['product_code'=>SORT_DESC],'attributes' => ['product_code', 'product_name', 'category_name', 'sku', 'display_price','vendor_code']]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($userId)) {
            $query->where(['=', 'shopping_cart.user_id', $userId]);
        }
        return $dataProvider;
    }

}
