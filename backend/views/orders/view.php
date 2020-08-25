<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'vendor_id',
            'product_id',
            'order_date',
            'amount',
            'estimate_delivery_date',
            'actual_delivery_date',
            'status',
            'updated_date',
            'guest_checkout',
            'billing_address1',
            'billing_address2',
            'billing_city',
            'billing_state',
            'billing_country',
            'billing_zip',
            'billing_phone',
            'shipping_address1',
            'shipping_address2',
            'shipping_city',
            'shipping_state',
            'shipping_country',
            'shipping_zip',
            'shipping_phone',
            'promocode',
            'invoice_id',
            'invoice_date',
            'updated_by',
        ],
    ]) ?>

</div>
