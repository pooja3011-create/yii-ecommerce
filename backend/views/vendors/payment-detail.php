<?php

namespace app\models;

use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

$this->title = 'Payment Information';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="panel">
    <div class="panel-body">
        <div class="col-sm-7">
            <div class="form-group">
                <label class="col-lg-12 control-label"><h4>Order Information</h4></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Order ID</label>
                <label class="col-lg-8 control-label"><a href="<?php echo Url::to(['orders/order-detail','id'=> $data['order_id']]); ?>" target="_blank" style="cursor: pointer;text-decoration: underline;"><?php echo $data['order_id']; ?></a></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Vendor</label>
                <label class="col-lg-8 control-label"><?php echo $data['vendor_name']; ?></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Order Date</label>
                <label class="col-lg-8 control-label"><?php echo date("d/m/y", strtotime($data['order_date'])); ?></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Delivery Date</label>
                <label class="col-lg-8 control-label">
                    <?php
                    if ($data['actual_delivery_date'] != "") {
                        echo date("d/m/y", strtotime($data['actual_delivery_date']));
                    } else {
                        echo "&nbsp;";
                    }
                    ?>
                </label>
            </div>
            <div class="form-group">&nbsp;</div>

            <div class="form-group">
                <label class="col-lg-12 control-label"><h4>Payment Information</h4></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Order Total</label>
                <label class="col-lg-8 control-label"><?php echo "S$" . $data['order_sum']; ?></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Boucle Commission</label>
                <label class="col-lg-8 control-label"><?php
                    $rate = $data["commission_rate"] != '' ? $data["commission_rate"] : 10;
                    if ($data["commission_type"] == 'percentage') {
                        $commission = ($data['order_sum'] * $rate) / 100;
                    } else {
                        $commission = $rate;
                    }
                    echo "S$" . $data['vendor_commission'];
                    ?></label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Vendor Payment</label>
                <label class="col-lg-8 control-label">
                    <?php
                    $rate = $data["commission_rate"] != '' ? $data["commission_rate"] : 10;
                    if ($data["commission_type"] == 'percentage') {
                        $commission = ($data['order_sum'] * $rate) / 100;
                    } else {
                        $commission = $rate;
                    }
                    echo "S$" . ($data['vendor_payment']);
                    ?>
                </label>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Payment Status</label>
                <label class="col-lg-8 control-label">
                    <?php
                    if ($data['payment_ref_number'] == "") {
                        echo "Pending";
                    } else {
                        echo "Paid";
                    }
                    ?>
                </label>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Paid Date</label>
                <label class="col-lg-8 control-label">
                    <?php
                    if ($data['payment_date'] == "") {
                        echo "&nbsp;";
                    } else {
                        echo date("d/m/y", strtotime($data['payment_date']));
                    }
                    ?>
                </label>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Reference Number</label>
                <label class="col-lg-8 control-label"><?php
                    if ($data['payment_ref_number'] == "") {
                        echo "&nbsp;";
                    } else {
                        echo $data['payment_ref_number'];
                    }
                    ?></label>
            </div>

            <div class="form-group">
                <label class="col-lg-4 control-label">Notes</label>
                <label class="col-lg-8 control-label"><?php
                    if ($data['notes'] == "") {
                        echo "&nbsp;";
                    } else {
                        echo $data['notes'];
                    }
                    ?></label>
            </div>
        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
    <?php echo Html::a('Back', ['vendors/payments'], ['class' => 'btn btn-default']); ?>
    </div>
</section>