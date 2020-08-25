<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderProductsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-products-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Order Products', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'vendor_id',
            'product_id',
            'price',
            // 'qty',
            // 'shipment_status',
            // 'carrier',
            // 'traking_number',
            // 'shipped_date',
            // 'shipment_from',
            // 'shipment_to',
            // 'shipment_note:ntext',
            // 'created_date',
            // 'updated_date',
            // 'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
