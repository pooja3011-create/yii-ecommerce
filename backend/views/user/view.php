<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

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
            'user_code',
            'first_name',
            'last_name',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email:email',
            'phone',
            'gender',
            'billing_address1:ntext',
            'billing_address2:ntext',
            'billing_city',
            'billing_state',
            'billing_country',
            'billing_zip',
            'billing_date',
            'status',
            'created_at',
            'updated_at',
            'updated_by',
            'last_loggedin',
            'user_role',
            'social_type',
            'social_id',
        ],
    ]) ?>

</div>
