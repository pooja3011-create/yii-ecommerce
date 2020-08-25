<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset Password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-4 col-md-offset-4 m-t-lg">
    <section class="panel">
        <header class="panel-heading text-center">
            <h1><?= Html::encode($this->title) ?></h1>   
            <?php if (Yii::$app->session->hasFlash('success')) { ?>
                <div class="signupSuccess" style="text-align: center;color: green;">
                    <?= Yii::$app->session->getFlash('success'); ?>
                    <?php // Yii::$app->session->setFlash('success', ''); ?>
                </div>
            <?php } ?>
            <?php if (Yii::$app->session->hasFlash('error')) { ?>
                <div class="signupSuccess" style="text-align: center;color: red;">
                    <?= Yii::$app->session->getFlash('error'); ?>
                    <?php // Yii::$app->session->setFlash('error', ''); ?>
                </div>            
            <?php } ?>
        </header>
        <?php
        $form = ActiveForm::begin(['id' => 'password-reset-form', 'options' => [
                        'class' => 'panel-body'
        ]]);
        ?>
        <div class="form-group"> 
            <label for="newPassword">Enter New Password</label> 
            <input type="password" class="form-control underlined" name="newPassword" id="newPassword">
        </div>
        <div class="form-group"> 
            <label for="confirmPassword">Confirm Password</label> 
            <input type="password" class="form-control underlined" name="confirmPassword" id="confirmPassword"> 
        </div>
        <div class="form-group"> 
            <button type="submit" class="btn btn-block btn-primary">Reset</button>
        </div>
        
        <?php ActiveForm::end(); ?>
    </section>
</div>

