<?php

use yii\helpers\Url; ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Disapprove Product(s)</h4>
        </div>
        <div class="col-lg-12" style="margin-bottom: 10px;margin-top: 10px;">
            <label class="col-lg-3 control-label">Select a reason</label>
            <div class="col-lg-9">
                <select id="disapproveReason" name="disapproveReason" class="form-control" onchange="return changeReason($(this));">
                    <option value="">Select reason</option>
                    <option value="Reason 1">Reason 1</option>
                    <option value="Reason 2">Reason 2</option>
                    <option value="Reason 3">Reason 3</option>
                    <option value="Other">Other</option>
                </select>
                <br>
                <textarea placeholder="Enter reason" id="txtOtherReason" class="form-control" style="display: none;s"></textarea>
                <label id="txtOtherReasonError" style="display: none;" class="error">Please enter reason.</label>
            </div>
        </div>
        <div class="" style="padding: 15px;text-align: right;">
            <a href="javascript:;" class="btn btn-primary" id="submitInfo" onclick="disapprove('<?php echo URL::to(['/products'], true); ?>')">Disapproved</a>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

