<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Helper;
use app\models\Orders;

$this->title = 'Order Shipment';
$rolePermitions = Helper::getRolePermission();
$orderId = $_GET['id'];
if (isset($_POST['shipment_from']) && $_POST['shipment_from'] != '') {
    $vendor = $_POST['shipment_from'];
    if (strpos($_POST['shipment_from'], "R") !== false) {
        $vendor = 'admin';
    }
    $productArr = Orders::orderProducts($orderId, $vendor);
   
}
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
                    'name' => 'shipmentAdd',
                    'id' => 'shipmentAdd',
                    'class' => 'bs-example form-horizontal',
                ]
    ]);
    $postArr = isset($_POST) ? $_POST : array();    
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
                    <input type="text" class="form-control" name="traking_number"  value="<?php echo (isset($postArr['traking_number']) && $postArr['traking_number'] != '') ? ($postArr['traking_number']) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Date Shipped</label>
                <div class="col-lg-8">
                    <input type="hidden" name="orderDate" id="orderDate" value="<?php echo date('d/m/Y',  strtotime($orderDate['order_date'])); ?>"/>
                    <input type="text" class="form-control" name="shipped_date" id="shipped_date"  value="<?php echo (isset($postArr['shipped_date']) && $postArr['shipped_date'] != '') ? $postArr['shipped_date'] : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">From</label>
                <div class="col-lg-8">
                    <select class="form-control" name="shipment_from" onchange="javascript:getProducts($(this));" id="shipment_from">
                        <option value="">Select shipped from</option>
                        <?php
                        foreach ($shippedFromArr as $key => $value) {
                            ?>
                        <option value="<?php echo $key; ?>" <?php echo (isset($postArr['shipment_from']) && $postArr['shipment_from'] == $key) ? 'selected' : '' ?> ><?php echo $value; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">To</label>
                <div class="col-lg-8">
                    <select class="form-control" name="shipment_to" id="shipment_to">
                        <option value="">Select shipped to</option>
                        <option value="Administrator" <?php echo (isset($postArr['shipment_to']) && $postArr['shipment_to'] == 'Administrator') ? 'selected' : '' ?>>Administrator</option>
                        <option value="Consumer" <?php echo (isset($postArr['shipment_to']) && $postArr['shipment_to'] == 'Consumer') ? 'selected' : '' ?>>Consumer</option>
                    </select>                   
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label">Notes</label>
                <div class="col-lg-8">
                    <textarea class="form-control" name="shipment_note" rows="6"><?php echo (isset($postArr['shipment_note']) && $postArr['shipment_note'] != '') ? $postArr['shipment_note'] : '' ?></textarea>                    
                </div>
            </div>  
              
        </div>
        <div class="col-lg-6" id="sectionProducts">
            <h4>Select Shipped Items</h4>
            <label for="productId[]" class="error" style="display: none;">Please select shipped product.</label>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Item Name</th>
                    </tr>
                </thead>
                <tbody id="tblProduct">
                    <?php
                    foreach ($productArr as $product) {
                        ?>
                        <tr>
                            <td><input type="checkbox" name="productId[]" value="<?php echo $product['product_id']; ?>" <?php echo (isset($postArr['productId']) && in_array($product['product_id'],$postArr['productId']))?'checked':''; ?>/></td>
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
            echo Html::submitButton('Save Shipment', ['class' => 'btn btn-primary','onclick'=>'javascript:return saveShipment();']);
            echo '&nbsp;';
        }

        echo Html::a('Back', ['/orders/order-detail', 'id' => $orderId, 'fromShipment' => 1], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</section>
