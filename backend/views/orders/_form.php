<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orders-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'vendor_id')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'order_date')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'estimate_delivery_date')->textInput() ?>

    <?= $form->field($model, 'actual_delivery_date')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'updated_date')->textInput() ?>

    <?= $form->field($model, 'guest_checkout')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'billing_address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_country')->textInput() ?>

    <?= $form->field($model, 'billing_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_country')->textInput() ?>

    <?= $form->field($model, 'shipping_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipping_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'promocode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_date')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
