
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

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
$userType = '';
if (isset($_GET['UserSearch']['user_type']) && $_GET['UserSearch']['user_type'] != '') {
    $userType = $_GET['UserSearch']['user_type'];
}
$helper = new Helper();
$orderStatus = $helper->getOrderStatus();
$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'options' => ['style' => 'width:7%;'],
        'attribute' => 'id',
        'label' => 'Order ID',
    ],
    [
        'options' => ['style' => 'width:11%;'],
        'attribute' => 'order_date',
        'label' => 'Order Date',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'first_name',
        'value' => 'first_name',
        'label' => 'Consumer',
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'phone',
        'value' => 'phone',
        'label' => 'Consumer Phone',
    ],
    [
        'attribute' => 'amount',
        'label' => 'Total',
        'value' => function ($orderSearchModel) {
            return 'S$' . $orderSearchModel['amount'];
        },
    ],
    [
        'attribute' => 'estimate_delivery_date',
        'label' => 'Est. Delivery Date',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'attribute' => 'updated_date',
        'label' => 'Last Updated',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'options' => ['style' => 'width:6%;'],
        'attribute' => 'guest_checkout',
        'label' => 'Guest Checkout?',
        'value' => function ($searchModel) {
    return $searchModel['guest_checkout'] == 1 ? 'Yes' : 'No';
},
        'filter' => Html::DropDownList('OrdersSearch[guest_checkout]', $searchModel['guest_checkout'], array("1" => "Yes", "0" => "No"), array('class' => 'form-control', 'prompt' => 'View All')),
    ],
    [
        'options' => ['style' => 'width:12%;'],
        'attribute' => 'status',
        'value' => function ($searchModel) {

            $orderStatus = Helper::getOrderStatus();
            $status = $orderStatus[$searchModel['status']];
            return $status;
        },
        'filter' => Html::DropDownList('OrdersSearch[status]', $searchModel['status'], $orderStatus, array('class' => 'form-control', 'prompt' => 'View All')),
//                                                                'filter' => $orderStatus,
    ],
];
if (Yii::$app->user->id == '1' || isset($rolePermitions['orders']) && in_array('view', $rolePermitions['orders'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Action',
        'contentOptions' => ['class' => 'action'],
        'template' => '{update}',
        'buttons' => ['update' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-eye"></span>', ['order-detail', 'id' => $model['id']],['title'=>'View']);
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
                            if (isset($_GET['OrdersSearch'])) {
                                foreach ($_GET['OrdersSearch'] as $key => $val) {
                                    if (strpos($url, '?') > 0) {
                                        $url .= '&OrdersSearch[' . $key . ']=' . $val;
                                    } else {
                                        $url .= '?OrdersSearch[' . $key . ']=' . $val;
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
                        $url = Url::to(['export-orders'], true);
                        if (isset($_GET['OrdersSearch'])) {
                            foreach ($_GET['OrdersSearch'] as $key => $val) {
                                if (strpos($url, '?') > 0) {
                                    $url .= '&OrdersSearch[' . $key . ']=' . $val;
                                } else {
                                    $url .= '?OrdersSearch[' . $key . ']=' . $val;
                                }
                            }
                        }
                        echo Html::a(Html::img('@web/images/download.png', ['height' => '30', 'width' => '30']), $url, ['title' => 'Export Order List', 'target' => '_blank']);
                        ?>
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


