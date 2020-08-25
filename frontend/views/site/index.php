<?php

//use Yii;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Boucle';
if (!empty($sliderArr)) {
    ?>
    <!-- BANNER SECTION STARTS HERE-->
    <section class="banner-section">
        <div class="owl-one owl-carousel owl-theme">
            <?php
            foreach ($sliderArr as $slider) {
                if ($slider['link'] != '') {
                    ?>
                    <a href="<?php echo $slider['link']; ?>" target="_blank">
                        <div class="item"><img src="<?php echo Yii::$app->params['imgPath'] . 'slider/' . $slider['image'] ?>" alt="<?php echo $slider['title']; ?>"></div>
                    </a>
                    <?php
                } else {
                    if ($slider['image'] != '' && file_exists(Yii::getAlias('@backend') . '/web/images/slider/' . $slider['image'])) {
                        $image = Yii::$app->params['imgPath'] . 'slider/' . $slider['image'];
                    } else {
                        $image = Yii::$app->params['imgPath'] . 'banner-image.jpg';
                    }
                    ?><div class="item"><img src="<?php echo $image; ?>" alt="<?php echo $slider['title']; ?>"></div><?php
                }
            }
            ?>
        </div>

    </section>
    <!-- BANNER SECTION STARTS ENDS-->
    <?php
}

if (!empty($newProductArr)) {
    ?>
    <!-- WHATS NEW SECTION STARTS HERE-->
    <section class="whatsnew-section">
        <div class="container">
            <div class="row clearfix">
                <div class="col-sm-12">
                    <div class="section-title">
                        <h2>What’s New</h2>

                    </div>
                    <div class="whatsnew-slider">
                        <div id="whatsnew" class="owl-carousel owl-theme">
                            <?php
                            foreach ($newProductArr as $product) {
                                if ($product['featured_image'] != '' && file_exists(Yii::getAlias('@backend') . '/web/images/products/' . $product['featured_image'])) {
                                    $productImg = Yii::$app->params['imgPath'] . 'products/' . $product['featured_image'];
                                } else {
                                    $productImg = Yii::$app->params['imgPath'] . 'no_image.png';
                                }
                                ?>
                                <div class="item">
                                    <div class="prolist-image">
                                        <img src="<?php echo $productImg; ?>" alt="">
                                        <div class="prolist-hover">
                                            <div class="prolist-icons">
                                                <ul>
                                                    <li><a href="#"><i class="flaticon-shopping-cart-1"></i></a></li>
                                                    <li><a href="#"><i class="flaticon-heart"></i></a></li>
                                                    <li><a href="#"><i class="flaticon-eye"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="prolist-name"><a href="#"><?php echo $product['name']; ?></a></div> 
                                    <div class="prolist-price"><?php echo $product['display_currency'] . number_format($product['display_price'], 2); ?></div> 
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- WHATS NEW SECTION ENDS HERE-->
    <?php
}


if (!empty($collectionArr)) {
    ?>
    <!-- SHOP BY SECTION STARTS HERE-->
    <section class="shopby-section">
        <div class="container">
            <div class="row clearfix">
                <?php
                foreach ($collectionArr as $collection) {
                    if ($collection['image'] != '' && file_exists(Yii::getAlias('@backend') . '/web/images/collection/' . $collection['image'])) {
                        $collectionImg = Yii::$app->params['imgPath'] . 'collection/' . $collection['image'];
                    } else {
                        $collectionImg = Yii::$app->params['imgPath'] . 'no_image.png';
                    }
                    ?>
                    <div class="col-sm-6">
                        <div class="shop-image"><a href="#"><img src="<?php echo $collectionImg; ?>" alt=""></a></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>
    <!-- SHOP BY SECTION ENDS HERE-->
    <?php
}

if (isset($instagramFeedArr->data) && !empty($instagramFeedArr->data)) {
    ?>
    <!-- INSTA SECTION STARTS HERE-->
    <section class="insta-section">
        <div class="container">
            <div class="row clearfix">
                <div class="col-sm-12">
                    <div class="section-title">
                        <h2>#bouclesg</h2>
                        <h5>View Gallery & Get Inspired</h5>
                    </div>
                    <div class="insta-slider">
                        <div id="insta" class="owl-carousel owl-theme">

                            <?php
                            foreach ($instagramFeedArr->data as $insta) {
                                ?>
                                <div class="item">
                                    <div class="prolist-image">
                                        <img src="<?php echo $insta->images->low_resolution->url; ?>" alt="">
                                        <div class="prolist-hover">
                                            <div class="prolist-icons">
                                                <ul>
                                                    <li><i class="flaticon-heart-1"></i><?php echo $insta->likes->count; ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php
                            }
                            ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- INSTA NEW SECTION ENDS HERE-->
    <?php
}
?>


