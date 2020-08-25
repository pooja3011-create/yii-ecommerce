<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductReviewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Reviews';
$this->params['breadcrumbs'][] = $this->title;

$rolePermitions = Helper::getRolePermission();
$columnArr = [
        [
            'label' => 'Image',
            'format' => 'html',
            'value' => function ($data) {
                return (empty($data["pImage"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/products/' . $data["pImage"], ['width' => '70px', 'height' => '60']);
            },
                ],
                [
                    'attribute' => 'productName',
                    'label' => 'Product',
                    'value' => 'pName',
                ],
                [
                    'attribute' => 'vendorName',
                    'label' => 'Vendor',
                    'value' => 'vShopName',
                ],
                [
                    'options' => ['style' => 'width:5%;'],
                    'attribute' => 'reviewRating',
                    'label' => 'Rating',
                    'value' => function ($data) {
                return $data['prRating'] . "/5";
            },
                ],
                [
                    'label' => 'Review',
                    'value' => 'prReview',
                ],
                [
                    'attribute' => 'consumerName',
                    'label' => 'Consumer',
                    'value' => 'uName',
                ],
                [
                    'options' => ['style' => 'width:10%;'],
                    'label' => 'Date',
                    'attribute' => 'prDate',
                    'value' => 'prDate',
                    'format' => ['date', 'php:d/m/Y'],
                /* 'value' => function ($data) {
                  return date_format(new DateTime($data['prDate']), 'd M Y');
                  }, */
                ],
             
            ];
if (Yii::$app->user->id == '1' || isset($rolePermitions['product_reviews']) && in_array('delete', $rolePermitions['product_reviews'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Actions',
        'contentOptions' => ['class' => 'action'],
        'template' => '{delete}',
        'buttons' => [
            'delete' => function ($url, $model, $key) {

                return '<a href="/boucle/backend/web/index.php?r=product-reviews%2Fdelete&amp;id=' . $model["prId"] . '" title="Delete" aria-label="Delete" data-pjax="' . $model["prId"] . '" data-confirm="'.Yii::$app->params['removePrdReviewConfirmation'].'" data-method="post"><span class="fa fa-times"></span></a>';
            }],
    ];
}
?>
<section class="panel">

    <div class="search-bar">
        <div class="row m-t-sm clearfix">
            <div class="col-sm-6 m-b-xs ">
                <div class="page-counter">
<?php
$arr = Yii::$app->request->queryParams;
$url = Url::to(['index'], true);
if (isset($_GET['ProductsSearch'])) {
    foreach ($_GET['ProductsSearch'] as $key => $val) {
        if (strpos($url, '?') > 0) {
            $url .= '&ProductsSearch[' . $key . ']=' . $val;
        } else {
            $url .= '?ProductsSearch[' . $key . ']=' . $val;
        }
    }
}
$page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
echo Helper::paginationHtml($page, $url);
?>
                </div>
            </div>
            <div class="col-sm-6 m-b-xs">
            </div>
        </div>
    </div>
    <div class="table-responsive">
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'layout' => "{items}\n{summary}\n{pager}",
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
    'columns' => $columnArr,
        ]);
        ?>

    </div>

    <div class="table-legends">
        <div class="icon-box disapproved">
            <i class="fa fa-times"></i> Remove
        </div>
    </div>




</section>
