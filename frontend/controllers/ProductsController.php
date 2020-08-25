<?php

namespace frontend\controllers;

use Yii;
use app\models\Products;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex($category, $child_cat = '') {
        $model = new Products();

        $productArr = $model->products($category, $child_cat);
        $categoryArr = $model->category($category);
        $sizeArr = $model->sizeList();
        $colorArr = $model->colorList();
        return $this->render('index', [
                    'productArr' => $productArr,
                    'categoryArr' => $categoryArr,
                    'sizeArr' => $sizeArr,
                    'colorArr' => $colorArr,
        ]);
    }

    /**
     * product search
     */
    public function actionProductSearch($keyword) {
        $model = new Products();

        $productArr = $model->productSearch($keyword);
        return $this->render('product-search', [
                    'productArr' => $productArr
        ]);
    }

    /**
     * product detail
     */
    public function actionProductDetail($product, $product_code) {
        $model = new Products();

        $productArr = $model->productDetail($product_code);
        $similarProducts = $model->similarProducts($product_code);
        return $this->render('product-detail', [
                    'productArr' => $productArr,
                    'similarProducts' => $similarProducts,
        ]);
    }

    /**
     * add product into cart
     * **/
    public function actionAddToCart($product_id, $user_id, $variation_id) {
        $saveArr['product_id'] = $product_id;
        $saveArr['user_id'] = $user_id;
        $saveArr['variation_id'] = $variation_id;
        $saveArr['created_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $user_id;
        $saveArr['status'] = '1';
        Yii::$app->db->createCommand()->insert('shopping_cart', $saveArr)->execute();
        exit;
    }
    
    /**
     * add product into wishlist
     * **/
    public function actionAddToWishlist($product_id, $user_id) {
        $saveArr['product_id'] = $product_id;
        $saveArr['user_id'] = $user_id;
        $saveArr['created_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $user_id;
        $saveArr['status'] = '1';
        Yii::$app->db->createCommand()->insert('wishlist', $saveArr)->execute();
        exit;
    }

        
}
