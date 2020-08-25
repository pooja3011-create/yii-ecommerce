<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VendorPaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vendor Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-payment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Vendor Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'order_id',
            'payment_date',
            'payment_ref_number',
            'notes:ntext',
            // 'created_date',
            // 'updated_date',
            // 'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
