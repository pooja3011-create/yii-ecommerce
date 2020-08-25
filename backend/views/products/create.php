<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$this->title = 'Add Product';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'productSave',
                    'id' => 'productSave',
                    'class' => 'bs-example form-horizontal',
                    'tag' => false,
                    'enctype' => 'multipart/form-data'
                ]
    ]);
    ?>
    <header class="panel-heading bg-light">
        <ul class="nav nav-tabs nav-justified">
            <li class="active" id="productInfo"><a data-toggle="tab" href="#product_info" aria-controls="product_info" role="tab" data-toggle="tab">Product Information</a></li>
            <li id="productVariation"><a data-toggle="tab" href="javascript:;" aria-controls="productVariation" role="tab" data-toggle="tab">Product Variations</a></li>           
        </ul>
    </header>
    <div class="panel-body">
        <div class="tab-content">
            <div id="product_info" class="tab-pane fade in active">
                <div class="row cleafix">
                    <div class="col-sm-6" style="padding-right: 0px;">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Product Code</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="product_code" readonly="" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Vendor</label>
                            <div class="col-lg-9">

                                <select name="vendor" class="form-control" onchange="getCategory($(this), '<?php echo Url::to(['vendor-category'], true); ?>');" >
                                    <option value="">Select Vendor</option>
                                    <?php
                                    foreach ($vendorArr as $vendor) {
                                        ?><option value="<?php echo $vendor['id']; ?>" <?php echo (isset($_POST['vendor']) && $_POST['vendor'] == $vendor['id']) ? 'selected' : '' ?>><?php echo $vendor['vendor_code'] . ' - ' . $vendor['shop_name'] ?></option><?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Featured Image</label>
                            <div class="col-lg-9">
                                <div class="col-lg-10" style="padding-left:  0px">
                                    <input type="file" class="file" name="image">
                                    <p>Recommended dimensions: 400*400 px</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Product Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Category</label>
                            <div class="col-lg-9">
                                <select name="category" class="form-control" id="drpCategory" >
                                    <option value="">Select Category</option>
                                    <?php
                                    /*  foreach ($productCategory as $key => $val) {
                                      ?><option value="<?php echo $key; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $key) ? 'selected' : '' ?>><?php echo $val ?></option><?php
                                      } */
                                    ?>
                                </select>                                
                            </div>
                        </div>                         
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Product Description</label>
                            <div class="col-lg-9">
                                <textarea class="form-control" name='description' rows="8"><?php echo isset($_POST['description']) ? $_POST['description'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">SKU</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="sku" value="<?php echo isset($_POST['sku']) ? $_POST['sku'] : ''; ?>" >
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-lg-3 control-label">Status</label>
                            <div class="col-lg-9">
                                <select name="status" class="form-control">
                                    <option value="0" <?php echo (isset($_POST['status']) && $_POST['status'] == '0') ? 'selected' : '' ?>>Pending</option>
                                    <option value="1" <?php echo (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : '' ?>>Approved</option>
                                    <!--<option value="2" <?php echo (isset($_POST['status']) && $_POST['status'] == '2') ? 'selected' : '' ?>>Disapproved</option>-->
                                    <option value="3" <?php echo (isset($model->status) && $model->status == '3') ? 'selected' : '' ?>>Active</option>
                                    <option value="4" <?php echo (isset($model->status) && $model->status == '4') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div style="text-align: center;margin: 10px 25px 10px 25px ;padding: 150px 50px 150px 50px;border: 1px solid #dddddd;">
                            <h3> Featured Image</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">       
        <?php
        echo Html::submitButton('Save and Continue', ['class' => 'btn btn-primary', 'name' => 'save_continue']);
        echo '&nbsp;';
        echo Html::submitButton('Save Product', ['class' => 'btn btn-primary', 'name' => 'save']);
        echo '&nbsp;';
        echo Html::a('Back', ['products/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



