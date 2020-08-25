<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Vendors;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Consumers';
$this->params['breadcrumbs'][] = $this->title;
$userType = '';
if (isset($_GET['UserSearch']['user_type']) && $_GET['UserSearch']['user_type'] != '') {
    $userType = $_GET['UserSearch']['user_type'];
}
$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'attribute' => 'user_code',
        'options' => ['style' => 'width:2%;'],
        'value' => 'user_code',
        'label' => 'Consumer ID'
    ],
    [
        'options' => ['style' => 'width:20%;'],
        'attribute' => 'user_type',
        'label' => 'Type',
        'value' => function ($searchModel) {

    return ucwords(str_replace('_', ' ', str_replace('`', '', $searchModel['user_type'])));
},
        'filter' => Html::DropDownList('UserSearch[user_type]', $userType, ['registered' => 'Registered', 'guest_user' => 'Guest User'], array('class' => 'form-control', 'prompt' => 'View All')),
    ],
    [
        'options' => ['style' => 'width:20%;'],
        'attribute' => 'first_name',
        'value' => 'first_name',
        'label' => 'Name'
    ],
    [
        'options' => ['style' => 'width:25%;'],
        'attribute' => 'email',
        'value' => 'email',
    ],
    [
        'options' => ['style' => 'width:20%;'],
        'attribute' => 'phone',
        'value' => 'phone',
        'label' => 'Phone Number'
    ],
    [
        'options' => ['style' => 'width:25%;'],
        'attribute' => 'gender',
        'value' => function ($searchModel) {

    return ucwords($searchModel['gender']);
},
        'filter' => Html::DropDownList('UserSearch[gender]', $searchModel['gender'], ['male' => 'Male', 'female' => 'Female'], array('class' => 'form-control', 'prompt' => 'View All')),
    ],    
    [
        'options' => ['style' => 'width:20%;'],
        'attribute' => 'billing_country',
        'label' => 'Country',
        'value' => function ($searchModel) {
    $name = Vendors::getCountry($searchModel['billing_country']);
    return $name['name'];
},
        'filter' => Html::DropDownList('UserSearch[billing_country]', $searchModel['billing_country'], $countryArr, array('class' => 'form-control', 'prompt' => 'View All')),
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'created_at',
        'label' => 'Joined',
        'format' => ['date', 'php:d/m/Y'],
    ],
];
if (Yii::$app->user->id == '1' || isset($rolePermitions['consumers']) && in_array('view', $rolePermitions['consumers'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Actions',
        'contentOptions' => ['class' => 'action'],
        'template' => ' {view}',
        'buttons' => [
            'view' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', ['update', 'id' => $model['id'], 'type' => str_replace('`', '', $model['user_type'])], ['title' => 'View']);
            }
                ],
            ];
        }
        ?>
        <section class="panel">
            <div class="action-row custom-border">
                <div class="row m-t-sm">
                    <div class="col-sm-6 m-b-xs ">
                        <div class="page-counter">
                            <?php
                            $arr = Yii::$app->request->queryParams;
                            $url = Url::to(['index'], true);
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
                       
                        $url = Url::to(['export-customers'], true);
                        if (isset($_GET['UserSearch'])) {
                            foreach ($_GET['UserSearch'] as $key => $val) {
                                if (strpos($url, '?') > 0) {
                                    $url .= '&UserSearch[' . $key . ']=' . $val;
                                } else {
                                    $url .= '?UserSearch[' . $key . ']=' . $val;
                                }
                            }
                        }
                        echo Html::a(Html::img('@web/images/download.png' , ['height' => '30', 'width' => '30']), $url, ['title' => 'Export Consumer List','target'=>'_blank']);
                        ?>
                    </div>
                </div>
            </div>

            <div class="search-bar">
                <div class="row m-t-sm clearfix">
                    <div class="col-sm-6 m-b-xs">

                    </div>
                    <div class="col-sm-6 m-b-xs">
                        <?php
                        $form = ActiveForm::begin([
                                    'action' => ['index'],
                                    'method' => 'get',
                        ]);
                        ?><div class="input-group">


                        </div>
                        <?php ActiveForm::end(); ?>
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
        <div class="icon-box view">
            <i class="fa fa-eye"></i> View
        </div>
    </div>
</section>

