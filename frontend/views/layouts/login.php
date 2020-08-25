<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

//AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <!-- CSS STARTS HERE -->
        <?php
        $this->registerCssFile('@web/css/bootstrap.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/check-radio.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/flaticon.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/select2.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/owl.carousel.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/owl.theme.default.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/style-en.css', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerCssFile('@web/css/media-en.css', ['position' => \yii\web\View::POS_HEAD]);
        ?>



        <!-- ie8 fixes -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.js"></script>
        <![endif]-->

    </head>
    <body>
        <?php $this->beginBody() ?>
        <!-- wrapper starts here-->
        <div id="wrapper">
            <div class="container">
                <div class="row clearfix"> 
                    <div class="col-sm-12">
                        <div class="signin-section">
                            <div class="signin-logo"><a href="#"><img src="images/signin-logo.png" alt=""></a></div>

                            <?= Alert::widget() ?>
                            <?= $content ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- wrapper ends here-->
        <!-- JAVASCRIPT STARTS HERE -->
        <?php
        $this->registerJsFile('@web/js/jquery-1.11.1.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/bootstrap.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/owl.carousel.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/select2.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/common.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/modernizr.custom.js', ['position' => \yii\web\View::POS_HEAD]);
        ?>
    </body>
</html>
<?php $this->endPage() ?>
