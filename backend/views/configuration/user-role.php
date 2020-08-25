<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$this->title = 'Settings - Add Admin User Role';
if (isset($_GET['id']) && $_GET['id'] != '') {
    $role = (isset($model['name'])) ? '- ' . $model['name'] : '';
    $this->title = 'Settings - Admin User Roles ' . $role;
}

$permission = array();
if (isset($model['permission']) && !empty($model['permission'])) {
    $permission = $model['permission'];
}
$rolePermitions = Helper::getRolePermission();
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'roleSave',
                    'id' => 'roleSave',
                    'class' => 'bs-example ',
                    'tag' => false,
                ]
    ]);
    ?>

    <?php
    if (Yii::$app->user->id == '1') {
        ?>
        <header class="panel-heading bg-light">
            <ul class="nav nav-tabs nav-justified">
                <li id="general"><a href="<?php echo URL::to(['/configuration'], true); ?>">General Settings</a></li>
                <li class="active" id="adminRoles"><a data-toggle="tab" href="#adminRoles" aria-controls="adminRoles" role="tab" data-toggle="tab">Admin User Roles</a></li>          
                <li id="adminUsers"><a href="<?php echo URL::to(['/configuration', 'fromUsers' => '1'], true); ?>">Admin Users</a></li>  

                <?php
            } else {
                if (isset($rolePermitions['configuration']) && in_array('view', $rolePermitions['configuration']) && isset($rolePermitions['admin_users']) && in_array('list', $rolePermitions['admin_users'])) {
                    ?>    
                    <header class="panel-heading bg-light">
                        <ul class="nav nav-tabs nav-justified">
                            <li id="general"><a href="<?php echo URL::to(['/configuration'], true); ?>">General Settings</a></li>         
                            <li id="adminUsers"><a href="<?php echo URL::to(['/configuration', 'fromUsers' => '1'], true); ?>">Admin Users</a></li>   
                        </ul>
                    </header>
                    <?php
                }
            }
            ?>
            <div class="panel-body">
                <div class="tab-content">
                    <div id="adminRoles" class="tab-pane fade in active">                
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Role Title</label>
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="UserRolesSearch[name]" value="<?php echo (isset($model['name'])) ? $model['name'] : ''; ?>"/>
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-lg-3 control-label">Status</label>
                                <div class="col-lg-9">
                                    <div class="form-group">
                                        <select name="UserRolesSearch[status]" class="form-control">
                                            <option value="1" <?php echo (isset($model['status']) && $model['status'] == '1') ? 'selected="selected"' : ''; ?>>Active</option>
                                            <option value="0" <?php echo (isset($model['status']) && $model['status'] == '0') ? 'selected="selected"' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>

                                </div>
                            </div> 
                        </div>
                        <div class="cleafix"></div>
                        <div class="col-sm-12" id="sectionPermission">
                            <h4><strong>Access Permissions</strong></h4>
                            <label id="permissionError" style="display: none">Please select access permission.</label>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Module</th>
                                        <th style="text-align: center;">Add<br/><input type="checkbox" onclick="checkedBox('chkAdd')" id="chkAdd" /></th>
                                        <th style="text-align: center;">Edit<br/><input type="checkbox" onclick="checkedBox('chkEdit')" id="chkEdit" /></th>
                                        <th style="text-align: center;">Delete<br/><input type="checkbox" onclick="checkedBox('chkDelete')" id="chkDelete"/></th>
                                        <th style="text-align: center;">View<br/><input type="checkbox" onclick="checkedBox('chkView')"  id="chkView"/></th>
                                        <th style="text-align: center;">List<br/><input type="checkbox" onclick="checkedBox('chkList')" id="chkList" /></th>
                                    </tr>
                                    <?php
                                    if (!empty($modules)) {

                                        foreach ($modules as $key => $val) {
                                            ?> 
                                            <tr>
                                                <td><?php echo ucwords(str_replace('_', ' ', $key)); ?></td>
                                                <td style="text-align: center;">
                                                    <?php if (isset($val['add'])) {
                                                        ?><input type="checkbox" name="permission[<?php echo $val['add']; ?>]" <?php echo in_array($val['add'], $permission) ? 'checked="checked"' : ''; ?> class="chkAdd"/><?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php if (isset($val['edit'])) {
                                                        ?><input type="checkbox" name="permission[<?php echo $val['edit']; ?>]" <?php echo in_array($val['edit'], $permission) ? 'checked="checked"' : ''; ?> class="chkEdit"/><?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php if (isset($val['delete'])) {
                                                        ?> <input type="checkbox" name="permission[<?php echo $val['delete']; ?>]"  <?php echo in_array($val['delete'], $permission) ? 'checked="checked"' : ''; ?> class="chkDelete"/><?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php if (isset($val['view'])) {
                                                        ?><input type="checkbox" name="permission[<?php echo $val['view']; ?>]" <?php echo in_array($val['view'], $permission) ? 'checked="checked"' : ''; ?> class="chkView"/><?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php if (isset($val['list'])) {
                                                        ?> <input type="checkbox" name="permission[<?php echo $val['list']; ?>]" <?php echo in_array($val['list'], $permission) ? 'checked="checked"' : ''; ?> class="chkList"/><?php
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
                <?php
                if (isset($model['id'])) {
                    echo Html::a('Delete Role', ['configuration/delete-role', 'id' => $model['id']], ['class' => 'btn btn-primary', 'onclick' => "javascript: return confirm('".Yii::$app->params['delRoleConf']."');"]);
                    echo '&nbsp;';
                }

                if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('edit', $rolePermitions['admin_users'])) {
                    echo Html::submitButton('Save Role', ['class' => 'btn btn-primary', 'onclick' => 'javascript:return checkPermission();']);
                }
                echo '&nbsp;';
                echo Html::a('Back', ['configuration/index', 'fromRoles' => 1], ['class' => 'btn btn-default']);
                ?>
            </div>
            <?php ActiveForm::end(); ?>  
            </section>



