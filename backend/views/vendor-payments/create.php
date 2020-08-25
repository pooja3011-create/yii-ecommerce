<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\VendorPayment */

$this->title = 'Create Vendor Payment';
$this->params['breadcrumbs'][] = ['label' => 'Vendor Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vendor-payment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
