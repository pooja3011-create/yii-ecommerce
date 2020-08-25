<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Helper;

$rolePermitions = Helper::getRolePermission();
$orderShipmentStatus = Helper::getOrderShipmentStatus();
$tabOrder = '';
$tabShipment = '';
if ($fromShipment > 0) {
    $this->title = 'Order Shipment';
    $tabShipment = 'active';
} else {
    $this->title = 'View Order - ' . $orderArr['id'];
    $tabOrder = 'active';
}

$tab2 = '';
if ($fromVendor > 0) {
    $tab2 = 'fromVendor';
} else if ($fromUser > 0) {
    $tab2 = 'fromUser';
}

$shipmentColumnArr = [
    [
        'label' => 'Shipped By',
        'value' => 'shipment_from'
    ],
    [
        'label' => 'Recipient',
        'value' => 'shipment_to'
    ],
    [
        'label' => 'Carrier',
        'value' => 'carrier'
    ],
    [
        'label' => 'Tracking #',
        'value' => 'traking_number'
    ],
    [
        'label' => 'Date Shipped',
        'value' => 'shipped_date',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'label' => 'Date Delivered',
        'value' => 'delivered_date',
        'format' => ['date', 'php:d/m/Y'],
    ],
];

$shipmentColumnArr[] = ['class' => 'yii\grid\ActionColumn',
    'header' => 'Action',
    'contentOptions' => ['class' => 'action'],
    'template' => '{view} {update}',
    'buttons' => ['view' => function ($url, $model, $key) {
            if ($model['shipment_status'] == '1') {
                return Html::a('<span class="fa fa-eye"></span>', ['shipment-update', 'id' => $model['traking_number'], 'order_id' => $model['order_id'], 'shippedBy' => ($model['shipment_from'] == 'Administrator') ? 'Administrator' : 'vendor'], ['title' => 'View']);
            }
        }, 'update' => function ($url, $model, $key) {
            $rolePermitions = Helper::getRolePermission();
            if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('edit', $rolePermitions['orders'])) {
                if ($model['shipment_status'] != '1') {
                    return Html::a('<span class="fa fa-pencil"></span>', ['shipment-update', 'id' => $model['traking_number'], 'order_id' => $model['order_id'], 'shippedBy' => ($model['shipment_from'] == 'Administrator') ? 'Administrator' : 'vendor'], ['title' => 'Edit']);
                }
            }
        }
            ],
        ];
        ?>
        <section class="panel">
            <header class="panel-heading bg-light">
                <ul class="nav nav-tabs nav-justified">
                    <li id="orders" class="<?php echo $tabOrder; ?>"><a data-toggle="tab" href="#orders" aria-controls="orders" role="tab" data-toggle="tab" onclick="openTab('', '<?php echo $tab2; ?>');">Order Information</a></li>   
                    <li class="<?php echo $tabShipment; ?>" id="shipment"><a data-toggle="tab" href="#shipment" aria-controls="shipment" role="tab" data-toggle="tab" onclick="openTab('fromShipment', '<?php echo $tab2; ?>');">Shipments</a></li>
                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="orders" class="tab-pane fade in <?php echo $tabOrder; ?>"> 
                        <div class="">
                            <div class="order-main">
                                <h4>Order & Account Information</h4>
                                <hr>
                                <div class="col-lg-6"> 
                                    <div class="order-sub">
                                        <label>Order ID</label><span class="pull-right"><?php echo $orderArr['id']; ?></span><br>
                                        <label>Order Date</label><span class="pull-right"><?php echo date('d/m/Y H:i A', strtotime($orderArr['order_date'])); ?></span><br>
                                        <label>Order Status</label><span class="pull-right"><?php echo $orderStatus[$orderArr['status']]; ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-6"> 
                                    <div class="order-sub">
                                        <label>Consumer Name</label><span class="pull-right"><?php echo $orderArr['first_name']; ?></span><br>
                                        <label>Email</label><span class="pull-right"><?php echo $orderArr['email']; ?></span><br>
                                        <label>Phone Number</label><span class="pull-right"><?php echo $orderArr['phone']; ?></span><br>
                                        <label>Consumer Type</label><span class="pull-right"><?php echo ($orderArr['guest_checkout'] == '1') ? 'Guest User' : 'Registered User'; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="order-main">
                                <h4>Address Information</h4>
                                <hr>
                                <div class="col-lg-6"> 
                                    <div class="order-sub">
                                        <label>Billing Address</label><br>
                                        <?php
                                        echo $orderArr['first_name'] . '<br>';
                                        $address = $orderArr['billing_address1'];

                                        $address .= ($orderArr['billing_address2'] != '') ? ', ' . $orderArr['billing_address2'] : $orderArr['billing_address2'];
                                        $address .= ($orderArr['billing_city'] != '') ? ', ' . $orderArr['billing_city'] : $orderArr['billing_city'];
                                        $address .= ($orderArr['billing_country_name'] != '') ? ', ' . $orderArr['billing_country_name'] : $orderArr['billing_country_name'];
                                        $address .= ($orderArr['billing_zip'] != '') ? '<br>' . $orderArr['billing_zip'] : $orderArr['billing_zip'];
                                        $address .= ($orderArr['billing_phone'] != '') ? '<br>Tel.: ' . $orderArr['billing_phone'] : $orderArr['billing_phone'];
                                        echo $address;
                                        ?>

                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="order-sub">
                                        <label>Shipping Address</label><br>
                                        <?php
                                        echo $orderArr['first_name'] . '<br>';
                                        $address = $orderArr['shipping_address1'];

                                        $address .= ($orderArr['shipping_address2'] != '') ? ', ' . $orderArr['shipping_address2'] : $orderArr['shipping_address2'];
                                        $address .= ($orderArr['shipping_city'] != '') ? ', ' . $orderArr['shipping_city'] : $orderArr['shipping_city'];
                                        $address .= ($orderArr['shipping_country_name'] != '') ? ', ' . $orderArr['shipping_country_name'] : $orderArr['shipping_country_name'];
                                        $address .= ($orderArr['shipping_zip'] != '') ? '<br>' . $orderArr['shipping_zip'] : $orderArr['shipping_zip'];
                                        $address .= ($orderArr['shipping_phone'] != '') ? '<br>Tel.: ' . $orderArr['shipping_phone'] : $orderArr['shipping_phone'];
                                        echo $address;
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="order-main">
                                <h4>Payment & Shipping Method</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <div class="order-sub">
                                        <label>Payment Method</label><br>
                                        <?php echo $orderArr['payment_method']; ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="order-sub">
                                        <label>Shipping Method</label><br>
                                        <?php echo $orderArr['shipping_method'] . ' - S$' . number_format($config['shopping_rate'], 2) . ' (' . $config['estimated_delivery_days'] . ')'; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                            <div class="order-main">
                                <h4>Items Ordered</h4>
                                <hr>
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <div class="grid-view" id="w1">
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Vendor</th>
                                                        <th>Vendor Ship-by</th>
                                                        <th>Item Status</th>
                                                        <th>Tracking Number</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th>Subtotal</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $completeOrder = '1';
                                                    $totalAmount = 0;
                                                    $editOrder = '1';
                                                    $cancelProducts = array();
                                                    if (isset($orderArr['products']) && !empty($orderArr['products'])) {
                                                        foreach ($orderArr['products'] as $product) {
                                                            if ($product['shipment_status'] != '3' && $product['shipment_status'] != '4' && $product['shipment_status'] != '7') {
                                                                $completeOrder = '0';
                                                            }
                                                            if($product['shipment_status'] == '4'){
                                                               $editOrder = '0'; 
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $product['product_name']; ?></td>
                                                                <td><?php echo $product['vendor_name']; ?></td>
                                                                <td><?php echo date('d/m/Y', strtotime($orderArr['order_date'] . '+' . $config['vendor_shipping_deadline'] . ' hour')); ?></td>
                                                                <td><?php echo $orderShipmentStatus[$product['shipment_status']]; ?></td>
                                                                <td><?php echo ($product['traking_number'] != '') ? $product['traking_number'] : '--'; ?></td>
                                                                <td>S$<?php echo $product['product_price']; ?></td>
                                                                <td><?php echo $product['order_qty']; ?></td>
                                                                <td>S$<?php
                                                                    $price = $product['product_price'] * $product['order_qty'];
//                                                                    $totalAmount = $totalAmount + $price;
                                                                    echo $price;
                                                                    ?>
                                                                </td>
                                                                <td><?php
                                                                    if (($product['shipment_history'] != '')) {
                                                                        ?><a data-toggle="ajaxModal" href="<?php echo URL::to(['/orders/shipment-history', 'order_id' => $orderArr['id'], 'product_id' => $product['product_id']], true); ?>">View Shipment History</a><?php
                                                                    }
                                                                    ?></td>
                                                            </tr>
                                                            <?php
                                                            if ($product['shipment_status'] == '6' || $product['shipment_status'] == '7' || $product['shipment_status'] == '8') {
                                                               $cancelProducts[] = array('name' => $product['product_name'], 'price' => $price, 'status' => $product['shipment_status'], 'note' => $product['order_cancel_note']);
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>                                 
                                    </div>
                                </div>
                            </div>

                            <div class="order-main">
                                <h4>Order Total</h4>
                                <hr>
                                <div class="col-lg-6">
                                    <?php
                                    if (!empty($cancelProducts)) {
                                        ?>
                                        <label>Returned - Cancelled Items</label><br>

                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($cancelProducts as $data) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $data['name'];?></td>
                                                        <td>S$<?php echo $data['price']; ?></td>
                                                    </tr><?php
                                                    if (($data['status'] == '7' || $data['status'] == '6') && $data['note'] != '') {
                                                        ?><tr>
                                                            <td colspan="2"><strong>Note: </strong><?php echo $data['note'] ; ?></td>
                                                        </tr><?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table> 
                                        <?php
                                    }
                                    ?>

                                </div>
                                <div class="col-lg-6">
                                    <label>Subtotal</label><span class="pull-right">S$<?php
                                        $totalAmount = $orderArr['subtotal'];
                                        echo number_format($totalAmount, 2);
                                        ?></span><br>
                                    <label>Shipping & Handling</label><span class="pull-right">S$<?php echo number_format($orderArr['shipping_rate'], 2); ?></span><br>
                                    <label>Tax(<?php echo $orderArr['tax_rate']; ?>%)</label>
                                    <span class="pull-right">
                                        <?php
                                        $tax = $orderArr['tax_paid'];
                                        echo 'S$' . number_format($tax, 2);
                                        ?>
                                    </span><br>
                                    <br>
                                    <label>Subtotal</label><span class="pull-right">S$<?php
                                        $subtotal = $totalAmount + ($orderArr['shipping_rate'] + $tax);
                                        echo number_format($subtotal, 2);
                                        ?></span><br>
                                    <label>Less: Coupon Code Discount(<?php echo ($orderArr['promocode'] != '') ? $orderArr['promocode'] : '-'; ?>)</label><span class="pull-right">S$<?php
                                        $discount = $orderArr['coupon_discount'];
                                        echo number_format($discount, 2);
                                        ?></span><br><br>
                                    <label>Total Paid</label><span class="pull-right">S$<?php echo number_format($orderArr['total_paid'], 2); ?></span><br>
                                </div>    
                                <div  class="col-lg-6"> </div>
                                <div  class="col-lg-6"> 
                                    <br><br>
                                    <label>Returns - Cancellations</label><br>
                                    <label>Returned Items Total</label><span class="pull-right">S$<?php echo number_format($orderArr['return_cancel_amount'], 2); ?></span><br>
                                    <label>Returned Tax Total</label><span class="pull-right">S$<?php echo number_format($orderArr['return_cancel_tax_amount'] , 2); ?></span><br><br>
                                     <label>Returns Subtotal</label><span class="pull-right">S$<?php echo number_format(($orderArr['return_cancel_tax_amount'] + $orderArr['return_cancel_amount']), 2); ?></span><br><br>
                                    <label>Net Amount</label><span class="pull-right">S$<?php echo number_format($orderArr['amount'], 2); ?></span><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="shipment" class="tab-pane fade in <?php echo $tabShipment; ?>"> 
                        <div class="search-bar">
                            <div class="row m-t-sm clearfix">
                                <div class="col-sm-6 m-b-xs ">
                                    <div class="page-counter">
                                        <?php
                                        $id = isset($_GET['id']) ? $_GET['id'] : '';
                                        $arr = Yii::$app->request->queryParams;
                                        $url = Url::to(['order-detail', 'id' => $id, 'fromShipment' => '1'], true);
                                        if (isset($_GET['OrderProductsSearch'])) {
                                            foreach ($_GET['OrderProductsSearch'] as $key => $val) {
                                                if (strpos($url, '?') > 0) {
                                                    $url .= '&OrderProductsSearch[' . $key . ']=' . $val;
                                                } else {
                                                    $url .= '?OrderProductsSearch[' . $key . ']=' . $val;
                                                }
                                            }
                                        }
                                        $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                        echo Helper::paginationHtml($page, $url);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs text-right">
                                    <?php
                                    if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('edit', $rolePermitions['orders'])) {

                                        if ($orderArr['traking_number'] == '') {
                                            echo Html::a('Add Shipment', ['orders/add-shipment', 'id' => $orderArr['id']], ['class' => 'btn btn-primary']);
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <?=
                            GridView::widget([
                                'dataProvider' => $shipmentDataProvider,
//                        'filterModel' => $shipmentSearchModel,
                                'layout' => "{items}\n{summary}\n{pager}",
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                                'columns' => $shipmentColumnArr,
                            ]);
                            ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">       
                <?php
                if ($tabOrder != '') {
                    if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('edit', $rolePermitions['orders'])) {
                        if ($orderArr['status'] != '4' && $orderArr['status'] != '1') {
                            echo Html::a('Cancel Order', ['orders/cancel-order', 'id' => $orderArr['id']], ['class' => 'btn btn-primary', 'onclick' => "javascript: return confirm('Are you sure you want to mark this order as cancelled? Please note, this opertation can\'t be reversed.');"]);
                            echo '&nbsp;';
                        }

                        if ($orderArr['status'] != '4' && $orderArr['status'] != '1' && $completeOrder != '0') {

                            echo Html::a('Mark as Completed', ['orders/complete-order', 'id' => $orderArr['id']], ['class' => 'btn btn-primary', 'onclick' => "javascript: return confirm('".Yii::$app->params['compleateOrderConf']."');"]);
                            echo '&nbsp;';
                        }

                        if ($orderArr['status'] != '4' && $orderArr['status'] != '1') {
                            echo Html::a('Send Email', ['orders/send-order-confirmation', 'id' => $orderArr['id']], ['class' => 'btn btn-primary']);
                            echo '&nbsp;';
                        }
                        if ($orderArr['status'] != '1' && $editOrder != '0') {
                            echo Html::a('Edit Order', ['orders/update', 'id' => $orderArr['id']], ['class' => 'btn btn-primary']);
                            echo '&nbsp;';
                        }
                    }
                    
                    echo Html::a('Invoice', ['orders/download-invoice', 'id' => $orderArr['id']], ['class' => 'btn btn-primary','target'=>'_blank']);
                            echo '&nbsp;';
                }
                if ($fromVendor > 0) {
                    echo Html::a('Back', ['vendors/update', 'id' => $fromVendor, 'fromOrders' => 1], ['class' => 'btn btn-default']);
                } else if ($fromUser > 0) {
                    echo Html::a('Back', ['user/update', 'id' => $fromUser, 'type' => (($orderArr['guest_checkout'] == '1') ? 'guest_user' : 'registered'), 'fromOrders' => 1], ['class' => 'btn btn-default']);
                } else {
                    echo Html::a('Back', ['orders/index'], ['class' => 'btn btn-default']);
                }
                ?>
    </div>
</section>
<style>
    .order-sub{
        background: #f3f3f3 none repeat scroll 0 0;        
        padding: 20px 20px 20px 10px;
    }
    .order-main{
        margin-top: 20px;
    }
</style>
