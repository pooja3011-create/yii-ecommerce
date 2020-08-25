<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderProductsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-products-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'qty') ?>

    <?php // echo $form->field($model, 'shipment_status') ?>

    <?php // echo $form->field($model, 'carrier') ?>

    <?php // echo $form->field($model, 'traking_number') ?>

    <?php // echo $form->field($model, 'shipped_date') ?>

    <?php // echo $form->field($model, 'shipment_from') ?>

    <?php // echo $form->field($model, 'shipment_to') ?>

    <?php // echo $form->field($model, 'shipment_note') ?>

    <?php // echo $form->field($model, 'created_date') ?>

    <?php // echo $form->field($model, 'updated_date') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
