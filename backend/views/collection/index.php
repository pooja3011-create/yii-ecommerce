<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CollectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Collections';
$this->params['breadcrumbs'][] = $this->title;

$columnArr = [

    'title',
    [
        'label' => 'Image',
        'format' => 'html',
        'value' => function ($data) {
            return (empty($data["image"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/collection/' . $data["image"], ['width' => '70px', 'height' => '60']);
        },
            ],
                [
                    'attribute' => 'product_count',
                    'label'=>'Product Count',
                    'value'=>function ($data) { 
            return $data['product_count'];
                    }
                ],
            [
                'options' => ['style' => 'width:12%;'],
                'attribute' => 'status',
                'value' => function ($searchModel) {
            return $searchModel['status'] == 1 ? 'Active' : 'Inactive';
        },
                'filter' => Html::DropDownList('CollectionSearch[status]', $searchModel['status'], array("1" => "Active", "0" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
            ],
            ['class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'contentOptions' => ['class' => 'action'],
                'template' => '{edit} {delete}',
                'buttons' => [
                    'edit' => function ($url, $model, $key) {

                        return Html::a('<span class="fa fa-pencil"></span>', ['update', 'id' => $model['id']], ['title' => 'Edit']);
                    },
                            'delete' => function ($url, $model, $key) {
                        return Html::a('<span class="fa fa-times"></span>', ['delete', 'id' => $model['id']], ['onclick' => 'javascript:return confirm("'.Yii::$app->params['removeCollectionConf'].'");', 'title' => 'Approved']);
                    }]
                    ],
                ];
                $rolePermitions = Helper::getRolePermission();
                ?>
                <!-- .vbox -->
                <section class="panel">
                    <div class="action-row custom-border">
                        <div class="row m-t-sm clearfix">
                            <div class="col-sm-6 m-b-xs ">
                            </div>
                            <div class="col-sm-6 m-b-xs text-right">
                                <?php
                                if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('add', $rolePermitions['products'])) {
                                    echo Html::a('Add Collection', ['create'], ['class' => 'btn custom-btn']);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="search-bar">
                        <div class="row m-t-sm clearfix">
                            <div class="col-sm-6 m-b-xs ">
                                <div class="page-counter">
                                    <?php
                                    $arr = Yii::$app->request->queryParams;
                                    $url = Url::to(['index'], true);
                                    if (isset($_GET['CollectionSearch'])) {
                                        foreach ($_GET['CollectionSearch'] as $key => $val) {
                                            if (strpos($url, '?') > 0) {
                                                $url .= '&CollectionSearch[' . $key . ']=' . $val;
                                            } else {
                                                $url .= '?CollectionSearch[' . $key . ']=' . $val;
                                            }
                                        }
                                    }
                                    $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                    echo Helper::paginationHtml($page, $url);
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-6 m-b-xs"></div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'layout' => "{items}\n{summary}\n{pager}",
                            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' -- '],
                            'columns' => $columnArr,
                        ]);
                        ?>

    </div>
    <div class="table-legends">
        <div class="icon-box">
            <i class="fa fa-pencil"></i> Edit
        </div>

        <div class="icon-box disapproved">
            <i class="fa fa-times"></i> Delete
        </div>

    </div>
</section>
<!-- /.vbox -->
