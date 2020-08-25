<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $postArr app\models\Products */


$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$postArr = $model;
$this->title = 'Settings - Add Admin User';
if (isset($_GET['id']) && $_GET['id'] != '') {
    $role = (isset($postArr['first_name'])) ? '- ' . $postArr['first_name'] : '';
    $this->title = 'Settings - Admin Users ' . $role;
}

if (isset($_POST['User'])) {
    $postArr = $_POST['User'];
}

$rolePermitions = Helper::getRolePermission();
?>
<section class="panel">
    <?php
    if (isset($model['id']) && $model['id'] != '') {
        $form = ActiveForm::begin([
                    'options' => [
                        'name' => 'adminUserUpadte',
                        'id' => 'adminUserUpadte',
                        'class' => 'bs-example form-horizontal',
                        'autocomplete' => 'off'
                    ]
        ]);
    } else {
        $form = ActiveForm::begin([
                    'options' => [
                        'name' => 'adminUser',
                        'id' => 'adminUser',
                        'class' => 'bs-example form-horizontal',
                    ]
        ]);
    }
    ?>

    <?php
    if (Yii::$app->user->id == '1') {
        ?>
        <header class="panel-heading bg-light">
            <ul class="nav nav-tabs nav-justified">
                <li id="general"><a href="<?php echo URL::to(['/configuration'], true); ?>">General Settings</a></li>
                <li id="adminRoles"><a href="<?php echo URL::to(['/configuration', 'fromRoles' => '1'], true); ?>">Admin User Roles</a></li>          
                <li class="active" id="adminUsers"><a href="javascript:;">Admin Users</a></li> 
            </ul>
        </header>
        <?php
    } else {
        if (isset($rolePermitions['configuration']) && in_array('view', $rolePermitions['configuration']) && isset($rolePermitions['admin_users']) && in_array('list', $rolePermitions['admin_users'])) {
            ?>   
            <header class="panel-heading bg-light">
                <ul class="nav nav-tabs nav-justified">
                    <li id="general"><a href="<?php echo URL::to(['/configuration'], true); ?>">General Settings</a></li>       
                    <li class="active" id="adminUsers"><a href="javascript:;">Admin Users</a></li>    </ul>
            </header>
            <?php
        }
    }
    ?>
    <div class="panel-body">
        <div class="tab-content">
            <div id="adminRoles" class="tab-pane fade in active">                
                <div class="col-sm-9">
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Admin User ID</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <input type="text" class="form-control" name="User[user_code]" value="<?php echo (isset($postArr['user_code'])) ? $postArr['user_code'] : ''; ?>" readonly="readonly"/>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Full Name</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <input type="text" class="form-control" name="User[first_name]" value="<?php echo (isset($postArr['first_name'])) ? $postArr['first_name'] : ''; ?>"/>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Email</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <input type="text" class="form-control" name="User[email]" value="<?php echo (isset($postArr['email'])) ? $postArr['email'] : ''; ?>"/>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Admin Role</label>
                        <div class="col-lg-9">
                            <div class="form-group">

                                <select name="User[user_role]" class="form-control">
                                    <option value="">Select admin role</option>
                                    <?php foreach ($userRole as $key => $val) {
                                        ?> <option value="<?php echo $key; ?>" <?php echo (isset($postArr['user_role']) && $postArr['user_role'] == $key) ? 'selected="selected"' : ''; ?>><?php echo $val; ?></option><?php }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Password</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <input type="password" class="form-control" name="User[password]" id="password" autocomplete="off"/>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Confirm Password</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <input type="password" class="form-control" name="User[confirmPassword]"/>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label class="col-lg-3 control-label">Status</label>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <select name="User[status]" class="form-control">
                                    <option value="1" <?php echo (isset($postArr['status']) && $postArr['status'] == '1') ? 'selected="selected"' : ''; ?>>Active</option>
                                    <option value="0" <?php echo (isset($postArr['status']) && $postArr['status'] == '0') ? 'selected="selected"' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div> 
                </div>

            </div>
        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('delete', $rolePermitions['admin_users'])) {
            if (isset($model['id']) && $model['id'] != '') {
                echo Html::a('Delete Admin User', ['configuration/delete-user', 'id' => $model['id']], ['class' => 'btn btn-primary', 'onclick' => "javascript: return confirm('".Yii::$app->params['delAdminUserConf']."');"]);
                echo '&nbsp;';
            }
        }
        if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('edit', $rolePermitions['admin_users'])) {
            echo Html::submitButton('Save Admin User', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
        }
        ?>

        <?php echo Html::a('Back', ['configuration/index', 'fromUsers' => 1], ['class' => 'btn btn-default']); ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



