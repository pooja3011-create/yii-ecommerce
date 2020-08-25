<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="signin-data-box">

    <div class="signin-tabs">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active"><a href="#login" aria-controls="home" role="tab" data-toggle="tab">NEW TO BOUcle</a></li>
            <li role="presentation"><a href="#register" aria-controls="profile" role="tab" data-toggle="tab">ALREADY REGISTERED?</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="login">
                <div class="signin-form">
                    <div class="signin-title">SIGN IN WITH EMAIL</div>

                    <div class="form-row">
                        <div class="form-label">Email Address</div>
                        <div class="input-box"><input type="text" placeholder=""></div>

                    </div>

                    <div class="form-row">
                        <div class="form-label">Password</div>
                        <div class="input-box"><input type="text" placeholder=""></div>

                    </div>

                    <div class="form-row">
                        <div class="input-box button-box"><button type="submit" class="black-btn">LOGIN</button></div>
                    </div>

                    <div class="form-row">
                        <div class="input-box"><div class="forgot-link"><a href="#">Forgot password?</a></div></div>
                    </div>

                    <div class="form-row">
                        <div class="input-box"><div class="orbox"><span>OR</span></div></div>
                    </div>

                    <div class="form-row">
                        <div class="input-box"><div class="fblogin-btn"><a href="#"><img src="images/fb-btn-login.png"></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="register">
                <?php
                $form = ActiveForm::begin([
                            'options' => [
                                'name' => 'frmSignup',
                                'id' => 'frmSignup',
                            ]
                ]);
                ?>
                <div class="signin-form">
                    <div class="signin-title">SIGN UP WITH</div>
                    <div class="form-row">
                        <div class="input-box"><div class="fblogin-btn"><a href="#"><img src="images/fb-btn-login.png"></a></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-box"><div class="orbox"><span>OR</span></div></div>
                    </div>
                    <div class="signin-title">SIGN UP USING YOUR EMAIL ADDRESS</div>
                    <div class="form-row">
                        <div class="form-label">First Name</div>
                        <div class="input-box">
                            <input type="text" name="Signup[first_name]" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Last Name</div>
                        <div class="input-box">
                            <input type="text" name="Signup[last_name]" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Email Address</div>
                        <div class="input-box"><input type="text" placeholder=""></div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Password</div>
                        <div class="input-box">
                            <input type="password" name="Signup[password]" id="txtPassword" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Confirm Password</div>
                        <div class="input-box">
                            <input type="password" name="Signup[confirm_password]" placeholder="">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Date of Birth</div>
                        <div class="input-box">
                            <div class="row clearfix">
                                <div class="col-sm-4">
                                    <select id="Day" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                        <option value="">DD</option>
                                        <?php
                                        for ($i = 1; $i <= 31; $i++) {
                                            ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
                                        }
                                        ?>                                        
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <select id="Month" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                        <option value="">MM</option>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select id="Year" tabindex="-1" class="select2-hidden-accessible" aria-hidden="true">
                                        <option value="">YYYY</option>
                                        <?php
                                        $curYear = date('Y');
                                        for ($i = $curYear; $i >= ($curYear - 100); $i--) {
                                            ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-label">Gender</div>
                        <div class="input-box">
                            <div class="inline-input">
                                <input id="radio-1" class="radio-custom" name="radio-group" type="radio" checked>
                                <label for="radio-1" class="radio-custom-label">Male</label>
                            </div>
                            <div class="inline-input">
                                <input id="radio-2" class="radio-custom" name="radio-group" type="radio">
                                <label for="radio-2" class="radio-custom-label">Female</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-box button-box"><button type="submit" class="black-btn">Sign Up</button></div>
                    </div>
                    <div class="form-row">
                        <div class="input-box"><div class="terms-link">By creating your account, you agree to our<a href="#">Terms and Conditions?</a></div></div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>  
            </div>
        </div>
    </div>
</div>
<?php
/** <div class="site-login">
  <h1><?= Html::encode($this->title) ?></h1>

  <p>Please fill out the following fields to login:</p>

  <div class="row">
  <div class="col-lg-5">
  <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

  <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

  <?= $form->field($model, 'password')->passwordInput() ?>

  <?= $form->field($model, 'rememberMe')->checkbox() ?>

  <div style="color:#999;margin:1em 0">
  If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
  </div>

  <div class="form-group">
  <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
  </div>

  <?php ActiveForm::end(); ?>
  </div>
  </div>
  </div>* */
?>

