<?php

use yii\helpers\Url; ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Shipment History</h4>
        </div>
        <div class="col-lg-12" style="margin-bottom: 10px;margin-top: 10px;">
            <?php
            if (!empty($shipmentArr)) {
                foreach ($shipmentArr as $data) {
                    ?>
                    <div class="col-lg-12"  style="margin-bottom: 10px;border-bottom: 1px solid #eee;">
                        <div class="col-lg-12"><label class="col-lg-4 control-label">Carrier</label>
                            <div class="col-lg-8"><?php echo $data['carrier']; ?></div></div>
                        <div class="col-lg-12">
                            <label class="col-lg-4 control-label">Tracking Number</label>
                            <div class="col-lg-8"><?php echo $data['traking_number']; ?></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="col-lg-4 control-label">Shipped Date</label><div class="col-lg-8"><?php echo date('d/m/Y',  strtotime($data['shipped_date'])); ?></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="col-lg-4 control-label">Shipment From</label><div class="col-lg-8"><?php echo $data['shipment_from']; ?></div>
                        </div>
                        <div class="col-lg-12">
                            <label class="col-lg-4 control-label">Shipment To</label><div class="col-lg-8"><?php echo $data['shipment_to']; ?></div> 
                        </div>
                        <div class="col-lg-12">
                            <label class="col-lg-4 control-label">Shipped Note</label><div class="col-lg-8"><?php echo $data['shipment_note']; ?></div>    
                        </div>                                           
                    </div>
           
                    <?php
                }
            } else {
                echo 'Record not found.';
            }
            ?>
        </div>
        <div class="" style="padding: 15px;text-align: right;">
            <button data-dismiss="modal" class="btn btn-primary" type="button">Close</button>
            <!--<a href="javascript:;" class="btn btn-primary">Close</a>-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
