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
        ?>
        <!--[if lt IE 9]>
  <script src="js/ie/respond.min.js" cache="false"></script>
  <script src="js/ie/html5.js" cache="false"></script>
  <script src="js/ie/fix.js" cache="false"></script>
<![endif]-->
    </head>
    <body>
        <section id="content" class="m-t-lg wrapper-md animated fadeInUp">
            <a class="nav-brand login" href="javascript:;"><img src="images/login-logo.png" alt=""/></a>
            <div class="row m-n">
                 
                    <?= $content ?>
                <?php /** */ ?>
            </div>
        </section>
        <!-- footer -->
        <footer id="footer">
            <div class="text-center padder clearfix">
                <p>
                    <small>Boucle &copy; <?php echo date('Y')?></small>
                </p>
            </div>
        </footer>
        <?php $this->endBody() ?>
        <?php
        $this->registerJsFile('@web/js/jquery.min.js', ['position' => \yii\web\View::POS_HEAD]);
        $this->registerJsFile('@web/js/bootstrap.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.plugin.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/app.data.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/jquery.validate.js', ['position' => \yii\web\View::POS_END]);
        $this->registerJsFile('@web/js/custom.js', ['position' => \yii\web\View::POS_END]);
        ?>   
    </body>
</html>
<?php $this->endPage() ?>