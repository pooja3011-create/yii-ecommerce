<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Forgot Password';
?>
<div class="col-md-4 col-md-offset-4 m-t-lg">
    <section class="panel">
        <header class="panel-heading text-center">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>Welcome to the backend portal. Please provide your email to reset password.</p>
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
         <?php $form = ActiveForm::begin(['id' => 'reset-form','options' => [
                'class' => 'panel-body'
             ]]); ?>
            <div class="form-group"> 
                <label for="email1">Email</label> 
                <input type="email" class="form-control underlined" name="email1" id="email1" placeholder="Your email address" required> 
            </div>
            <div class="form-group"> 
                <button type="submit" class="btn btn-block btn-primary">Reset</button>
            </div>
            <a class="pull-right m-t-xs" href="<?php echo Url::to(['site/login']); ?>">Return to Login</a>            
            <?php ActiveForm::end(); ?>
    </section>
</div>


<?php /** <div class="auth-content">
    <div class="row">
        <div class="col-lg-5">
            <p class="text-xs-center">PASSWORD RECOVER</p>
            <p class="text-muted text-xs-center"><small>Welcom to the backend portal. Please provide your email to reset password.</small></p>
            <?php $form = ActiveForm::begin(['id' => 'reset-form']); ?>
            <div class="form-group"> 
                <label for="email1">Email</label> 
                <input type="email" class="form-control underlined" name="email1" id="email1" placeholder="Your email address" required> 
            </div>
            <div class="form-group"> 
                <button type="submit" class="btn btn-block btn-primary">Reset</button>
            </div>
            <div class="form-group clearfix"> 
                <a class="pull-left" href="<?php echo Url::to(['site/login']); ?>">Return to Login</a>
                <!--<a class="pull-right" href="signup.html">Sign Up!</a>--> 
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>**/ ?>

