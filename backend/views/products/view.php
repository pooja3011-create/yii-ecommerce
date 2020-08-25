<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$sizeStr = array();
$vendor = '';

if (count($vendorArr) > 0) {
    $vendor = isset($vendorArr['vendor_code']) ? $vendorArr['vendor_code'] : '';
    $vendor .= isset($vendorArr['shop_name']) ? ' - ' . $vendorArr['shop_name'] : '';
}
foreach ($productSize as $size) {
    array_push($sizeStr, $size['size']);
}
$productAttr = unserialize($model->product_attributes);
?>
<section class="panel">
    <?php
    $form = ActiveForm::begin([
                'options' => [
                    'name' => 'productUpdate',
                    'id' => 'productUpdate',
                    'class' => 'bs-example form-horizontal',
                    'tag' => false,
                    'enctype' => 'multipart/form-data'
                ]
    ]);
    ?>
    <div class="head-buttons">
        <?php echo Html::a('Save and Continue', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'submitProductForm(0)']); ?>
        <?php echo Html::a('Save Product', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'submitProductForm(1)']); ?>
        <?php echo Html::a('Back', ['products/index'], ['class' => 'btn btn-default']); ?>
    </div>
    <input type="hidden" name="save" value="0" id="btnSave"/>
    <div class="panel-body">
        <div class="col-sm-6"> 
            <div class="form-group">
                <label class="col-lg-3 control-label">Product Code</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="product_code" readonly="" value="<?php echo isset($model->product_code) ? $model->product_code : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Vendor</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="vendor" readonly="" value="<?php echo ($vendor != '') ? $vendor : '-' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Product Name</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="name" value="<?php echo isset($model->name) ? $model->name : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Product Description</label>
                <div class="col-lg-9">
                    <textarea class="form-control" name='description'><?php echo isset($model->description) ? $model->description : '' ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">SKU</label>
                <div class="col-lg-9">
                    <input type="text" class="form-control" name="sku" value="<?php echo isset($model->sku) ? $model->sku : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Add Color</label>
                <div class="col-lg-9" style="border: 1px #c0c0c0 solid;padding: 15px;">

                    <div class="form-group">
                        <div class="col-lg-8">
                            <input type="text" class="form-control" id="color_name" name="color_name" placeholder="Color Name">
                            <label for="color_name" id="color_name_error" class="error" style="display: none">Please enter color name.</label>
                        </div>
                        <div class="col-lg-3">
                            <button id="btnAddColor" class="btn btn-s-sm btn-primary" onclick="return addcolor()">Add Color</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12" style="margin-bottom: 20px;">
                            <label>Featured Image</label>
                            <input type="file" class="file" name="featured_image_color">                                           
                        </div>
                        <div class="col-lg-12" >    
                            <label>Other Images</label>
                            <input type="file" class="file" name="other_color_images[]" multiple="" style="margin-bottom: 20px;">
                            <p><b>NOTE: </b>Featured image will be one per one color. Other images will be max 3 per color.</p>
                        </div>   

                    </div>
                    <div class="line line-dashed line-lg pull-in"></div>
                    <div id="addColors">
                        <h4>Colors</h4>
                        <?php
                        if (count($productColor) > 0) {
                            foreach ($productColor as $color) {
                                ?>
                                <div id="colorDiv" style="margin-bottom: 5px;">
                                    <div class="col-lg-10">
                                        <input type="text" class="form-control col-lg-4" name="txtColor[]" value="<?php echo $color['color'] ?>">
                                    </div>
                                    <div class="col-lg-2">
                                        <a id="removeRow" class="btn btn-sm btn-primary" onclick="removeRow('<?php echo $color['color']; ?>', '<?php echo $model->id; ?>')" style="cursor: pointer">
                                            <span class="fa fa-minus"></span>
                                        </a>
                                    </div>
                                    <?php
                                    if (isset($colorImgs[$color['color']]) && count($colorImgs[$color['color']]) > 0) {
                                        ?>
                                        <div class="col-lg-10" style="margin: 5px;">
                                            <?php
                                            foreach ($colorImgs[$color['color']] as $img) {
                                                echo Html::img('@web/images/products/' . $img['image'], ['height' => '50', 'width' => '50']);
                                                echo '&nbsp;';
                                            }
                                            ?>
                                        </div>&nbsp;
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div> 

                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Size & Fit Info</label>
                <div class="col-lg-9">
                    <label class="error" id="fitsError" style="display: none;">Size and fit info should not be blank and 0.</label>
                    <table >
                        <?php
                        $sizeCount = count($categorySize);
                        $fitsCount = count($attributesSize);
                        ?>
                        <tr>
                            <th>Size</th>
                            <?php for ($j = 0; $j < $fitsCount; $j++) {
                                ?><th><?php echo $attributesSize[$j]['attribute_name']; ?></th><?php }
                            ?>

                        </tr>
                        <?php
                        for ($i = 0; $i < $sizeCount; $i++) {
                            $readonly = '';
                            if (!in_array($categorySize[$i]['size'], $sizeStr)) {
                                $readonly = 'readonly';
                            }
                            ?><tr>
                                <td><?php echo $categorySize[$i]['size']; ?></td>
                                <?php for ($j = 0; $j < $fitsCount; $j++) {
                                    ?>
                                    <td><input class="fitBox" type="text" size="3" name="fits[<?php echo $categorySize[$i]['size'] . '-' . str_replace(' ', '_', $attributesSize[$j]['attribute_name']); ?>]" <?php echo $readonly; ?> value="<?php echo $productAttr[$categorySize[$i]['size'] . '-' . str_replace(' ', '_', $attributesSize[$j]['attribute_name'])]; ?>"/></td>
                                <?php }
                                ?>
                            </tr><?php }
                            ?>


                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label class="col-lg-3 control-label">Featured Image</label>
                <div class="col-lg-9">
                    <div class="col-lg-7">
                        <input type="file" class="file" name="image">
                    </div>
                    <div class="col-lg-5">
                        <?php
                        if ($model->featured_image != '') {
                            echo Html::img('@web/images/products/' . $model->featured_image, ['height' => '50', 'width' => '50']);
                            ?> <a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $model->id; ?>" data-myval="<?php echo $model->id; ?>" data-image="featured_image">
                                Remove</a><?php
                    }
                        ?>
                    </div>


                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Category</label>
                <div class="col-lg-9">
                    <select name="category" class="form-control" disabled="">
                        <option value="">Select Category</option>
                        <?php
                        foreach ($productCategory as $key => $val) {
                            ?><option value="<?php echo $key; ?>" <?php echo ($key == $model->category_id) ? 'selected' : ''; ?>><?php echo $val ?></option><?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Does it have multiple sizes?</label>
                <div class="col-lg-9">

<!--<input type="text" class="form-control" name="multiple_size" value="<?php // echo $sizeStr;              ?>">-->
                    <?php
                    foreach ($categorySize as $key => $val) {
                        ?>
                        <input type="checkbox" class="" name="multiple_size[]" value="<?php echo $val['size']; ?>" <?php echo in_array($val['size'], $sizeStr) ? 'checked' : ''; ?> onchange="changeSize($(this));"> <label style="vertical-align: super;"><?php echo $val['size']; ?>
                        </label> &nbsp;
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Quantity & Price</label>
                <div class="col-lg-9">
                    <label class="error" id="variationError" style="display: none;">Quantity and price should not be blank and 0.</label>
                    <table class="">
                        <tr>
                            <th>&nbsp;</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        <?php
                        if (count($productVariation) > 0) {
                            ?>
                            <tr>
                                <td></td>
                                <td><input type="checkbox" name="chkQty" id="setVariationQty"/></td>
                                <td><input type="checkbox" name="chkPrice" id="setVariationPrice"/></td>

                            </tr>
                            <?php
                            foreach ($productVariation as $variation) {
                                ?>
                                <tr id="variation-table">
                                    <td><?php echo ucfirst($variation['color']) . '-' . $variation['size']; ?></td>
                                    <td><input type="text" name="variationQty[<?php echo $variation['id']; ?>]" value="<?php echo $variation['qty']; ?>" size="4" class="variationQty" onkeypress="return isNumber(event)"/></td>
                                    <td><input type="text" name="variationPrice[<?php echo $variation['id']; ?>]" value="<?php echo $variation['display_price']; ?>" size="6" class="variationPrice" onkeypress="return isNumber(event)"/></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3 control-label">Status</label>
                <div class="col-lg-9">
                    <select name="status" class="form-control">
                        <option value="0" <?php echo (isset($model->status) && $model->status == '0') ? 'selected' : '' ?>>Pending</option>
                        <option value="1" <?php echo (isset($model->status) && $model->status == '1') ? 'selected' : '' ?>>Approved</option>
                        <option value="2" <?php echo (isset($model->status) && $model->status == '2') ? 'selected' : '' ?>>Disapproved</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>  
</section>
<style>
    table{
        border: 1px #c0c0c0 solid;
        width: 100%;
    }
    th,td{
        vertical-align: top;
        padding: 5px;
        border: 1px #c0c0c0 solid;
    }
</style>




