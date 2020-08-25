<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Helper;
use app\models\Configuration;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConfigurationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$rolePermitions = Helper::getRolePermission();

$this->params['breadcrumbs'][] = $this->title;
$config = array();
$userActive = '';
$userRoleActive = '';
$generalActive = '';
if ($fromUsers > 0) {
    $this->title = 'Settings - Admin Users';
    $userActive = 'active';
} else if ($fromRoles > 0) {
    $this->title = 'Settings - Admin User Roles';
    $userRoleActive = 'active';
} else {
    $this->title = 'Settings - General settings';
    $generalActive = 'active';
}

foreach ($model as $fieldName) {
    if (array_key_exists($fieldName->config_key, $config)) {
        if (!is_array($config[$fieldName->config_key])) {
            $config[$fieldName->config_key] = array($config[$fieldName->config_key]);
        }
        array_push($config[$fieldName->config_key], $fieldName->config_val);
    } else {
        $config[$fieldName->config_key] = $fieldName->config_val;
    }
}

$userRoleArr = Configuration::userRole();
$columnArr = [
    [
        'options' => ['style' => 'width:10%;'],
        'label' => 'Admin ID',
        'value' => 'user_code'
    ],
    [
        'attribute' => 'first_name',
        'label' => 'Name',
        'value' => 'first_name'
    ],
    [
        'attribute' => 'email',
        'label' => 'Email',
        'value' => 'email'
    ],
    [
        'label' => 'Admin Role',
        'options' => ['style' => 'width:20%;'],
        'attribute' => 'user_role',
        'value' => function ($userSearchModel) {
    $userRoleArr = Configuration::userRole();
    return $userRoleArr[$userSearchModel->user_role];
},
        'filter' => Html::DropDownList('UserSearch[user_role]', $userSearchModel->user_role, $userRoleArr, array('class' => 'form-control', 'prompt' => 'View All')),
    ],
    [
        'options' => ['style' => 'width:11%;'],
        'label' => 'Date Added',
        'attribute' => 'created_at',
        'value' => 'created_at',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'status',
        'value' => function ($userSearchModel) {
    return $userSearchModel->status == 1 ? 'Active' : 'Inactive';
},
        'filter' => Html::DropDownList('UserSearch[status]', $userSearchModel->status, array("1" => "Active", "0" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
    ],
];
if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('view', $rolePermitions['admin_users'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Action',
        'contentOptions' => ['class' => 'action'],
        'template' => '{update}',
        'buttons' => ['update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-pencil"></span>', ['admin-user', 'id' => $model->id], ['title' => 'Edit']);
            }
                ],
            ];
        }
        ?>

        <section class="panel">

            <header class="panel-heading bg-light">
                <ul class="nav nav-tabs nav-justified">
                    <?php
                    if (Yii::$app->user->id == '1') {
                        ?>
                        <li class="<?php echo $generalActive; ?>" id="general"><a data-toggle="tab" href="#general" aria-controls="general" role="tab" data-toggle="tab" onclick="openTab('','');">General Settings</a></li> 
                        <li id="adminRoles" class="<?php echo $userRoleActive; ?>"><a data-toggle="tab" href="#adminRoles" aria-controls="adminRoles" role="tab" data-toggle="tab" onclick="openTab('fromRoles','');">Admin User Roles</a></li>
                        <li id="adminUsers" class="<?php echo $userActive; ?>"><a data-toggle="tab" href="#adminUsers" aria-controls="adminUsers" role="tab" data-toggle="tab" onclick="openTab('fromUsers','');">Admin Users</a></li>  
                        <?php
                    } else {
                        if (isset($rolePermitions['configuration']) && in_array('view', $rolePermitions['configuration']) && isset($rolePermitions['admin_users']) && in_array('list', $rolePermitions['admin_users'])) {
                            ?>                       
                            <li class="<?php echo $generalActive; ?>" id="general"><a data-toggle="tab" href="#general" aria-controls="general" role="tab" data-toggle="tab" onclick="openTab('','');">General Settings</a></li> 
                            <li id="adminUsers" class="<?php echo $userActive; ?>"><a data-toggle="tab" href="#adminUsers" aria-controls="adminUsers" role="tab" data-toggle="tab" onclick="openTab('fromUsers','');">Admin Users</a></li>  
                            <?php
                        }
                    }
                    ?>

                </ul>
            </header>
            <div class="panel-body">
                <div class="tab-content">
                    <?php
                    $active = ' active';
                    if (Yii::$app->user->id != '1' && (isset($rolePermitions['admin_users']) && in_array('list', $rolePermitions['admin_users'])) && (!isset($rolePermitions['configuration']) || !in_array('view', $rolePermitions['configuration']))) {
                        $active = '';
                    }
                    if ($active == '') {
                        $generalActive = '';
                    }
                    ?>
                    <div id="general" class="tab-pane fade in <?php echo $generalActive; ?>">
                        <div class="row cleafix">
                            <div class="col-sm-11">
                                <?php
                                $form = ActiveForm::begin([
                                            'options' => [
                                                'name' => 'frmConfig',
                                                'id' => 'frmConfig',
                                                'class' => 'bs-example form-horizontal',
                                            ]
                                ]);
                                ?>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Tax Rate (%)</label>
                                    <div class="col-lg-9">
                                        <input type="text" value="<?php echo isset($config['tax_rate']) ? $config['tax_rate'] : ''; ?>" name="configuration[tax_rate]" class="form-control" onkeypress="return isBudget(event, $(this));" style="width: 14%;">
                                        <p>This indicates the Tax Rate for all orders</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Shipping Rate (S$) </label>
                                    <div class="col-lg-9">
                                        <span>Standard Express</span>
                                        <input type="text" value="<?php echo isset($config['shopping_rate']) ? $config['shopping_rate'] : ''; ?>" name="configuration[shopping_rate]" class="form-control" onkeypress="return isBudget(event, $(this));" style="width: 14%;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Vendor Shipping Deadline (hours) </label>
                                    <div class="col-lg-9">
                                        <input type="text" value="<?php echo isset($config['vendor_shipping_deadline']) ? $config['vendor_shipping_deadline'] : ''; ?>" name="configuration[vendor_shipping_deadline]" class="form-control" onkeypress="return isNumber(event);" style="width: 14%;">
                                        <p>If the order status is under "Processing" for more than these hours, there'll be an automatic notification to both the vendors and admins to remind them about the shipping. This notification will repeat every 24 hours untill the order has been shipped by vendor.</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">Estimated Delivery Days</label>
                                    <div class="col-lg-9">
                                        <input type="text" value="<?php echo isset($config['estimated_delivery_days']) ? $config['estimated_delivery_days'] : ''; ?>" name="configuration[estimated_delivery_days]" class="form-control" style="width: 25%;">
                                        <p>This will be shown to the customers on website.</p>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>  
                            </div>                   
                        </div>
                    </div>  
                    <div id="adminRoles" class="tab-pane fade in <?php echo $userRoleActive; ?>">     
                        <div class="search-bar">
                            <div class="row m-t-sm clearfix">
                                <div class="col-sm-6 m-b-xs ">
                                    <div class="page-counter">
                                        <?php
                                        $arr = Yii::$app->request->queryParams;
                                        $url = Url::to(['index', 'fromRoles' => '1'], true);
                                        if (isset($_GET['UserRolesSearch'])) {
                                            foreach ($_GET['UserRolesSearch'] as $key => $val) {
                                                if (strpos($url, '?') > 0) {
                                                    $url .= '&UserRolesSearch[' . $key . ']=' . $val;
                                                } else {
                                                    $url .= '?UserRolesSearch[' . $key . ']=' . $val;
                                                }
                                            }
                                        }
                                        $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                        echo Helper::paginationHtml($page, $url);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 m-b-xs text-right">
                                    <?php
                                    if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('add', $rolePermitions['admin_users'])) {
                                        echo Html::a('Add Role', ['user-role'], ['class' => 'btn custom-btn']);
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <?=
                            GridView::widget([
                                'dataProvider' => $roleDataProvider,
                                'filterModel' => $roleSearchModel,
                                'layout' => "{items}\n{summary}\n{pager}",
                                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                                'columns' => [

                                    [
                                        'attribute' => 'name',
                                        'value' => 'name',
                                        'label' => 'Role Title',
                                    ],
                                    [
                                        'options' => ['style' => 'width:12%;'],
                                        'attribute' => 'status',
                                        'value' => function ($roleSearchModel) {
                                    return $roleSearchModel->status == 1 ? 'Active' : 'Inactive';
                                },
                                        'filter' => Html::DropDownList('UserRolesSearch[status]', $roleSearchModel->status, array("1" => "Active", "0" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
                                    ],
                                    ['class' => 'yii\grid\ActionColumn',
                                        'header' => 'Action',
                                        'contentOptions' => ['class' => 'action'],
                                        'template' => '{update}',
                                        'buttons' => ['update' => function ($url, $model, $key) {
                                                $rolePermitions = Helper::getRolePermission();
                                                if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('view', $rolePermitions['admin_users'])) {
                                                    return Html::a('<span class="fa fa-pencil"></span>', ['user-role', 'id' => $model->id], ['title' => 'View']);
                                                }
                                            }
                                                ],
                                            ],
                                        ],
                                    ]);
                                    ?>
                                </div>

                            </div>
                            <?php
                            $active = '';
                            if (Yii::$app->user->id != '1' && (isset($rolePermitions['admin_users']) && in_array('list', $rolePermitions['admin_users'])) && (!isset($rolePermitions['configuration']) || !in_array('view', $rolePermitions['configuration']))) {
                                $active = 'active';
                            }
                            if ($active != '' && Yii::$app->user->id != '1') {
                                $userActive = 'active';
                            }
                            ?>
                            <div id="adminUsers" class="tab-pane fade in <?php echo $userActive; ?>">
                                <div class="search-bar">
                                    <div class="row m-t-sm clearfix">
                                        <div class="col-sm-6 m-b-xs ">
                                            <div class="page-counter">
                                                <?php
                                                $arr = Yii::$app->request->queryParams;
                                                $url = Url::to(['index', 'fromRoles' => '1'], true);
                                                if (isset($_GET['UserSearch'])) {
                                                    foreach ($_GET['UserSearch'] as $key => $val) {
                                                        if (strpos($url, '?') > 0) {
                                                            $url .= '&UserSearch[' . $key . ']=' . $val;
                                                        } else {
                                                            $url .= '?UserSearch[' . $key . ']=' . $val;
                                                        }
                                                    }
                                                }
                                                $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                                echo Helper::paginationHtml($page, $url);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 m-b-xs text-right">
                                            <?php
                                            if (Yii::$app->user->id == '1' || isset($rolePermitions['admin_users']) && in_array('add', $rolePermitions['admin_users'])) {
                                                echo Html::a('Add Admin User', ['admin-user'], ['class' => 'btn custom-btn']);
                                            }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <?=
                                    GridView::widget([
                                        'dataProvider' => $userDataProvider,
                                        'filterModel' => $userSearchModel,
                                        'layout' => "{items}\n{summary}\n{pager}",
                                        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
                                        'columns' => $columnArr,
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
                        <?php
                        if (Yii::$app->user->id == '1' || isset($rolePermitions['configuration']) && in_array('edit', $rolePermitions['configuration']) && in_array('view', $rolePermitions['configuration'])) {
                            if ($fromUsers > 0) {
                                
                            } else if ($fromRoles > 0) {
                                
                            } else {
                                $form = 'frmConfig';

                                echo Html::a('Save Configuration', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'saveForm("' . $form . '","")']);
                            }
                        }
                        ?>
                    </div>
                </section>
                <script language="Javascript">
                <?php /* if ($fromUsers > 0) { ?>
                  $('ul.nav-tabs li').removeClass('active');
                  $('ul.nav-tabs li#adminUsers').addClass('active');
                  $('.tab-content .tab-pane').removeClass('active');
                  $('.tab-content .tab-pane').removeClass('in');
                  $('.tab-content #adminUsers').addClass('active');
                  $('.tab-content #adminUsers').addClass('in');
                  <?php
                  }
                  if ($fromRoles > 0) {
                  ?>
                  $('ul.nav-tabs li').removeClass('active');
                  $('ul.nav-tabs li#adminRoles').addClass('active');
                  $('.tab-content .tab-pane').removeClass('active');
                  $('.tab-content .tab-pane').removeClass('in');
                  $('.tab-content #adminRoles').addClass('active');
                  $('.tab-content #adminRoles').addClass('in');
                  <?php
                  }
                 */ ?>
</script>  