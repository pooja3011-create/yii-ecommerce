<?php

use yii\helpers\Url; ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit Order</h4>
        </div>
        <div class="col-lg-12" style="margin-bottom: 10px;margin-top: 10px;">
            <form name="frmProducts"  id="frmProducts"> 
                <?php
                if (!empty($productArr)) {
                    foreach ($productArr as $data) {
                        ?>
                        <div class="col-lg-12"  style="margin-bottom: 10px;">
                            <label class="col-lg-4 control-label"><?php echo $data['name']; ?></label>
                            <div class="col-lg-8">
                                <textarea name="note_<?php echo $data['id']; ?>" class="form-control product-note" id="note_<?php echo $data['id']; ?>" data-id='<?php echo $data['id']; ?>'> </textarea>
                                <label class="error note_<?php echo $data['id']; ?>" style="display: none;">Please enter note.</label>
                            </div>

                        </div>

                        <?php
                    }
                } else {
                    echo 'Record not found.';
                }
                ?>
            </form>


        </div>
        <div class="" style="padding: 15px;text-align: right;">
            <button class="btn btn-primary" type="button" id="btnSaveNote" onclick="javascript:return saveOrderNote();">Save Order</button>
            <!--<a href="javascript:;" class="btn btn-primary">Close</a>-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
