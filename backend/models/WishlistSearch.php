<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Wishlist;

/**
 * WishlistSearch represents the model behind the search form about `app\models\Wishlist`.
 */
class WishlistSearch extends Wishlist {

    public $product_code;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'user_id', 'product_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date', 'status', 'product_code'], 'safe'],
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
//        $query = Wishlist::find()
//                ->leftJoin('products', 'products.id=wishlist.product_id')
//                ->leftJoin('category','products.category_id=category.id');

        $query = (new \yii\db\Query())
                ->select(['`products`.`id` as product_id, `products`.`product_code`, `products`.`name` as product_name, `products`.`featured_image`, `products`.`sku`, `products`.`vendor_id`, `category`.`name` as category_name,`vendors`.`name` as vendor_name, `vendors`.`shop_name` as shop_name,`vendors`.`vendor_code` as vendor_code,pa.`display_price`,pa.`display_currency`'])
                ->from('wishlist')
                ->join('LEFT JOIN', 'products', 'products.id=wishlist.product_id')
                ->join('LEFT JOIN', 'category', 'products.category_id=category.id')
                ->join('LEFT JOIN', 'vendors', 'products.vendor_id=vendors.id')
                ->join('LEFT JOIN', '(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa', 'products.id=pa.product_id');

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
            $query->where(['=', 'wishlist.user_id', $userId]);
        }

        return $dataProvider;
    }

}
