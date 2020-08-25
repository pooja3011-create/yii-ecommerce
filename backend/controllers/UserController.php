<?php

namespace backend\controllers;

use Yii;
use app\models\User;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Country;
use yii\helpers\ArrayHelper;
use common\models\Helper;
use backend\models\OrdersSearch;
use backend\models\WishlistSearch;
use backend\models\ShoppingCartSearch;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller {

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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['consumers']) || !in_array('list', $rolePermitions['consumers']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }


        $searchModel = new UserSearch();
        $dataProvider = $searchModel->userSearch(Yii::$app->request->queryParams);
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'countryArr' => $listData
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['consumers']) || !in_array('add', $rolePermitions['consumers']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new User();
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'countryArr' => $listData
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $type) {
        $rolePermitions = Helper::getRolePermission();
        if (Yii::$app->user->id != '1' && (!isset($rolePermitions['consumers']) || !in_array('view', $rolePermitions['consumers']))) {
            return $this->redirect(['/site/unauthorized-access']);
        }
        $model = new User();
        $userArr = $model->getUserData($id, $type);
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');

        // load order item model for order grid  
        $orderSearchModel = new OrdersSearch();
        $orderDataProvider = $orderSearchModel->search(Yii::$app->request->queryParams, '', '', $id);

        // load order item model for order grid  
        $wishlistSearchModel = new WishlistSearch();
        $wishlistDataProvider = $wishlistSearchModel->search(Yii::$app->request->queryParams, $id);

// load order item model for order grid  
        $shoppingCartSearch = new ShoppingCartSearch();
        $shoppingCartDataProvider = $shoppingCartSearch->search(Yii::$app->request->queryParams, $id);

        $cartTotal = $model->getCartTotal($id);

        $fromOrders = Yii::$app->getRequest()->getQueryParam('fromOrders', "0");
        $fromWishlist = Yii::$app->getRequest()->getQueryParam('fromWishlist', "0");
        $fromShoppingCart = Yii::$app->getRequest()->getQueryParam('fromShoppingCart', "0");

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveCustomer($id, $type)) {
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                            'model' => $model,
                            'countryArr' => $listData,
                            'userArr' => $userArr,
                            'fromShoppingCart' => $fromShoppingCart,
                            'fromOrders' => $fromOrders,
                            'fromWishlist' => $fromWishlist,
                            'orderSearchModel' => $orderSearchModel,
                            'orderDataProvider' => $orderDataProvider,
                            'wishlistSearchModel' => $wishlistSearchModel,
                            'wishlistDataProvider' => $wishlistDataProvider,
                            'shoppingCartSearch' => $shoppingCartSearch,
                            'shoppingCartDataProvider' => $shoppingCartDataProvider,
                            'cartTotal' => $cartTotal
                ]);
            }
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'userArr' => $userArr,
                        'countryArr' => $listData,
                        'fromShoppingCart' => $fromShoppingCart,
                        'fromOrders' => $fromOrders,
                        'fromWishlist' => $fromWishlist,
                        'orderSearchModel' => $orderSearchModel,
                        'orderDataProvider' => $orderDataProvider,
                        'wishlistSearchModel' => $wishlistSearchModel,
                        'wishlistDataProvider' => $wishlistDataProvider,
                        'shoppingCartSearch' => $shoppingCartSearch,
                        'shoppingCartDataProvider' => $shoppingCartDataProvider,
                        'cartTotal' => $cartTotal
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * generate customer excel sheet
     */
    public function actionExportCustomers() {

        $query1 = $query = (new \yii\db\Query())
                ->select("user.id, user.user_code as user_code, user.first_name as first_name, user.email as email, user.phone as phone, user.gender as gender, user.billing_address1 as billing_address1, user.billing_city as billing_city, country.name as billing_country, user.created_at as created_at,'`registered`' as user_type,user.billing_country as country")
                ->join('LEFT JOIN', 'country', 'country.id=user.billing_country')
                ->from('user')
                ->where(['user.user_role' => '2']);

        $query2 = $query = (new \yii\db\Query())
                ->select("guest_user.id, guest_user.user_code as user_code, guest_user.first_name as first_name, guest_user.email as email, guest_user.phone as phone, guest_user.gender as gender, guest_user.billing_address1 as billing_address1, guest_user.billing_city as billing_city, country.name as billing_country, guest_user.created_at as created_at,'`guest_user`' as user_type,guest_user.billing_country as country")
                ->join('LEFT JOIN', 'country', 'country.id=guest_user.billing_country')
                ->from('guest_user');

        $where = '';
        if (isset($_GET['UserSearch']) && !empty($_GET['UserSearch'])) {
            $postParams = $_GET['UserSearch'];
            if (isset($postParams['user_code']) && $postParams['user_code'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'user_code = "' . $postParams['user_code'] . '"';
            }
            if (isset($postParams['user_type']) && $postParams['user_type'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'user_type = "`' . $postParams['user_type'] . '`"';
            }
            if (isset($postParams['first_name']) && $postParams['first_name'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'first_name LIKE "%' . $postParams['first_name'] . '%"';
            }
            if (isset($postParams['email']) && $postParams['email'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where = 'email LIKE "%' . $postParams['email'] . '%"';
            }
            if (isset($postParams['phone']) && $postParams['phone'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'phone LIKE "%' . $postParams['phone'] . '%"';
            }
            if (isset($postParams['gender']) && $postParams['gender'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'gender = "' . $postParams['gender'] . '"';
            }
            if (isset($postParams['billing_city']) && $postParams['billing_city'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'billing_city LIKE "' . $postParams['billing_city'] . '"';
            }
            if (isset($postParams['billing_country']) && $postParams['billing_country'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $where .= 'country = "' . $postParams['billing_country'] . '"';
            }
            if (isset($postParams['created_at']) && $postParams['created_at'] != '') {
                if ($where != '') {
                    $where .= ' and ';
                }
                $date = date('Y-m-d', strtotime(str_replace('/', '-', $postParams['created_at'])));
                $where .= 'DATEDIFF( DATE_FORMAT( created_at, "%Y-%m-%d" ) , "' . $date . '" ) >= 0';
            }
        }
        $query = (new \yii\db\Query())
                ->from(['customer' => $query1->union($query2)])
                ->where($where)
                ->orderBy(['id' => SORT_DESC]);
        $command = $query->createCommand();
        $results = $command->queryAll();

        $filename = 'Consumers-' . time() . '.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=" . $filename);
        echo '<table border="1" width="100%">        
        <thead>
            <tr>
            <th>Consumer ID</th>
            <th>Type</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Gender</th>
            <th>Address</th>
            <th>City</th>
            <th>Country</th>
            <th>Consumer Since</th>
            </tr>
        </thead>';

        if (!empty($results)) {
            echo '<tbody>';
            foreach ($results as $result) {
                echo '<tr>
                    <td>' . $result['user_code'] . '</td>
                    <td>' . ucwords(str_replace('`', '', str_replace('_', ' ', $result['user_type']))) . '</td>
                    <td>' . $result['first_name'] . '</td>
                    <td>' . $result['email'] . '</td>
                    <td>' . $result['phone'] . '</td>
                    <td>' . ucfirst($result['gender']) . '</td>
                    <td>' . $result['billing_address1'] . '</td>
                    <td>' . $result['billing_city'] . '</td>
                    <td>' . $result['billing_country'] . '</td>
                    <td>' . date('d/m/Y', strtotime($result['created_at'])) . '</td>
                    </tr>';
            }
            echo '</tbody>';
        }
        echo '</table>';
    }

}
