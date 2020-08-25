<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $postArr app\models\Products */


$postArr = $model;
$this->title = 'Add Collection';

if (isset($_POST['Collection'])) {
    $postArr = $_POST['Collection'];
}
$rolePermitions = Helper::getRolePermission();
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'addCollection',
                    'id' => 'addCollection',
                    'class' => 'bs-example form-horizontal',
                    'enctype' => 'multipart/form-data'
                ]
    ]);
    ?>
    <div class="panel-body">
        <div class="col-sm-12">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-lg-4 control-label">Collection Name</label>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input type="text" name="Collection[title]" class="form-control" value="<?php echo (isset($postArr['title']) && $postArr['title'] != '') ? $postArr['title'] : ''; ?>"/>
                        </div>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-lg-4 control-label">Image</label>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <input type="file" name="Collection[image]"/>
                        </div>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-lg-4 control-label">Status</label>
                    <div class="col-lg-8">
                        <div class="form-group">
                            <select name="Collection[status]" class="form-control">
                                <option value="1" <?php echo (isset($postArr['status']) && $postArr['status'] == '1') ? 'selected="selected"' : ''; ?>>Active</option>
                                <option value="0" <?php echo (isset($postArr['status']) && $postArr['status'] == '0') ? 'selected="selected"' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-sm-6">
                <div class="col-lg-6">
                </div>
<!--                <div style="text-align: center;border: 1px solid #dddddd;" class="col-lg-6">
                    <h3>Collection Image</h3>
                </div>-->
            </div>
        </div>       
        <div class="col-sm-12" style="border-bottom: 0px;border-top:  1px solid #ddd;">
            <h4>Products</h4>
            <div class="col-lg-4">
                <select class="form-control" onchange="javascript:getVendor($(this));" id="productCategory">
                    <option value="">Select Category</option>
                    <?php
                    if (!empty($categoryArr)) {
                        foreach ($categoryArr as $key => $val) {
                            ?> <option value="<?php echo $key; ?>"><?php echo $val; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-3">
                <select class="form-control" id="productVendor">
                    <option value="">Select Vendor</option>
                </select>
            </div>
            <div class="col-lg-3">
                <input type="text" name="product_name" id="productName" class="form-control"/>
                <input type="hidden" name="product_id" id="productId" class="form-control"/>
                <div id="suggesstion-box"></div>
            </div>
            <div class="col-lg-2"> 
                <?php echo Html::a('Add', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'addProduct()']); ?>
            </div>
            <div id="test">testtttt</div>
        </div>        
        <div class="col-sm-12">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="collectionProducts">

                </tbody>
            </table>
        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        echo Html::a('Delete', ['collection/delete'], ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::submitButton('Save Collection', ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Back', ['collection/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



