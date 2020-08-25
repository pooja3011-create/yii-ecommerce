<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Helper;

$this->title = 'Order Shipment';
$rolePermitions = Helper::getRolePermission();
$orderId = $_GET['order_id'];
?>
<section class="panel">
    <header class="panel-heading bg-light">
        <ul class="nav nav-tabs nav-justified">
            <li id="orders" ><a href="<?php echo URL::to(['/orders/order-detail', 'id' => $orderId], true); ?>">Order Information</a></li>   
            <li class="active" id="shipment"><a data-toggle="tab" href="#shipment" aria-controls="shipment" role="tab" data-toggle="tab">Shipments</a></li>
        </ul>
    </header>
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'shipmentUpdate',
                    'id' => 'shipmentUpdate',
                    'class' => 'bs-example form-horizontal',
                ]
    ]);
    $postArr = array();
    if (isset($shipmentArr) && !empty($shipmentArr)) {
        $postArr = $shipmentArr[0];
    }
    ?>
    <div class="panel-body">
        <div class="col-lg-6">
            <div class="form-group">
                <label class="col-lg-4 control-label">Carrier</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" name="carrier"  value="<?php echo (isset($postArr['carrier']) && $postArr['carrier'] != '') ? $postArr['carrier'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Tracking Number</label>
                <div class="col-lg-8">
                    <input type="text" class="form-control" name="traking_number"  value="<?php echo (isset($postArr['traking_number']) && $postArr['traking_number'] != '') ? $postArr['traking_number'] : '' ?>" >
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Date Shipped</label>
                <div class="col-lg-8">
                    <input type="hidden" name="orderDate" id="orderDate" value="<?php echo date('d/m/Y',  strtotime($orderDate['order_date'])); ?>"/>
                    <input type="text" class="form-control" name="shipped_date" id="shipped_date"  value="<?php echo (isset($postArr['shipped_date']) && $postArr['shipped_date'] != '') ? date('d/m/Y', strtotime($postArr['shipped_date'])) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">From</label>
                <div class="col-lg-8">
                    <select class="form-control" name="shipment_from" disabled="disabled">
                        <option value=""><?php echo $postArr['shipment_from']; ?></option>
                        <?php /*
                          foreach ($shippedFromArr as $key => $value) {
                          ?>
                          <option value="<?php echo $key; ?>" <?php echo (trim($postArr['shipment_from']) == trim($value)) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                          <?php
                          } */
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">To</label>
                <div class="col-lg-8">
                    <select class="form-control" name="shipment_to" disabled="disabled">
                        <option value="">Select shipped to</option>
                        <option value="Administrator" <?php echo (trim($postArr['shipment_to']) == 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
                        <option value="Consumer" <?php echo (trim($postArr['shipment_to']) == 'Consumer') ? 'selected' : ''; ?> >Consumer</option>
                    </select>                   
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Notes</label>
                <div class="col-lg-8">
                    <textarea class="form-control" name="shipment_note" rows="6"><?php echo (isset($postArr['shipment_note']) && $postArr['shipment_note'] != '') ? ($postArr['shipment_note']) : '' ?></textarea>                    
                </div>
            </div>                
        </div>
        <div class="col-lg-6" id="sectionProducts">
            <h4>Select Shipped Items</h4>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Item Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($shipmentArr as $product) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="productId[]" checked="checked" disabled=""/></td>
                            <td><?php echo $product['name']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">       
        <?php
        if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('edit', $rolePermitions['orders'])) {
//            if ($postArr['shipment_status'] != '1') {
                echo Html::submitButton('Save Shipment', ['class' => 'btn btn-primary']);
                echo '&nbsp;';
//            }
        }

        echo Html::a('Back', ['/orders/order-detail', 'id' => $orderId, 'fromShipment' => 1], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</section>
