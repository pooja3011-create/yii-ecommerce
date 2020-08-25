<?php

namespace frontend\controllers;

use Yii;
use app\models\Vendors;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VendorsController implements the CRUD actions for Vendors model.
 */
class VendorsController extends Controller {

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
     * Lists all Vendors models.
     * @return array
     */
    public function actionIndex() {
        $model = new Vendors();
        $vendorArr = $model->vendorList();
        return $this->render('index', [
                    'vendorArr' => $vendorArr,
        ]);
    }

    /**
     * vendor profile
     * @return array
     */
    public function actionVendorDetail($vendor) {
        $model = new Vendors();
        $vendorArr = $model->vendorDetail($vendor);
        return $this->render('vendor-detail', [
                    'vendorArr' => $vendorArr,
        ]);
    }
    /**
     * Lists all Vendors Products.
     * @return array
     */
    public function actionVendorProducts($vendor) {
//        echo 'test';exit;
        $model = new Vendors();
        $productArr = $model->vendorProducts($vendor);
        return $this->render('vendor-products', [
                    'productArr' => $productArr,
        ]);
    }

}
