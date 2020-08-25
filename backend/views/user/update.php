<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$rolePermitions = Helper::getRolePermission();
$tabOrder = '';
$tabUser = '';
$tabWishlist = '';
$tabCart = '';
$condumerName = isset($userArr['first_name']) ? '- ' . $userArr['first_name'] : '';
if ($fromOrders > 0) {
    $this->title = "Consumer's Orders List " . $condumerName;
    $tabOrder = 'active';
} else if ($fromWishlist > 0) {
    $this->title = "Consumer's Wish List " . $condumerName;
    $tabWishlist = 'active';
} else if ($fromShoppingCart > 0) {
    $this->title = "Consumer's Shopping Cart " . $condumerName;
    $tabCart = 'active';
} else {
    $this->title = "Consumer's Account Information " . $condumerName;
    $tabUser = 'active';
}
$helper = new Helper();
$orderStatus = $helper->getOrderStatus();

/** orders grid start */
$OrderColumnArr = [
    [
        'options' => ['style' => 'width:9%;'],
        'attribute' => 'id',
        'label' => 'Order ID',
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'order_date',
        'label' => 'Order Date',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:10%;'],
        'attribute' => 'amount',
        'label' => 'Total',
        'value' => function ($orderSearchModel) {
    return 'S$' . $orderSearchModel->amount;
},
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'estimate_delivery_date',
        'label' => 'Est. Delivery Date',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'updated_date',
        'label' => 'Last Updated',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'guest_checkout',
        'label' => 'Guest Checkout?',
        'value' => function ($orderSearchModel) {
    return $orderSearchModel->guest_checkout == 1 ? 'Yes' : 'No';
},
        'filter' => Html::DropDownList('OrdersSearch[guest_checkout]', $orderSearchModel->guest_checkout, array("1" => "Yes", "0" => "No"), array('class' => 'form-control', 'prompt' => 'View All')),
    ],
    [
        'options' => ['style' => 'width:18%;'],
        'attribute' => 'status',
        'value' => function ($orderSearchModel) {

    $orderStatus = Helper::getOrderStatus();
    $status = $orderStatus[$orderSearchModel->status];
    return $status;
},
        'filter' => Html::DropDownList('OrdersSearch[status]', $orderSearchModel->status, $orderStatus, array('class' => 'form-control', 'prompt' => 'View All')),
//                                                                'filter' => $orderStatus,
    ],
];
if (Yii::$app->user->id == '1' || (isset($rolePermitions['orders']) && in_array('view', $rolePermitions['orders']))) {
    $OrderColumnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Action',
        'contentOptions' => ['class' => 'action'],
        'template' => '{update}',
        'buttons' => ['update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', ['/orders/order-detail', 'id' => $model->id, 'fromUser' => $model->user_id], ['title' => 'View']);
            }
                ],
            ];
        }
        /** orders grid end */
        ?>
        <section class="panel">
            <header class="panel-heading bg-light">
                <ul class="nav nav-tabs nav-justified">
                    <li class="<?php echo $tabUser; ?>" id="account_info"><a data-toggle="tab" href="#account_info" aria-controls="account_info" role="tab" data-toggle="tab" onclick="openTab('', '');">Account Info</a></li>
                    <?php if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('list', $rolePermitions['consumers'])) {
                        ?> <li id="orders" class="<?php echo $tabOrder; ?>"><a data-toggle="tab" href="#orders" aria-controls="orders" role="tab" data-toggle="tab" onclick="openTab('fromOrders', '');">Orders</a></li>           
                        <li id="wishlist" class="<?php echo $tabWishlist; ?>"><a data-toggle="tab" href="#wishlist" aria-controls="wishlist" role="tab" data-toggle="tab" onclick="openTab('fromWishlist', '');">Wish List</a></li>           
                        <li id="shopping_cart" class="<?php echo $tabCart; ?>"><a data-toggle="tab" href="#shopping_cart" aria-controls="shopping_cart" role="tab" data-toggle="tab" onclick="openTab('fromShoppingCart', '');">Shopping Cart</a></li>   
                    <?php }
                    ?>

                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="account_info" class="tab-pane fade in <?php echo $tabUser; ?>">
                        <?php
                        $form = ActiveForm::begin([
                                    'options' => [
                                        'name' => 'customerSave',
                                        'id' => 'customerSave',
                                        'class' => 'bs-example form-horizontal',
                                        'tag' => false,
                                    ]
                        ]);
                        $postData = $userArr;
                        if (isset($_POST['User'])) {
                            $postData = $_POST['User'];
                        }
                        ?>
                        <div class="row cleafix">
                            <div class="col-lg-9">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Consumer ID</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'user_code', [ 'inputOptions' => ['value' => (isset($postData['user_code']) ? $postData['user_code'] : ''), 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Registration Date</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'created_at', [ 'inputOptions' => ['value' => ((isset($postData['created_at']) && $postData['created_at'] != '') ? date('d/m/Y', strtotime($postData['created_at'])) : ''), 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Full Name</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'first_name', [ 'inputOptions' => ['value' => (isset($postData['first_name']) ? $postData['first_name'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Email</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'email', [ 'inputOptions' => ['value' => (isset($postData['email']) ? $postData['email'] : ''), 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Phone Number</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'phone', [ 'inputOptions' => ['value' => (isset($postData['phone']) ? $postData['phone'] : ''), 'class' => 'form-control', 'onkeypress' => 'return isPhone(event);']])->textInput(['maxlength' => '20'])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Gender</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'gender', [ 'inputOptions' => ['value' => (isset($postData['gender']) ? $postData['gender'] : ''), 'class' => 'form-control']])->dropDownList(['male' => 'Male', 'female' => 'Female'])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Address Line 1</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'billing_address1', [ 'inputOptions' => ['value' => (isset($postData['billing_address1']) ? $postData['billing_address1'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">City</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'billing_city', [ 'inputOptions' => ['value' => (isset($postData['billing_city']) ? $postData['billing_city'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Country</label>
                                    <div class="col-lg-9">
                                        <?php
                                        echo $form->field($model, 'billing_country')->dropDownList($countryArr, ['prompt' => 'Select Country', 'value' => (isset($postData['billing_country']) ? $postData['billing_country'] : '')])->label(FALSE)
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Birthdate</label>
                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <!--<input type="text" value="<?php echo $postData['birthdate']; ?>" name="User[birthdate]" class="form-control" id="user-birthdate">-->
                                            <input type="text" value="<?php echo ((isset($postData['birthdate']) && $postData['birthdate'] != '') ? date('d/m/Y', strtotime($postData['birthdate'])) : ''); ?>" name="User[birthdate]" class="form-control" id="user-birthdate">
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $readonly = '';
                                if (isset($_GET['type']) && $_GET['type'] == 'guest_user') {
                                    $readonly = 'readonly="readonly"';
                                }
                                ?>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Password</label>
                                    <div class="col-lg-9">
                                        <div class="form-group field-user-password">
                                            <input type="password" value="" name="User[password]" class="form-control" id="user-password" <?php echo $readonly; ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Confirm Password</label>
                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <input type="password" value="" name="User[confirm_password]" class="form-control" id="user-confirm_password" <?php echo $readonly; ?> >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Status</label>
                                    <div class="col-lg-9">
                                        <?= $form->field($model, 'status')->dropDownList([ '1' => 'Active', '0' => 'Inactive'], ['value' => (isset($postData['status']) ? $postData['status'] : '')])->label(FALSE) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>  
                    </div>
                    <div id="orders" class="tab-pane fade in <?php echo $tabOrder; ?>">
                        <div class="search-bar">
                            <div class="row m-t-sm clearfix">
                                <div class="col-sm-6 m-b-xs ">
                                    <div class="page-counter">
                                        <?php
                                        $userID = isset($_GET['id']) ? $_GET['id'] : '';
                                        $userType = isset($_GET['type']) ? $_GET['type'] : '';
                                        $arr = Yii::$app->request->queryParams;
                                        $url = Url::to(['update', 'id' => $userID, 'type' => $userType, 'fromOrders' => '1'], true);
                                        if (isset($_GET['OrdersSearch'])) {
                                            foreach ($_GET['OrdersSearch'] as $key => $val) {
                                                if (strpos($url, '?') > 0) {
                                                    $url .= '&OrdersSearch[' . $key . ']=' . $val;
                                                } else {
                                                    $url .= '?OrdersSearch[' . $key . ']=' . $val;
                                                }
                                            }
                                        }
                                        $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                        echo Helper::paginationHtml($page, $url);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs text-right">

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <?=
                            GridView::widget([
                                'dataProvider' => $orderDataProvider,
                                'filterModel' => $orderSearchModel,
                                'layout' => "{items}\n{summary}\n{pager}",
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                                'columns' => $OrderColumnArr
                            ]);
                            ?>
                        </div>
                        <div class="table-legends">
                            <div class="icon-box view">
                                <i class="fa fa-eye"></i> View
                            </div>
                        </div>
                    </div>
                    <div id="wishlist" class="tab-pane fade in <?php echo $tabWishlist; ?>">
                        <div class="search-bar">
                            <div class="row m-t-sm clearfix">
                                <div class="col-sm-6 m-b-xs ">
                                    <div class="page-counter">
                                        <?php
                                        $userID = isset($_GET['id']) ? $_GET['id'] : '';
                                        $userType = isset($_GET['type']) ? $_GET['type'] : '';
                                        $arr = Yii::$app->request->queryParams;
                                        $url = Url::to(['update', 'id' => $userID, 'type' => $userType, 'fromWishlist' => '1'], true);
                                        if (isset($_GET['WishlistSearch'])) {
                                            foreach ($_GET['WishlistSearch'] as $key => $val) {
                                                if (strpos($url, '?') > 0) {
                                                    $url .= '&WishlistSearch[' . $key . ']=' . $val;
                                                } else {
                                                    $url .= '?WishlistSearch[' . $key . ']=' . $val;
                                                }
                                            }
                                        }
                                        $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                        echo Helper::paginationHtml($page, $url);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs text-right">

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <?=
                            GridView::widget([
                                'dataProvider' => $wishlistDataProvider,
                                // 'filterModel' => $wishlistSearchModel,
                                'columns' => [
                                    [
                                        'attribute' => 'product_code',
                                        'label' => 'Product ID',
                                        'value' => 'product_code',
                                    ],
                                    [
                                        'label' => 'Thumbnail',
                                        'format' => 'html',
                                        'value' => function ($data) {

                                            return (empty($data["featured_image"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/products/' . $data["featured_image"], ['width' => '70px', 'height' => '60']);
                                        },
                                            ],
                                            [
                                                'attribute' => 'product_name',
                                                'label' => 'Name',
                                                'value' => 'product_name',
                                            ],
                                            [
                                                'attribute' => 'category_name',
                                                'label' => 'Category',
                                                'value' => 'category_name',
                                            ],
                                            [
                                                'attribute' => 'sku',
                                                'label' => 'SKU',
                                                'value' => 'sku',
                                            ],
                                            [
                                                'attribute' => 'display_price',
                                                'label' => 'Price',
                                                'value' => function ($data) {
                                                    return $data['display_currency'] . $data['display_price'];
                                                }
                                            ],
                                            [
                                                'attribute' => 'vendor_code',
                                                'label' => 'Vendor',
                                                'value' => function ($data) {
                                                    return $data['vendor_code'] . '-' . $data['shop_name'];
                                                }
                                            ]
                                        ]
                                    ]);
                                    ?>
                                </div>
                            </div>
                            <div id="shopping_cart" class="tab-pane fade in <?php echo $tabCart; ?>">
                                <div class="search-bar">
                                    <div class="row m-t-sm clearfix">
                                        <div class="col-sm-6 m-b-xs ">
                                            <div class="page-counter">
                                                <?php
                                                $userID = isset($_GET['id']) ? $_GET['id'] : '';
                                                $userType = isset($_GET['type']) ? $_GET['type'] : '';
                                                $arr = Yii::$app->request->queryParams;
                                                $url = Url::to(['update', 'id' => $userID, 'type' => $userType, 'fromShoppingCart' => '1'], true);
                                                if (isset($_GET['ShoppingCartSearch'])) {
                                                    foreach ($_GET['ShoppingCartSearch'] as $key => $val) {
                                                        if (strpos($url, '?') > 0) {
                                                            $url .= '&ShoppingCartSearch[' . $key . ']=' . $val;
                                                        } else {
                                                            $url .= '?ShoppingCartSearch[' . $key . ']=' . $val;
                                                        }
                                                    }
                                                }
                                                $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                                echo Helper::paginationHtml($page, $url);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs text-right">
                                            <h4><b>Total:</b> S$<?php echo (isset($cartTotal['total']) && $cartTotal['total'] != '') ? $cartTotal['total'] : '0'; ?></h4> 
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">

                                    <?=
                                    GridView::widget([
                                        'dataProvider' => $shoppingCartDataProvider,
                                        'columns' => [
                                            [
                                                'attribute' => 'product_code',
                                                'label' => 'Product ID',
                                                'value' => 'product_code',
                                            ],
                                            [
                                                'label' => 'Thumbnail',
                                                'format' => 'html',
                                                'value' => function ($data) {

                                                    return (empty($data["featured_image"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/products/' . $data["featured_image"], ['width' => '70px', 'height' => '60']);
                                                },
                                                    ],
                                                    [
                                                        'attribute' => 'product_name',
                                                        'label' => 'Name',
                                                        'value' => 'product_name',
                                                    ],
                                                    [
                                                        'attribute' => 'category_name',
                                                        'label' => 'Category',
                                                        'value' => 'category_name',
                                                    ],
                                                    [
                                                        'attribute' => 'sku',
                                                        'label' => 'SKU',
                                                        'value' => 'sku',
                                                    ],
                                                    [
                                                        'attribute' => 'display_price',
                                                        'label' => 'Price',
                                                        'value' => function ($data) {
                                                            return $data['display_currency'] . $data['display_price'];
                                                        }
                                                    ],
                                                    [
                                                        'attribute' => 'vendor_code',
                                                        'label' => 'Vendor',
                                                        'value' => function ($data) {
                                                            return $data['vendor_code'] . '-' . $data['shop_name'];
                                                        }
                                                    ]
                                                ]
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">       
                                <?php
                                if (Yii::$app->user->id == '1' || isset($rolePermitions['consumers']) && in_array('edit', $rolePermitions['consumers'])) {
                                    if ($fromOrders <= 0 && $fromShoppingCart <= 0 && $fromWishlist <= 0) {
                                        echo Html::a('Save Consumer', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("customerSave")']);
                                        echo '&nbsp;';
                                    }
                                }
                                echo Html::a('Back', ['user/index'], ['class' => 'btn btn-default']);
                                ?>
    </div>

</section>



