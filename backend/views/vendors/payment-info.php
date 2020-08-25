<?php

use yii\helpers\Url; ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Payment Info</h4>
        </div>
        <div class="col-lg-12" style="margin-bottom: 12px;margin-top: 10px;">
            <label class="col-lg-3 control-label">Payment Date</label>
            <div class="col-lg-9">
                <input type="text" class="form-control datepicker" name="payment_date" id="payment_date" >
                <label class="error" id="payment_date_error" style="display: none"> Please select payment date.</label>
            </div>
        </div>
        <div class="col-lg-12" style="margin-bottom: 5px;">
            <label class="col-lg-3 control-label">Reference Number</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="ref_num" id="ref_num" >
                <label class="error" id="ref_num_error" style="display: none"> Please enter reference number.</label>
            </div>
        </div>
        <div class="col-lg-12" style="margin-bottom: 10px;">
            <label class="col-lg-3 control-label">Notes</label>
            <div class="col-lg-9">
                <textarea class="form-control"  name="payment_notes" id="payment_notes"></textarea>

            </div>
        </div>
        <div class="form-group" style="padding: 15px;text-align: right;">
            <a href="javascript:;" class="btn btn-primary" id="submitInfo" onclick="addPaymentInfo('<?php echo URL::to(['/vendors/payments'], true); ?>')">Save Payment Info</a>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: '0'
    });
</script>