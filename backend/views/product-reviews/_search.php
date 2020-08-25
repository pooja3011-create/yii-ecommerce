<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductReviewsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-reviews-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'variation_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'review_title') ?>

    <?php // echo $form->field($model, 'review') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'review_date') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
