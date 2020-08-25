<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$this->title = 'Add Customer';
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'customerSave',
                    'id' => 'customerSave',
                    'class' => 'bs-example form-horizontal',
                    'tag' => false,
                ]
    ]);
    $postData = array();
    if (isset($_POST['User'])) {
        $postData = $_POST['User'];
        $randomString = $postData['vendor_code'];
    }
    ?>
    <div class="panel-body">
        <div class="row cleafix">
            <div class="col-lg-9">
                <div class="form-group">
                    <label class="col-lg-3 control-label">Customer ID</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'user_code', [ 'inputOptions' => ['value' => '', 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Registration Date</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'created_at', [ 'inputOptions' => ['value' => '', 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Full Name</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'first_name', [ 'inputOptions' => ['value' => (isset($postData['first_name']) ? $postData['first_name'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Email</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'email', [ 'inputOptions' => ['value' => (isset($postData['email']) ? $postData['email'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Phone</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'phone', [ 'inputOptions' => ['value' => (isset($postData['phone']) ? $postData['phone'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Gender</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'gender', [ 'inputOptions' => ['value' => (isset($postData['gender']) ? $postData['gender'] : ''), 'class' => 'form-control']])->dropDownList(['male' => 'Male', 'female' => 'Female'])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Address Line 1</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'billing_address1', [ 'inputOptions' => ['value' => (isset($postData['billing_address1']) ? $postData['billing_address1'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">City</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'billing_city', [ 'inputOptions' => ['value' => (isset($postData['billing_city']) ? $postData['billing_city'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Country</label>
                    <div class="col-lg-9">
                        <?php
                        echo $form->field($model, 'billing_country')->dropDownList($countryArr, ['prompt' => 'Select Country'])->label(FALSE)
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Birthdate</label>
                    <div class="col-lg-9">
                        <div class="form-group">
                            <input type="text" value="" name="User[birthdate]" class="form-control" id="user-birthdate">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Password</label>
                    <div class="col-lg-9">
                        <div class="form-group field-user-password">
                            <input type="password" value="" name="User[password]" class="form-control" id="user-password">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Confirm Password</label>
                    <div class="col-lg-9">
                        <div class="form-group">
                            <input type="password" value="" name="User[confirm_password]" class="form-control" id="user-confirm_password">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">Status</label>
                    <div class="col-lg-9">
                        <?= $form->field($model, 'status')->dropDownList([ '1' => 'Active', '0' => 'Inactive'])->label(FALSE) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">       
        <?php
        echo Html::submitButton('Save Customer', ['class' => 'btn btn-primary', 'name' => 'save']);
        echo '&nbsp;';
        echo Html::a('Back', ['user/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



