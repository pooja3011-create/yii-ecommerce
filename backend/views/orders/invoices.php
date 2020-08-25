
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

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
$userType = '';
if (isset($_GET['UserSearch']['user_type']) && $_GET['UserSearch']['user_type'] != '') {
    $userType = $_GET['UserSearch']['user_type'];
}
$helper = new Helper();
$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'options' => ['style' => 'width:7%;'],
        'attribute' => 'invoice_id',
        'label' => 'Invoice ID',
    ],
    [
        'options' => ['style' => 'width:7%;'],
        'attribute' => 'id',
        'label' => 'Order ID',
    ],
    [
        'options' => ['style' => 'width:15%;'],
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
        'attribute' => 'invoice_date',
        'label' => 'Invoice Date',
        'format' => ['date', 'php:d/m/Y'],
    ]
];
if (Yii::$app->user->id == '1' || isset($rolePermitions['invoices']) && in_array('view', $rolePermitions['invoices'])) {
    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
        'header' => 'Action',
        'contentOptions' => ['class' => 'action'],
        'template' => '{download} {email}',
        'buttons' => ['download' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-download"></span>', ['download-invoice', 'id' => $model['id']]);
            },
                    'email' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-envelope"></span>', ['send-invoice', 'id' => $model['id']]);
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
            <i class="fa fa-download"></i> Download PDF
        </div>
        <div class="icon-box view">
            <i class="fa fa-envelope"></i> Send Via Email
        </div>
    </div>
</section>


