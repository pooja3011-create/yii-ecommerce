<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Vendors */

$this->title = 'Add/Edit Vendors';
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<section class="panel">   

    <header class="panel-heading bg-light">
        <ul class="nav nav-tabs nav-justified">
            <li class="active"><a data-toggle="tab" href="#account_info">Account Info</a></li>
            <li><a data-toggle="tab" href="javascript:;" >Shop info</a></li>
            <li><a data-toggle="tab" href="javascript:;">Bank account info</a></li>
            <?php /** <li><a data-toggle="tab" href="javascript:;">Products</a></li>
              <li><a data-toggle="tab" href="javascript:;">Orders</a></li> */ ?>
        </ul>
    </header>
    <div class="panel-body">
        <div class="tab-content">
            <div id="account_info">
                <div class="row cleafix">
                    <div class="col-sm-7">
                        <?php
                        $form = ActiveForm::begin([
                                    'options' => [
                                        'name' => 'Vendor',
                                        'id' => 'Vendor'
                                    ]
                        ]);
                        $postData = array();
                        if (isset($_POST['Vendors'])) {
                            $postData = $_POST['Vendors'];
                            $randomString = $postData['vendor_code'];
                        }
                        ?>
                        <input type="hidden" name="save" class="pageSave"/>
                        <div class="tab-content">
                            <div id="account_info" class="tab-pane fade in active">
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Vendor Code</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'vendor_code', [ 'inputOptions' => ['value' => '', 'class' => 'form-control', 'readonly' => 'readonly']])->textInput(['maxlength' => true])->label(FALSE) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Full Name</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'name', [ 'inputOptions' => ['value' => (isset($postData['name']) ? $postData['name'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Email</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'email', [ 'inputOptions' => ['value' => (isset($postData['email']) ? $postData['email'] : ''), 'class' => 'form-control']])->textInput(['maxlength' => true])->label(FALSE); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Phone Number</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'phone', [ 'inputOptions' => ['value' => (isset($postData['phone']) ? $postData['phone'] : ''), 'class' => 'form-control', 'onkeypress' => 'return isPhone(event);']])->textInput(['maxlength' => '20'])->label(FALSE); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Gender</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'gender')->dropDownList([ 'male' => 'Male', 'female' => 'Female',])->label(FALSE); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Password</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'password')->passwordInput(['maxlength' => true])->label(FALSE); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Confirm Password</label>
                                    <div class="col-lg-8 form-group">

                                        <input type="password" aria-required="true" maxlength="255" name="Vendors[password_repeat]" class="form-control" id="vendors-password_repeat">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 control-label">Status</label>
                                    <div class="col-lg-8">
                                        <?= $form->field($model, 'status')->dropDownList([ '1' => 'Active', '0' => 'Inactive'])->label(FALSE) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">            
        <?php echo Html::a('Save and Continue', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("Vendor","0")']); ?>
        <?php echo Html::a('Save Vendor', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("Vendor","1")']); ?>
        <?php echo Html::a('Back', ['vendors/index'], ['class' => 'btn btn-default']); ?>
    </div>
</section>


