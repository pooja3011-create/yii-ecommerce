<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Helper;
use dosamigos\datepicker\DatePicker;
use app\models\Products;

/* @var $this yii\web\View */
/* @var $model app\models\Vendors */
$tabAccount = '';
$tabShop = '';
$tabBank = '';
$tabProduct = '';
$tabOrders = '';
if ($fromShop > 0) {
    $tabShop = 'active';
} else if ($fromAccount > 0) {
    $tabBank = 'active';
} else if ($fromProducts > 0) {
    $tabProduct = 'active';
} else if ($fromOrders > 0) {
    $tabOrders = 'active';
} else {
    $tabAccount = 'active';
}

$helper = new Helper();
$orderStatus = $helper->getOrderStatus();
$this->title = 'Vendor - ' . $model->name;
;
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add/Edit Vendors';
unset($model->password);

$rolePermitions = Helper::getRolePermission();
$categoryArr = Products::getProductCategory();
$columnArr = [

    'product_code',
    [
        'label' => 'Image',
        'format' => 'html',
        'value' => function ($data) {
            return (empty($data["featured_image"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/products/' . $data["featured_image"], ['width' => '70px', 'height' => '60']);
        },
            ],
            [
                'attribute' => 'name',
                'label' => 'Name',
                'value' => 'name',
            ],
            [
                'attribute' => 'category_name',
                'label' => 'Category',
                'value' => 'category.name',
                'filter' => Html::DropDownList('ProductsSearch[category_id]', $productSearchModel->category_id, $categoryArr, array('class' => 'form-control', 'prompt' => 'View All')),
            ],
            [
                'attribute' => 'sku',
                'label' => 'SKU',
                'value' => 'sku',
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'contentOptions' => function ($productSearchModel) {
                    if ($productSearchModel->status == 0) {
                        return array('class' => 'status yellow');
                    } else if ($productSearchModel->status == 1) {
                        return array('class' => 'status green');
                    } else if ($productSearchModel->status == 2) {
                        return array('class' => 'status red');
                    } else if ($productSearchModel->status == 3) {
                        return array('class' => 'status green');
                    } else {
                        return array('class' => 'status gray');
                    }
                },
                        'value' => function ($productSearchModel) {
                    if ($productSearchModel->status == 0) {
                        return '<span>Pending</span>';
                    } else if ($productSearchModel->status == 1) {
                        return '<span>Approved</span>';
                    } else if ($productSearchModel->status == 2) {
                        return '<span>Disapproved</span>';
                    } else if ($productSearchModel->status == 3) {
                        return '<span>Active</span>';
                    } else {
                        return '<span>Inactive</span>';
                    }
                },
                        'filter' => Html::DropDownList('ProductsSearch[status]', $productSearchModel->status, array('0' => 'Pending', "1" => "Approved", "2" => "Disapproved", "3" => 'Active', "4" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
                    ],
                ];
                if (Yii::$app->user->id == '1' || (isset($rolePermitions['products']) && in_array('view', $rolePermitions['products']))) {
                    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'contentOptions' => ['class' => 'action'],
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                $rolePermitions = Helper::getRolePermission();
                                if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('view', $rolePermitions['products'])) {
                                    return Html::a('<span class="fa fa-eye"></span>', ['products/update', 'id' => $model->id, 'fromVendor' => 1], ['title' => 'View']);
                                }
                            }
                                ],
                            ];
                        }
                        $OrderColumnArr = [

                            [
                                'options' => ['style' => 'width:7%;'],
                                'attribute' => 'id',
                                'label' => 'Order ID',
                            ],
                            [
                                'attribute' => 'order_date',
                                'label' => 'Order Date',
                                'format' => ['date', 'php:d/m/Y h:i A'],
                            ],
                            [
                                'attribute' => 'userName',
                                'label' => 'Consumer',
                                'value' => function ($model) {
                                    return $model->user->getUserName();
                                },
                            ],
                            [
                                'attribute' => 'userPhone',
                                'label' => 'Consumer Phone',
                                'value' => function ($model) {
                                    return $model->user->getPhone();
                                },
                            ],
                            [
                                'attribute' => 'estimate_delivery_date',
                                'label' => 'Est. Delivery Date',
                                'format' => ['date', 'php:d/m/Y'],
                            ],
                            [
                                'attribute' => 'updated_date',
                                'label' => 'Last Updated',
                                'format' => ['date', 'php:d/m/Y h:i A'],
                            ],
                            [
                                'attribute' => 'guest_checkout',
                                'label' => 'Guest Checkout?',
                                'value' => function ($searchModel) {
                                    return $searchModel->guest_checkout == 1 ? 'Yes' : 'No';
                                },
                                'filter' => Html::DropDownList('OrdersSearch[guest_checkout]', $orderSearchModel->guest_checkout, array("1" => "Yes", "0" => "No"), array('class' => 'form-control', 'prompt' => 'View All')),
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function ($searchModel) {

                                    $orderStatus = Helper::getOrderStatus();
                                    $status = $orderStatus[$searchModel->status];
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
                                        $vendor_id = $_GET['id'];
                                        return Html::a('<span class="fa fa-eye"></span>', ['orders/order-detail', 'id' => $model->id, 'fromVendor' => $vendor_id], ['title' => 'View']);
                                    }
                                        ],
                                    ];
                                }
                                ?>

                                <section class="panel">    
                                    <header class="panel-heading bg-light">
                                        <ul class="nav nav-tabs nav-justified">
                                            <li class="<?php echo $tabAccount; ?>" id="accountInfo"><a data-toggle="tab" href="#account_info" aria-controls="account_info" role="tab" data-toggle="tab" onclick="openTab('', '');">Account Info</a></li>
                                            <li id="shopInfo" class="<?php echo $tabShop; ?>"><a data-toggle="tab" href="#shop_info" aria-controls="shop_info" role="tab" data-toggle="tab" onclick="openTab('fromShop', '');">Shop info</a></li>
                                            <li id="bankInfo" class="<?php echo $tabBank; ?>"><a data-toggle="tab" href="#bank_info" aria-controls="bank_info" role="tab" data-toggle="tab" onclick="openTab('fromAccount', '');">Bank account info</a></li>
                                            <?php if (Yii::$app->user->id == '1') {
                                                ?>
                                                <li id="products" class="<?php echo $tabProduct; ?>"><a data-toggle="tab" href="#products" aria-controls="products" role="tab" data-toggle="tab" onclick="openTab('fromProducts', '');">Products</a></li>
                                                <li id="orders" class="<?php echo $tabOrders; ?>"><a data-toggle="tab" href="#orders" aria-controls="orders" role="tab" data-toggle="tab" onclick="openTab('fromOrders', '');">Orders</a></li>
                                                <?php
                                            } else {
                                                if (isset($rolePermitions['products']) && in_array('list', $rolePermitions['products'])) {
                                                    ?><li id="products" class="<?php echo $tabProduct; ?>"><a data-toggle="tab" href="#products" aria-controls="products" role="tab" data-toggle="tab" onclick="openTab('fromProducts', '');">Products</a></li><?php
                                                    }
                                                    if (isset($rolePermitions['orders']) && in_array('list', $rolePermitions['orders'])) {
                                                        ?>
                                                    <li id="orders" class="<?php echo $tabOrders; ?>"><a data-toggle="tab" href="#orders" aria-controls="orders" role="tab" data-toggle="tab" onclick="openTab('fromOrders', '');">Orders</a></li>
                                                    <?php
                                                }
                                            }
                                            ?>

                                        </ul>
                                    </header>
                                    <div class="panel-body">
                                        <div class="tab-content">
                                            <div id="account_info" class="tab-pane fade in <?php echo $tabAccount; ?>">
                                                <div class="row cleafix">
                                                    <div class="col-sm-7">
                                                        <?php
                                                        $form = ActiveForm::begin([
                                                                    'options' => [
                                                                        'name' => 'VendorUpdate',
                                                                        'id' => 'VendorUpdate'
                                                                    ]
                                                        ]);
                                                        ?>
                                                        <input type="hidden" name="save" class="pageSave"/>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Vendor Code</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'vendor_code')->textInput(['maxlength' => true, 'readonly' => 'readonly'])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Full Name</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Email</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Phone Number</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'onkeypress' => 'return isPhone(event);'])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Gender</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'gender')->dropDownList([ 'male' => 'Male', 'female' => 'Female',])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Password</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Confirm Password</label>
                                                            <div class="col-lg-8 form-group">
                                                                <input type="password" aria-required="true" maxlength="255" name="Vendors[password_repeat]" class="form-control" id="vendors-password_repeat">
                                                            </div>
                                                        </div>                                 
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Status</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'status')->dropDownList([ '1' => 'Active', '0' => 'Inactive'])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        ActiveForm::end();
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <div id="shop_info" class="tab-pane fade in <?php echo $tabShop; ?>">
                                                <?php
                                                $form = ActiveForm::begin([
                                                            'options' => [
                                                                'name' => 'VendorUpdate1',
                                                                'id' => 'VendorUpdate1'
                                                            ]
                                                ]);
                                                ?>
                                                <input type="hidden" name="save" class="pageSave"/>
                                                <div class="col-lg-7">
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Shop Name</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'shop_name')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Address Line 1</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'address1')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Address Line 2</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'address2')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">City</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'city')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">State</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'state')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Country</label>
                                                        <div class="col-lg-9">
                                                            <?php
                                                            echo $form->field($model, 'country_id')->dropDownList(
                                                                    $listData, ['prompt' => 'Select Country'])->label(FALSE)
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Short Description</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'shop_description')->textarea(['rows' => 6])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Tax/Vat Number</label>
                                                        <div class="col-lg-9">
                                                            <?= $form->field($model, 'tax_vat_number')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class=" col-lg-3 control-label">Vendor Commission</label>
                                                        <div class="col-lg-9">
                                                            <div class="col-lg-6" style="padding-left: 0px;">
                                                                <?= $form->field($model, 'commission_type')->dropDownList(['percentage' => 'Percentage', 'amount' => 'Fixed Amount'])->label(FALSE) ?>
                                                            </div>
                                                            <div class="col-lg-6"><?= $form->field($model, 'commission_rate')->textInput(['maxlength' => '10', 'placeholder' => 'Commission Rate'])->label(FALSE) ?></div>
                                                            <!--<p>This will override the default commission rate set in the configure.</p>-->
                                                        </div>
                                                        <div class="clearfix"></div>

                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Product Approval</label>
                                                        <div class="col-lg-9"  style="margin-bottom: 10px;">
                                                            <?= $form->field($model, 'product_approval')->dropDownList(['required' => 'Required', 'not required' => 'Not Required'])->label(FALSE) ?>
                                                            <p>Select not required to bypass product approval process for this vendor</p>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Shop Banner Image</label>
                                                        <div class="col-lg-9" style="margin-bottom: 10px;">

                                                            <div class="col-lg-7"><?= $form->field($model, 'shop_banner_image')->fileInput(['maxlength' => true])->label(FALSE) ?>
                                                                <p>Recommended dimensions: 700*300 px</p></div>
                                                            <div class="col-lg-5"><?php
                                                                if ($model->shop_banner_image != '') {
                                                                    echo Html::img('@web/images/vendors/' . $model->shop_banner_image, ['height' => '50', 'width' => '50']);
                                                                    ?> <a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $model->id; ?>" data-myval="<?php echo $model->id; ?>" data-image="shop_banner_image">
                                                                        Remove</a><?php
                                                                }
                                                                ?></div>

                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-lg-3 control-label">Shop Logo Image</label>
                                                        <div class="col-lg-9" style="margin-bottom: 10px;">
                                                            <div class="col-lg-7"> <?= $form->field($model, 'shop_logo_image')->fileInput(['maxlength' => true])->label(FALSE) ?>
                                                                <p>Recommended dimensions: 400*400 px</p></div>
                                                            <div class="col-lg-5"> <?php
                                                                if ($model->shop_logo_image != '') {
                                                                    echo Html::img('@web/images/vendors/' . $model->shop_logo_image, ['height' => '50', 'width' => '50']);
                                                                    if (Yii::$app->user->id == '1' || isset($rolePermitions['vendors']) && in_array('edit', $rolePermitions['vendors'])) {
                                                                        ?> <a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $model->id; ?>" data-myval="<?php echo $model->id; ?>" data-image="shop_logo_image">
                                                                            Remove</a><?php
                                                                    }
                                                                }
                                                                ?></div>


                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <h4>Select Applicable Categories</h4>
                                                    <label for="category[]" class="error" style="display: none;">Please select category.</label>
                                                    <ul class="checkbox">
                                                        <?php
                                                        foreach ($categoryTree as $r) {
                                                            echo $r;
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                                <?php
                                                ActiveForm::end();
                                                ?>
                                            </div>
                                            <div id="bank_info" class="tab-pane fade in <?php echo $tabBank; ?>">
                                                <div class="row cleafix">
                                                    <div class="col-sm-7">
                                                        <?php
                                                        $form = ActiveForm::begin([
                                                                    'options' => [
                                                                        'name' => 'VendorUpdate2',
                                                                        'id' => 'VendorUpdate2'
                                                                    ]
                                                        ]);
                                                        ?>
                                                        <input type="hidden" name="save" class="pageSave"/>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Bank Name</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Account Number</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'account_number')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Account Holder Name</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'account_holder_name')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Swift Code</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'swift_code')->textInput(['maxlength' => true])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Account Notes</label>
                                                            <div class="col-lg-8">
                                                                <?= $form->field($model, 'account_notes')->textarea(['rows' => 6])->label(FALSE) ?>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        ActiveForm::end();
                                                        ?>
                                                    </div>
                                                </div>

                                            </div>
                                            <div id="products" class="tab-pane fade in <?php echo $tabProduct; ?>">
                                                <div class="search-bar">
                                                    <div class="row m-t-sm clearfix">
                                                        <div class="col-sm-6 m-b-xs ">
                                                            <div class="page-counter">
                                                                <?php
                                                                $arr = Yii::$app->request->queryParams;
                                                                $url = Url::to(['update', 'id' => $model->id, 'fromProducts' => '1'], true);
                                                                if (isset($_GET['ProductsSearch'])) {
                                                                    foreach ($_GET['ProductsSearch'] as $key => $val) {
                                                                        if (strpos($url, '?') > 0) {
                                                                            $url .= '&ProductsSearch[' . $key . ']=' . $val;
                                                                        } else {
                                                                            $url .= '?ProductsSearch[' . $key . ']=' . $val;
                                                                        }
                                                                    }
                                                                }
                                                                $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                                                echo Helper::paginationHtml($page, $url);
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 m-b-xs">
                                                            <?php
                                                            $form = ActiveForm::begin([
                                                                        'action' => ['update', 'id' => $model->id, 'fromProducts' => $fromProducts],
                                                                        'method' => 'get',
                                                            ]);
                                                            ?>
                                                            <div class="input-group">
                                                                <?php /** <input type="text" placeholder="search by name" size="100" value="<?php echo isset($_GET['ProductsSearch']['searchAll']) ? $_GET['ProductsSearch']['searchAll'] : '' ?>" name="ProductsSearch[searchAll]"  class="input-sm form-control"> */ ?>
                                                            </div>
                                                            <?php ActiveForm::end(); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">
                                                    <?=
                                                    GridView::widget([
                                                        'dataProvider' => $productDataProvider,
                                                        'filterModel' => $productSearchModel,
                                                        'layout' => "{items}\n{summary}\n{pager}",
                                                        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                                                        'columns' => $columnArr,
                                                    ]);
                                                    ?>
                                                </div>


                                            </div>
                                            <div id="orders" class="tab-pane fade in <?php echo $tabOrders; ?>">
                                                <div class="search-bar">
                                                    <div class="row m-t-sm clearfix">
                                                        <div class="col-sm-6 m-b-xs ">
                                                            <div class="page-counter">
                                                                <?php
                                                                $arr = Yii::$app->request->queryParams;
                                                                $url = Url::to(['update', 'id' => $model->id, 'fromOrders' => '1'], true);
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
                                                        <div class="col-sm-6 m-b-xs">
                                                            <?php
                                                            $form = ActiveForm::begin([
                                                                        'action' => ['update', 'id' => $model->id, 'fromOrders' => $fromOrders],
                                                                        'method' => 'get',
                                                            ]);
                                                            ?>
                                                            <div class="input-group">
                                                                <?php /** <input type="text" placeholder="search by id" size="100" value="<?php echo isset($_GET['OrdersSearch']['searchAll']) ? $_GET['OrdersSearch']['searchAll'] : '' ?>" name="OrdersSearch[searchAll]"  class="input-sm form-control"> */ ?>
                                                            </div>
                                                            <?php ActiveForm::end(); ?>
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
                                                        'columns' => $OrderColumnArr,
                                                    ]);
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
                                        <?php
                                        $form = 'VendorUpdate';
                                        if ($fromShop > 0) {
                                            $form = 'VendorUpdate1';
                                        }
                                        if ($fromAccount > 0) {
                                            $form = 'VendorUpdate2';
                                        }
                                        if ($fromProducts != 1 && $fromOrders != 1) {
                                            if (Yii::$app->user->id == '1' || isset($rolePermitions['vendors']) && in_array('edit', $rolePermitions['vendors'])) {
                                                echo Html::a('Save and Continue', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("' . $form . '","0")']);
                                                echo '&nbsp;';
                                                echo Html::a('Save Vendor', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("' . $form . '","1")']);
                                            }
                                        }
                                        ?>
                                        <?php echo Html::a('Back', ['vendors/index'], ['class' => 'btn btn-default']); ?>
    </div>  
</section>
