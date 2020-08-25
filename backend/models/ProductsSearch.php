<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Products;

/**
 * ProductsSearch represents the model behind the search form about `app\models\Products`.
 */
class ProductsSearch extends Products {

    public $display_price;
    public $qty;
    public $category_name;
    public $vendor_name;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            [['id', 'category_id', 'updated_by'], 'integer'],
            [['name', 'category_id', 'status', 'category_name', 'vendor_name','product_code','sku'], 'safe'],
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
    public function search($params, $userID = "") {
        $query = Products::find();

        // add conditions that should always apply here

        $pageSize = Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => [
                'defaultOrder' => [
                    'product_code' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        $query->joinWith('productVariation');
        $query->joinWith('category');
        $query->joinWith('vendors');

        // grid filtering conditions
        $query->andFilterWhere([
            'products.id' => $this->id,
            'products.category_id' => $this->category_id,
            'products.created_date' => $this->created_date,
            'products.updated_date' => $this->updated_date,
            'products.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'products.name', $this->name])
                ->andFilterWhere(['=', 'products.product_code', $this->product_code])
                ->andFilterWhere(['like', 'products.sku', $this->sku])
                ->andFilterWhere(['like', 'products.description', $this->description])
                ->andFilterWhere(['like', 'products.featured_image', $this->featured_image])
                ->andFilterWhere(['like', 'products.disapprove_reason', $this->disapprove_reason])
                ->andFilterWhere(['like', 'products.status', $this->status]);

        if (!empty($userID)) {
            $query->andFilterWhere(['=', 'products.vendor_id', $userID]);
        }
        
        if (isset($_GET['ProductsSearch']['searchAll']) && $_GET['ProductsSearch']['searchAll'] != '') {
            $query->andWhere('products.name LIKE "%' . $_GET['ProductsSearch']['searchAll'] . '%"');
        }
        
        if (isset($_GET['ProductsSearch']['searchProduct']) && $_GET['ProductsSearch']['searchProduct'] != '') {
            $query->andWhere('(products.name LIKE "%' . $_GET['ProductsSearch']['searchProduct'] . '%" OR products.product_code = "' . $_GET['ProductsSearch']['searchProduct'] . '" OR products.sku = "' . $_GET['ProductsSearch']['searchProduct'] . '")');
        }
        $query->andFilterWhere(['like', 'category.name', $this->category_name]);
        $query->andFilterWhere(['like', 'vendors.name', $this->vendor_name]);
//        print_r($dataProvider);
        return $dataProvider;
    }

    public function searchQuery($params, $userID = "") {
        $query = Products::find();

        // add conditions that should always apply here

        $pageSize =Yii::$app->params['list-pagination'];
        if (isset($_GET['per-page']) && $_GET['per-page'] != '') {
            $pageSize = $_GET['per-page'];
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => $pageSize),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
//        $query->joinWith('productVariation');
        $query->joinWith('category');
        $query->joinWith('vendors');

        // grid filtering conditions
        $query->andFilterWhere([
            'products.id' => $this->id,
            'products.category_id' => $this->category_id,
            'products.created_date' => $this->created_date,
            'products.updated_date' => $this->updated_date,
            'products.updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'products.name', $this->name])
                ->andFilterWhere(['like', 'products.product_code', $this->product_code])
                ->andFilterWhere(['like', 'products.sku', $this->sku])
                ->andFilterWhere(['like', 'products.description', $this->description])
                ->andFilterWhere(['like', 'products.featured_image', $this->featured_image])
                ->andFilterWhere(['like', 'products.disapprove_reason', $this->disapprove_reason])
                ->andFilterWhere(['like', 'products.status', $this->status]);

        if (!empty($userID)) {
            $query->andFilterWhere(['=', 'products.vendor_id', $userID]);
        }
        if (isset($_GET['ProductsSearch']['searchAll']) && $_GET['ProductsSearch']['searchAll'] != '') {
            $query->andWhere('products.name LIKE "%' . $_GET['ProductsSearch']['searchAll'] . '%"');
        }
        if (isset($_GET['ProductsSearch']['searchProduct']) && $_GET['ProductsSearch']['searchProduct'] != '') {
            $query->andWhere('(products.name LIKE "%' . $_GET['ProductsSearch']['searchProduct'] . '%" OR products.id = "' . $_GET['ProductsSearch']['searchProduct'] . '" OR products.product_code = "' . $_GET['ProductsSearch']['searchProduct'] . '")');
        }
        $query->andFilterWhere(['like', 'category.name', $this->category_name]);
        $query->andFilterWhere(['like', 'vendors.name', $this->vendor_name]);
//        print_r($dataProvider);
        return $query;
    }

}
