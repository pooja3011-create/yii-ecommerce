<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $postArr app\models\Products */

$this->title = 'Edit Slide - ' . $sliderArr['title'];

$postArr = $sliderArr;
if (isset($_POST['Slider'])) {
    $postArr = $_POST['Slider'];
}
$rolePermitions = Helper::getRolePermission();
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'editSlider',
                    'id' => 'editSlider',
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
                    <input type="hidden" name="Slider[id]" value="<?php echo (isset($postArr['id']) && $postArr['id'] != '') ? $postArr['id'] : ''; ?>"/>
                    <div class="form-group">
                        <input type="text" name="Slider[title]" class="form-control" value="<?php echo (isset($postArr['title']) && $postArr['title'] != '') ? $postArr['title'] : ''; ?>"/>
                    </div>
                </div>
            </div> 
            <div class="form-group">
                <label class="col-lg-3 control-label">Image</label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <input type="hidden" name="Slider[hdnImage]" value="<?php echo (isset($postArr['image']) && $postArr['image'] != '') ? $postArr['image'] : ''; ?>"/>
                        <input type="file" name="Slider[image]"/>
                        <label class="error" style="display: none;" for="Slider[hdnImage]">Please upload slider image.</label>
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
        <div class="col-sm-4">
            <div style="text-align: center;margin-bottom: 5px;">
                <?php
                if (isset($postArr['image']) && $postArr['image'] != '' && file_exists(Yii::$app->basePath . '/web/images/slider/' . $postArr['image'])) {
                    ?>
                    <img src="<?php echo Url::to('@web/images/slider/' . $postArr['image'], TRUE); ?>" style="max-width: 100%;height: auto;"/>                
                    <?php
                }
                ?>
            </div>

        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        echo Html::a('Delete', ['slider/delete', 'id' => $postArr['id']], ['class' => 'btn btn-primary', 'onclick' => 'javascript:return confirm("Are you sure you want to permanently remove this slide from the system?");']);
        echo '&nbsp;';
        echo Html::submitButton('Edit Slide', ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Back', ['slider/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



