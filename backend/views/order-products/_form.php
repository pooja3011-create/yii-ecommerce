<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProducts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-products-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'vendor_id')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipment_status')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'carrier')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'traking_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipped_date')->textInput() ?>

    <?= $form->field($model, 'shipment_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipment_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shipment_note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'updated_date')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
