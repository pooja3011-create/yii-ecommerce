<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Vendors;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VendorsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendors';
$this->params['breadcrumbs'][] = $this->title;
$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'options' => ['style' => 'width:8%;'],
        'attribute' => 'vendor_code',
        'label' => 'Vendor ID',
    ],
    'name',
    [
        'attribute' => 'email',
        'label' => 'Email',
    ],
    [
        'attribute' => 'phone',
        'label' => 'Phone Number',
    ],
    [
        'attribute' => 'country_id',
        'label' => 'Country',
        'value' => function ($searchModel) {
            $name = Vendors::getCountry($searchModel->country_id);
            return $name['name'];
        },
        'filter' => Html::DropDownList('VendorsSearch[country_id]', $searchModel->country_id, $countryArr, array('class' => 'form-control', 'prompt' => 'View All')),
//                    'filter' => $countryArr,
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'created_date',
        'label' => 'Vendor Since',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'status',
        'value' => function ($searchModel) {
    return $searchModel->status == 1 ? 'Active' : 'Inactive';
},
        'filter' => Html::DropDownList('VendorsSearch[status]', $searchModel->status, array("1" => "Active", "0" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
    ],
];
if (Yii::$app->user->id == '1' || isset($rolePermitions['vendors']) && in_array('view', $rolePermitions['vendors'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Action',
        'contentOptions' => ['class' => 'action'],
        'template' => '{update}',
        'buttons' => ['update' => function ($url, $model, $key) {

                return Html::a('<span class="fa fa-pencil"></span>', ['update', 'id' => $model->id], ['title' => 'View']);
            }
                ],
            ];
        }
        ?>
        <!-- .vbox -->
        <section class="panel">
            <div class="action-row custom-border">
                <div class="row m-t-sm">
                    <div class="col-sm-6 m-b-xs ">
                        <div class="page-counter">
                            <?php
                            $arr = Yii::$app->request->queryParams;
                            $url = Url::to(['index'], true);
                            if (isset($_GET['VendorsSearch'])) {
                                foreach ($_GET['VendorsSearch'] as $key => $val) {
                                    if (strpos($url, '?') > 0) {
                                        $url .= '&VendorsSearch[' . $key . ']=' . $val;
                                    } else {
                                        $url .= '?VendorsSearch[' . $key . ']=' . $val;
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
                        if (Yii::$app->user->id == '1' || isset($rolePermitions['vendors']) && in_array('add', $rolePermitions['vendors'])) {
                            echo Html::a('Add Vendor', ['create'], ['class' => 'btn custom-btn']);
                        }
                        ?>

                    </div>
                </div>
            </div>

            <div class="search-bar">
                <div class="row m-t-sm clearfix">
                    <div class="col-sm-6 m-b-xs">

                    </div>

                    <div class="col-sm-6 m-b-xs text-right">

                        <?php
                        $url = Url::to(['export-vendors'], true);
                        if (isset($_GET['VendorsSearch'])) {
                            foreach ($_GET['VendorsSearch'] as $key => $val) {
                                if (strpos($url, '?') > 0) {
                                    $url .= '&VendorsSearch[' . $key . ']=' . $val;
                                } else {
                                    $url .= '?VendorsSearch[' . $key . ']=' . $val;
                                }
                            }
                        }
                        echo Html::a(Html::img('@web/images/download.png', ['height' => '30', 'width' => '30']), $url, ['title' => 'Export Vendor List', 'target' => '_blank']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}\n{summary}\n{pager}",
            'filterModel' => $searchModel,
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => '  --  '],
            'columns' => $columnArr,
        ]);
        ?>
    </div>

    <div class="table-legends">
        <div class="icon-box view">
            <i class="fa fa-pencil"></i> View
        </div>
    </div>
</section>
<!-- /.vbox -->
