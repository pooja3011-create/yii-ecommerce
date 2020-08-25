<?php
/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\models\User;
use common\models\Helper;

AppAsset::register($this);
?>
<?php
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8" />
<!--        <title>Web Application | todo</title>
        <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />-->
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?php
        $this->registerCssFile('@web/css/bootstrap.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/animate.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/font-awesome.min.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/font.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/plugin.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/app.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/jquery-ui.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/js/datepicker/datepicker.css', ['position' => \yii\web\View::POS_HEAD]);
        ?>
        <!--[if lt IE 9]>
  <script src="js/ie/respond.min.js" cache="false"></script>
  <script src="js/ie/html5.js" cache="false"></script>
  <script src="js/ie/fix.js" cache="false"></script>
<![endif]-->
    </head>
    <body>
        <?php $this->beginBody() ?>
        <?php
        $user = User::find()->where(['id' => Yii::$app->user->id])->One();

        $rolePermitions = Helper::getRolePermission($user->user_role);
        if (!empty($rolePermitions) && isset($rolePermitions['user']) && $rolePermitions['user'] == 'admin') {
            $rolePermitions = array();
        }
        $models = array_keys($rolePermitions);
        ?>
        <section class="hbox stretch">
            <!-- .aside -->
            <aside class="dker aside-sm nav-vertical caustom-nav" id="nav">
                <section class="vbox">
                    <header class="bg-white nav-bar">
                        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen" data-target="#nav">
                            <i class="fa fa-bars"></i>
                        </a>
                        <a href="javascript:;" class="nav-brand"><img src="images/index-logo.png"></a>
                        <a class="btn btn-link visible-xs" data-toggle="collapse" data-target=".navbar-collapse">
                            <i class="fa fa-comment-o"></i>
                        </a>
                    </header>
                    <section>
                        <!-- nav -->
                        <nav class="caustom-nav nav-primary hidden-xs">
                            <ul class="nav">
                                <?php
                                if (Yii::$app->user->id == '1' || in_array('dashboard', $models) && in_array('view', $rolePermitions['dashboard'])) {
                                    ?>
                                    <li <?php if (Yii::$app->controller->id == "site" && $this->context->action->id == "index") { ?> class="active" <?php } ?>>
                                        <a href="<?php echo URL::to(['/site'], true); ?>">
                                            <i class="flaticon-time"></i>
                                            <span>Dashboard</span>
                                        </a>
                                    </li> <?php
                                }
                                if (Yii::$app->user->id == '1' || (in_array('products', $models) && in_array('list', $rolePermitions['products'])) || (in_array('product_reviews', $models) && in_array('list', $rolePermitions['product_reviews']))) {
                                    ?>
                                    <li class="dropdown-submenu <?php
                                    if (Yii::$app->controller->id == "products" || Yii::$app->controller->id == "product-reviews") {
                                        echo 'active';
                                    }
                                    ?>" >
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="flaticon-box-1"></i>
                                            <span>Products</span>
                                        </a>
                                        <ul class="dropdown-menu">  
                                            <?php if (Yii::$app->user->id == '1' || in_array('products', $models) && in_array('list', $rolePermitions['products'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/products'], true); ?>">Products</a>
                                                </li><?php }
                                            ?>
                                            <?php if (Yii::$app->user->id == '1' || in_array('product_reviews', $models) && in_array('list', $rolePermitions['product_reviews'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/product-reviews'], true); ?>">Product Reviews</a>
                                                </li><?php }
                                            ?>

                                        </ul>
                                    </li> 
                                    <?php
                                }
                                if (Yii::$app->user->id == '1' || (in_array('vendors', $models) && in_array('list', $rolePermitions['vendors'])) || (in_array('vendor_payments', $models) && in_array('list', $rolePermitions['vendor_payments']))) {
                                    ?><li class="dropdown-submenu <?php
                                    if (Yii::$app->controller->id == "vendors") {
                                        echo 'active';
                                    }
                                    ?>">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="flaticon-salesman"></i>
                                            <span>Vendors</span>
                                        </a>
                                        <ul class="dropdown-menu">  
                                            <?php if (Yii::$app->user->id == '1' || in_array('vendors', $models) && in_array('list', $rolePermitions['vendors'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/vendors'], true); ?>">Vendors</a>
                                                </li><?php
                                            }
                                            if (Yii::$app->user->id == '1' || in_array('vendor_payments', $models) && in_array('list', $rolePermitions['vendor_payments'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/vendors/payments'], true); ?>">Vendor Payments</a>
                                                </li>
                                                <?php
                                            }
                                            ?>


                                        </ul>
                                    </li> <?php
                                }
                                 if (Yii::$app->user->id == '1' || (in_array('orders', $models) && in_array('list', $rolePermitions['orders'])) || (in_array('invoices', $models) && in_array('list', $rolePermitions['invoices']))) {
                                    ?>
                                    <li class="dropdown-submenu <?php
                                    if (Yii::$app->controller->id == "orders") {
                                        echo 'active';
                                    }
                                    ?>">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-usd"></i>
                                            <span>Orders</span>
                                        </a>
                                        <ul class="dropdown-menu">  
                                            <?php if (Yii::$app->user->id == '1' || in_array('orders', $models) && in_array('list', $rolePermitions['orders'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/orders'], true); ?>">Orders</a>
                                                </li><?php
                                            }
                                            if (Yii::$app->user->id == '1' || in_array('invoices', $models) && in_array('list', $rolePermitions['invoices'])) {
                                                ?><li>
                                                    <a href="<?php echo URL::to(['/orders/invoices'], true); ?>">Invoices</a>
                                                </li>
                                                <?php
                                            }
                                            ?>


                                        </ul>
                                    </li> <?php
                                }
                               if (Yii::$app->user->id == '1' || in_array('consumers', $models) && in_array('list', $rolePermitions['consumers'])) {
                                    ?>
                                    <li class="dropdown-submenu <?php
                                    if (Yii::$app->controller->id == "user") {
                                        echo 'active';
                                    }
                                    ?>">
                                        <a href="<?php echo URL::to(['/user'], true); ?>">
                                            <i class="flaticon-profile-1"></i>
                                            <span>Consumers</span>
                                        </a>

                                    </li> <?php
                                }
                                
                                if (Yii::$app->user->id == '1' || in_array('configuration', $models) || in_array('admin_users', $models)) {
                                    ?>
                                    <li class="dropdown-submenu <?php
                                    if (Yii::$app->controller->id == "configuration") {
                                        echo 'active';
                                    }
                                    ?>">
                                        <a href="<?php echo URL::to(['/configuration'], true); ?>">
                                            <i class="flaticon-cogwheel"></i>
                                            <span>Settings</span>
                                        </a>

                                    </li> <?php
                                }
                                ?>
                            </ul>
                        </nav>
                        <!-- / nav -->
                    </section>
                    <!--                    <footer class="footer bg-gradient hidden-xs">
                                            <a href="modal.lockme.html" data-toggle="ajaxModal" class="btn btn-sm btn-link m-r-n-xs pull-right">
                                                <i class="fa fa-power-off"></i>
                                            </a>
                                            <a href="#nav" data-toggle="class:nav-vertical" class="btn btn-sm btn-link m-l-n-sm">
                                                <i class="fa fa-bars"></i>
                                            </a>
                                        </footer>-->
                </section>
            </aside>
            <!-- /.aside -->
            <?php /** page content start */ ?>
            <section id="content">
                <section class="vbox">
                    <header class="header navbar navbar-inverse content-header">
                        <div class="collapse navbar-collapse pull-in">   
                            <div class="pull-left pagetitle-section">
                                <div class="pageheader"><?php echo $this->title; ?></div>
                                <div class="pagesubtext">Welcome to the Backend Portal</div>
                            </div> 
                            <div class="pull-right pagetitle-section">
                                <div class="pageheader">&nbsp;</div>
                                <div class="pagesubtext">
                                    <?php
                                    echo 'Welcome, ' . ucwords($user->first_name);
                                    ?> 
                                    ( <?php echo Html::a('Logout', ['/site/logout']); ?> )
                                </div>

                            </div>

                            <?php /** <ul class="nav navbar-nav navbar-right">
                              <li class="dropdown" id="accountDrp">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" >
                              <span class="thumb-sm avatar pull-left m-t-n-xs m-r-xs">
                              <img src="images/avatar.jpg">
                              </span>
                              <?php
                              $user = User::find()->where(['id' => Yii::$app->user->id])->One();
                              echo $user->first_name . ' ' . $user->last_name;
                              ?>
                              <b class="caret"></b>
                              </a>
                              <ul class="dropdown-menu animated fadeInLeft">
                              <li>


                              <!--<a href="signin.html">Logout</a>-->
                              </li>
                              </ul>
                              </li>
                              </ul> */ ?>
                        </div>
                    </header>
                    <section class="scrollable wrapper">
                        <?= Alert::widget() ?>
                        <?= $content ?>
                    </section>
                </section>
                <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
            </section>

            <?php /** page content end */ ?>
        </section>

        <?php $this->endBody() ?>
        <?php
        $this->registerJsFile('@web/js/jquery.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/bootstrap.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/charts/sparkline/jquery.sparkline.min.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.plugin.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.data.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/slimscroll/jquery.slimscroll.min.js', ['position' => \yii\web\View::POS_END]);
//       Sparkline Chart
        $this->registerJsFile('@web/js/charts/sparkline/jquery.sparkline.min.js', ['position' => \yii\web\View::POS_END]);
//        Easy Pie Chart
        $this->registerJsFile('@web/js/charts/easypiechart/jquery.easy-pie-chart.js', ['position' => \yii\web\View::POS_END]);
//        Morris
        $this->registerJsFile('@web/js/charts/morris/raphael-min.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/charts/morris/morris.min.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/jquery.validate.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/datepicker/bootstrap-datepicker.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/additional-methods.min.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/jquery-ui.js', ['position' => \yii\web\View::POS_END]);

        $this->registerJsFile('@web/js/custom.js', ['position' => \yii\web\View::POS_END]);
        ?>    
    </body>
</html>
<?php $this->endPage() ?>