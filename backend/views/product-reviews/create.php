<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ProductReviews */

$this->title = 'Create Product Reviews';
$this->params['breadcrumbs'][] = ['label' => 'Product Reviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-reviews-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
