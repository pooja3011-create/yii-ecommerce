<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Vendors;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VendorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendor Payments';
$this->params['breadcrumbs'][] = $this->title;
$helper = new Helper();
$orderStatus = $helper->getOrderStatus();
$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            $class = array('value' => $model['order_id'] . '_' . $model['payment_vendor'], 'id' => $model['order_id'] . '_' . $model['payment_vendor']);
            if ($model['payment_ref_number'] != '') {
                $class = array('value' => $model['order_id'] . '_' . $model['payment_vendor'], 'disabled' => 'disabled', 'id' => $model['order_id'] . '_' . $model['payment_vendor']);
            }
            return $class;
        }
            ],
            [
                'attribute' => 'orderId',
                'label' => 'Order ID',
                'value' => 'order_id'
            ],
            [
                'attribute' => 'order_date',
                'label' => 'Order Date',
                'format' => ['date', 'php:d/m/Y'],
            ],
            [
                'attribute' => 'actual_delivery_date',
                'label' => 'Delivery Date',
                'format' => ['date', 'php:d/m/Y'],
            ],
            [
                'attribute' => 'vendorName',
                'label' => 'Vendor',
                'value' => function ($model) {
                    return $model['vendor_code'] . ' - ' . $model['shop_name'];
                },
            ],
            [
//                            'attribute' => 'amount',
                'label' => 'Total',
                'value' => function ($model) {
                    return 'S$' . $model['order_sum'];
                },
            ],
            [
//                            'attribute' => 'vendorCommission',
                'label' => 'Boucle Commission',
                'value' => function ($model) {
                    return 'S$'.$model['comission'];
                },
            ],
            [
//                            'attribute' => 'vendorPayment',
                'label' => 'Vendor Payment',
                'value' => function ($model) {
                    return 'S$'.$model['vendor_payment'];
                },
            ],
            [
                'attribute' => 'paymentStatus',
                'label' => 'Payment Status',
                'value' => function ($model) {
                    return ($model['payment_ref_number'] != '') ? 'Paid' : 'Pending';
                },
                'filter' => array("paid" => "Paid", "pending" => "Pending"),
            ],
            [
                'attribute' => 'paymentDate',
                'label' => 'Payment Date',
                'value' => 'payment_date',
                'format' => ['date', 'php:d/m/Y'],
            ],
            [
                'attribute' => 'refNumber',
                'value' => 'payment_ref_number',
                'label' => 'Reference Number',
            ],
        ];
        if (Yii::$app->user->id == '1' || (isset($rolePermitions['vendor_payments']) && (in_array('add', $rolePermitions['vendor_payments']) || in_array('view', $rolePermitions['vendor_payments'])))) {
            $columnArr[] = ['class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'contentOptions' => ['class' => 'action'],
                'template' => '{pay} {update}',
                'buttons' => [
                    'pay' => function ($url, $model, $key) {
                        if ($model['payment_ref_number'] != '') {
                            return '';
                        } else {
                            $rolePermitions = Helper::getRolePermission();
                            if (Yii::$app->user->id == '1' || isset($rolePermitions['vendor_payments']) && in_array('add', $rolePermitions['vendor_payments'])) {
                                return Html::a('<span class="fa fa-dollar"></span>', 'javascript:;', ['onclick' => 'showDialog("' . URL::to(['/vendors/payment-info'], true) . '","' . $model['order_id'] . '_' . $model['payment_vendor'] . '")']);
                            } else {
                                return '';
                            }
                        }
                    },
                            'update' => function ($url, $model, $key) {
                        $rolePermitions = Helper::getRolePermission();
                        if (Yii::$app->user->id == '1' || isset($rolePermitions['vendor_payments']) && in_array('view', $rolePermitions['vendor_payments'])) {
                            return Html::a('<span class="fa fa-eye"></span>', ['payment-detail', 'id' => $model['order_id'], 'vendor_id' => $model['payment_vendor']]);
                        }
                    }
                        ],
                    ];
                }
                ?>
                <!-- .vbox -->
                <section class="panel">
                    <?php if (Yii::$app->user->id == '1' || isset($rolePermitions['vendor_payments']) && in_array('add', $rolePermitions['vendor_payments'])) {
                        ?>
                        <div class="action-row custom-border">
                            <div class="row m-t-sm clearfix">
                                <div class="col-sm-6 m-b-xs ">
                                    <select class="form-control input-s inline" id="drpVendorPayment" name="productAction">
                                        <option value="">Actions</option>
                                        <option value="1">Add Payment Info</option>
                                    </select>
                                    <button class="btn btn-primary" onclick="showDialog('<?php echo URL::to(['/vendors/payment-info'], true); ?>', '')">Apply</button>  
                                </div>
                                <div class="col-sm-6 m-b-xs ">
                                </div>
                            </div>
                        </div><?php
                }
                    ?>
                    <div class="search-bar">
                        <div class="row m-t-sm clearfix">
                            <div class="col-sm-6 m-b-xs ">
                                <div class="page-counter">
                                    <?php
                                    $arr = Yii::$app->request->queryParams;
                                    $url = Url::to(['payments'], true);
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
                                            'action' => ['payments'],
                                            'method' => 'get',
                                ]);
                                ?><div class="input-group">
                                <?php /*                                 * <input type="text" placeholder="search by order id and reference number" size="100" value="<?php echo isset($_GET['OrdersSearch']['searchOrder']) ? $_GET['OrdersSearch']['searchOrder'] : '' ?>" name="OrdersSearch[searchOrder]" class="input-sm form-control">* */ ?>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'layout' => "{items}\n{summary}\n{pager}",
                            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                            'columns' => $columnArr,
                        ]);
                        ?>
    </div>
    <div class="table-legends">
        <div class="icon-box view">
            <i class="fa fa-dollar"></i> Add Payment Info
        </div>
        <div class="icon-box view">
            <i class="fa fa-eye"></i> View
        </div>

    </div>
</section>
<!-- /.vbox -->
