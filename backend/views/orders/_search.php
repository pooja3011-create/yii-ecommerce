<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrdersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'vendor_id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'order_date') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'estimate_delivery_date') ?>

    <?php // echo $form->field($model, 'actual_delivery_date') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'updated_date') ?>

    <?php // echo $form->field($model, 'guest_checkout') ?>

    <?php // echo $form->field($model, 'billing_address1') ?>

    <?php // echo $form->field($model, 'billing_address2') ?>

    <?php // echo $form->field($model, 'billing_city') ?>

    <?php // echo $form->field($model, 'billing_state') ?>

    <?php // echo $form->field($model, 'billing_country') ?>

    <?php // echo $form->field($model, 'billing_zip') ?>

    <?php // echo $form->field($model, 'billing_phone') ?>

    <?php // echo $form->field($model, 'shipping_address1') ?>

    <?php // echo $form->field($model, 'shipping_address2') ?>

    <?php // echo $form->field($model, 'shipping_city') ?>

    <?php // echo $form->field($model, 'shipping_state') ?>

    <?php // echo $form->field($model, 'shipping_country') ?>

    <?php // echo $form->field($model, 'shipping_zip') ?>

    <?php // echo $form->field($model, 'shipping_phone') ?>

    <?php // echo $form->field($model, 'promocode') ?>

    <?php // echo $form->field($model, 'invoice_id') ?>

    <?php // echo $form->field($model, 'invoice_date') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
