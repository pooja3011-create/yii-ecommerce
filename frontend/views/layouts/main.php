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
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8">
        <link rel="Shortcut Icon" href="favicon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>

        <?php $this->head() ?>
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
        <div class="menu-overlay"></div>
        <!-- wrapper starts here-->
        <div id="wrapper">
            <!-- HEADER STARTS HERE-->
            <header id="header-section">
                <div class="header-top-section">
                    <div class="container">
                        <div class="row clearfix">
                            <div class="col-sm-7 col-md-4 col-sm-offset-5 col-md-offset-8">
                                <div class="header-top-rightbox">
                                    <div class="wistlist-link"><a href="#"><i class="flaticon-heart-1"></i></a></div>
                                    <div class="cart-link"><a href="#"><i class="flaticon-shopping-bag"></i></a></div>
                                    <div class="login-links">
                                        <ul>
                                            <li><a href="#">Login</a></li>
                                            <li><a href="#">Register</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="header-bottom-sections">
                    <div class="container">
                        <div class="row clearfix">
                            <div class="col-sm-5">
                                <div class="logo"><a href="#"><img src="images/logo.png" alt=""></a></div>
                            </div>

                            <div class="col-sm-7 hidden-xs">
                                <div class="deskmenu">
                                    <ul class="nav navbar-nav">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Womens</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">Clothing</a></li>
                                                <li><a href="#">BAGS</a></li>
                                                <li><a href="#">Shoes</a></li>
                                                <li><a href="#">Accessories</a></li>
                                            </ul>
                                        </li>

                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Mens</a>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">Clothing</a></li>
                                                <li><a href="#">BAGS</a></li>
                                                <li><a href="#">Shoes</a></li>
                                                <li><a href="#">Accessories</a></li>
                                            </ul>
                                        </li>

                                        <li class="dropdown">
                                            <div class="box">
                                                <input class="search" type="search" placeholder="Search" />
                                                <div class="icon"><img src="images/search-icon.png" alt=""></div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- HEADER ENDS HERE-->
            <?= Alert::widget() ?>
            <?= $content ?>
            <div class="push"></div>
        </div>
        <!-- wrapper ends here-->
        <!-- FOOTER SECTION STARTS HERE -->
        <footer>
            <div class="footer-top">
                <div class="container"> 
                    <div class="row clearfix">
                        <div class="col-sm-3">
                            <div class="footer-link">
                                <ul>
                                    <li>About US</li>
                                    <li><a href="#">About Boucle</a></li>
                                    <li><a href="#">Terms and conditions</a></li>
                                    <li><a href="#">Privacy Policy</a></li>
                                    <li><a href="#">CONTACT US</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="footer-link">
                                <ul>
                                    <li>Customer Care</li>
                                    <li><a href="#">FAQ</a></li>
                                    <li><a href="#">RETURN &amp; EXCHANGE</a></li>
                                    <li><a href="#">SHIPPING</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-sm-5">
                            <div class="footer-link">
                                <ul>
                                    <li>Keep in Touch</li>
                                    <li class="news-text">Sign up for Boucle's newsletter to stay updated on
                                        latest promotions &amp; news</li>

                                </ul>
                                <div class="inputbox">
                                    <input type="text" placeholder="Your Email Address" />
                                    <div class="send-btn"><button type="submit"><img src="images/send-icon.png" alt=""></button></div> 
                                </div>

                                <div class="social-links">
                                    <a href="#"><img src="images/fb-icon.png" alt=""></a>
                                    <a href="#"><img src="images/insta-icon.png" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="container"> 
                    <div class="row clearfix">
                        <div class="col-sm-12">
                            <div class="copyright-text">© 2017. Boucle Pte. Ltd. </div>
                        </div>
                    </div>
                </div>    
            </div>	
        </footer>
        <!-- FOOTER SECTION ENDS HERE -->
        <?php
        $this->registerJsFile('@web/js/jquery-1.11.1.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/bootstrap.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/owl.carousel.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/common.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/modernizr.custom.js', ['position' => \yii\web\View::POS_HEAD]);
        ?>
        <?php // $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
