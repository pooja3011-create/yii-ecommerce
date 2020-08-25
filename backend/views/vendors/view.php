<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Vendors */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Vendors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendors-view">

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
            'vendor_code',
            'name',
            'email:email',
            'phone',
            'gender',
            'password',
            'status',
            'shop_name',
            'country_id',
            'shop_description:ntext',
            'tax_vat_number',
            'commission_type',
            'commission_rate:ntext',
            'product_approval',
            'shop_banner_image',
            'shop_logo_image',
            'bank_name',
            'account_number',
            'account_holder_name',
            'swift_code',
            'account_notes:ntext',
            'created_date',
            'updated_date',
            'updated_by',
            'device_type',
            'device_id',
            'access_token',
        ],
    ]) ?>

</div>
