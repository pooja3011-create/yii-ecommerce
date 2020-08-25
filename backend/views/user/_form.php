<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'user_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password_reset_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gender')->dropDownList([ 'male' => 'Male', 'female' => 'Female', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'billing_address1')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'billing_address2')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'billing_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_country')->textInput() ?>

    <?= $form->field($model, 'billing_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'billing_date')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'last_loggedin')->textInput() ?>

    <?= $form->field($model, 'user_role')->textInput() ?>

    <?= $form->field($model, 'social_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'social_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
