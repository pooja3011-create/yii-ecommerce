<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $postArr app\models\Products */


$postArr = $model;
$this->title = 'Add Slide';

if (isset($_POST['Slider'])) {
    $postArr = $_POST['Slider'];
}
$rolePermitions = Helper::getRolePermission();
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'addSlider',
                    'id' => 'addSlider',
                    'class' => 'bs-example form-horizontal',
                    'enctype' => 'multipart/form-data'
                ]
    ]);
    ?>
    <div class="panel-body">
        <div class="col-sm-8">
            <div class="form-group">
                <label class="col-lg-3 control-label">Slide Title</label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <input type="text" name="Slider[title]" class="form-control" value="<?php echo (isset($postArr['title']) && $postArr['title'] != '') ? $postArr['title'] : ''; ?>"/>
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label">Image</label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <input type="file" name="Slider[image]"/>
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label">Slide Link</label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <input type="text" name="Slider[link]" class="form-control" value="<?php echo (isset($postArr['link']) && $postArr['link'] != '') ? $postArr['link'] : ''; ?>"/>
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label">Status</label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <select name="Slider[status]" class="form-control">
                            <option value="1" <?php echo (isset($postArr['status']) && $postArr['status'] == '1') ? 'selected="selected"' : ''; ?>>Active</option>
                            <option value="0" <?php echo (isset($postArr['status']) && $postArr['status'] == '0') ? 'selected="selected"' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>          
        </div>       

    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        
        echo Html::submitButton('Save Slide', ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Back', ['slider/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



