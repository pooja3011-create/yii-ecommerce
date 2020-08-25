<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-md-4 col-md-offset-4 m-t-lg">
    <section class="panel">
        <header class="panel-heading text-center">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>Welcome to the backend portal. Please sign in.</p>
            
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
        $form = ActiveForm::begin(['id' => 'login-form', 'options' => [
                        'class' => 'panel-body'
        ]]);
        ?>
        <!--<form action="index.html" class="panel-body">-->
        <?= $form->field($model, 'email')->textInput(['autofocus' => true,'tabindex'=>'1']) ?>

        <?= $form->field($model, 'password')->passwordInput(['tabindex'=>'2']) ?>

        <?= $form->field($model, 'rememberMe')->checkbox(['tabindex'=>'3']) ?>
        <?php echo Html::a('Forgot Password', ['/site/forgot-password'], ['class' => 'pull-right m-t-xs','tabindex'=>'5']); ?>       
        <?= Html::submitButton('Login', ['class' => 'btn custom-btn', 'name' => 'login-button','tabindex'=>'4']) ?>

        <?php ActiveForm::end(); ?>
    </section>
</div>
