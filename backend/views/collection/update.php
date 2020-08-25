<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $postArr app\models\Products */


$postArr = $model;
$this->title = 'Add Collection';
$postArr = $collectionArr;
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
                <?php
                if (isset($postArr['image']) && $postArr['image'] != '') {
                    ?> 
                    <div style="text-align: center;margin-bottom: 5px;" class="col-lg-6">
                        <?php
                        echo Html::img('@web/images/collection/' . $postArr['image'], ['height' => '200', 'width' => '200']);
                        ?> <br><a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $postArr['id']; ?>" data-myval="<?php echo $postArr['id']; ?>" data-image="image">
                            Remove</a>
                    </div>
                    <?php
                } else {
                  /*  ?>  <div style="text-align: center;border: 1px solid #dddddd;" class="col-lg-6">
                        <h3>Collection Image</h3>
                    </div><?php*/
                }
                ?>
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
                <input type="text" name="product_name" id="productName" class="form-control" />
                <input type="hidden" name="product_id" id="productId" class="form-control"/>
                <div id="suggesstion-box"></div>
            </div>
            <div class="col-lg-2"> 
                <?php echo Html::a('Add', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'addProduct()']); ?>
            </div>
           
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
                    <?php
                    if (isset($postArr['products']) && !empty($postArr['products'])) {

                        foreach ($postArr['products'] as $product) {
                            if (isset($product['featured_image']) && $product['featured_image'] != '') {
                                $product['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true);
                            } else {
                                $product['featured_image'] = Url::to('@web/images/no_image.png', true);
                            }
                            ?>
                            <tr id="row_<?php echo $product['id']; ?>">
                                <td><?php echo $product['product_code']; ?><input type="hidden" name="collectionProduct[]" value="<?php echo $product['id']; ?>"/></td>
                                <td>                                    
                                    <img src="<?php echo $product['featured_image']; ?>" height="60" weight="60"/></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo $product['vendor_name']; ?></td>    
                                <td><a href="javascript:;" onclick="removeProduct('row_<?php echo $product['id']; ?>');"><i class="fa fa-times"></i></a></td>                     
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        echo Html::a('Delete', ['collection/delete','id'=>$postArr['id']], ['class' => 'btn btn-primary','onclick' => 'javascript:return confirm("Are you sure you want to permanently remove this collection from the system?");']);
        echo '&nbsp;';
        echo Html::submitButton('Save Collection', ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Back', ['collection/index'], ['class' => 'btn btn-default']);
        ?>
    </div>
    <?php ActiveForm::end(); ?>  
</section>



