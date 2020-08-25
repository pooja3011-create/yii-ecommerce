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

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
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
        $this->registerCssFile('@web/css/app.css', ['position' => \yii\web\View::POS_HEAD]);
        ?>
        <!--[if lt IE 9]>
  <script src="js/ie/respond.min.js" cache="false"></script>
  <script src="js/ie/html5.js" cache="false"></script>
  <script src="js/ie/fix.js" cache="false"></script>
<![endif]-->
    </head>
    <body>
        <?php $this->beginBody() ?>
        <section class="hbox stretch">
            <!-- .aside -->
            <aside class="bg-success dker aside-sm nav-vertical" id="nav">
                <section class="vbox">
                    <header class="bg-black nav-bar">
                        <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen" data-target="#nav">
                            <i class="fa fa-bars"></i>
                        </a>
                        <a href="#" class="nav-brand" data-toggle="fullscreen"><img src="images/index-logo.png"></a>
                        <a class="btn btn-link visible-xs" data-toggle="collapse" data-target=".navbar-collapse">
                            <i class="fa fa-comment-o"></i>
                        </a>
                    </header>
                    <section>
                        <!-- nav -->
                        <nav class="nav-primary hidden-xs">
                            <ul class="nav">
                                <li>
                                    <a href="<?php echo URL::to(['/site'], true); ?>">
                                        <i class="fa fa-eye"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>              
                                <li class="dropdown-submenu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-flask"></i>
                                        <span>Vendors</span>
                                    </a>
                                    <ul class="dropdown-menu">                
                                        <li>
                                            <a href="<?php echo URL::to(['/vendors'], true); ?>">Vendors</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo URL::to(['/vendors'], true); ?>">Vendor Payments</a>
                                        </li>
                                    </ul>
                                </li>
                                 <li>
                                    <a href="<?php echo URL::to(['/products'], true); ?>">
                                        <i class="fa fa-eye"></i>
                                        <span>Products</span>
                                    </a>
                                </li>   

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
            <?php /** page content start */?>
            <?= Alert::widget() ?>
            <?= $content ?>
            <?php /** page content end */?>
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
        $this->registerJsFile('@web/js/jquery-ui.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/custom.js', ['position' => \yii\web\View::POS_END]);
        ?>    
    </body>
</html>
<?php $this->endPage() ?>