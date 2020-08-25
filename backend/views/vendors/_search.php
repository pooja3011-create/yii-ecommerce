<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\VendorsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vendors-search">
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>
    
    <div class="form-group">
        <input type="text" placeholder="search by id, name, email or phone number" size="100" value="<?php echo  isset($_GET['VendorsSearch']['searchAll'])?$_GET['VendorsSearch']['searchAll']:''?>" name="VendorsSearch[searchAll]" >
    </div>
    <?php ActiveForm::end(); ?>
</div>
