<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Helper;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
$this->title = 'Product - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$sizeStr = array();
$vendor = '';
$rolePermitions = Helper::getRolePermission();
$editPermossion = '';
if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('edit', $rolePermitions['products'])) {
    $editPermossion = 'true';
}
if (count($vendorArr) > 0) {
    $vendor = isset($vendorArr['vendor_code']) ? $vendorArr['vendor_code'] : '';
    $vendor .= isset($vendorArr['shop_name']) ? ' - ' . $vendorArr['shop_name'] : '';
}
foreach ($productSize as $size) {
    array_push($sizeStr, $size['size']);
}
$productAttr = unserialize($model->product_attributes);
$tabVariation = '';
$tabProduct = '';
if ($fromVariation > 0) {
    $tabVariation = 'active';
} else {
    $tabProduct = 'active';
}
$tab2 = '';
if ($fromVendor > 0) {
    $tab2 = 'fromVendor';
}
?>
<section class="panel">
    <header class="panel-heading bg-light">
        <ul class="nav nav-tabs nav-justified">
            <li class="<?php echo $tabProduct; ?>" id="productInfo"><a data-toggle="tab" href="#product_info" aria-controls="product_info" role="tab" data-toggle="tab" onclick="openTab('', '<?php echo $tab2; ?>');">Product Information</a></li>
            <li id="productVariation" class="<?php echo $tabVariation; ?>"><a data-toggle="tab" href="#productVariation" aria-controls="productVariation" role="tab" data-toggle="tab" onclick="openTab('fromVariation', '');">Product Variations</a></li>           
        </ul>
    </header>

    <div class="panel-body">
        <div class="tab-content">
            <div id="product_info" class="tab-pane fade in <?php echo $tabProduct; ?>">
                <div class="row cleafix">
                    <div class="col-sm-6" style="padding-right: 0px;">
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
                        <input type="hidden" name="formType" value="productUpdate"/>
                        <input type="hidden" name="save" value="0" id="btnSave" class="btnSave"/>
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
                            <label class="col-lg-3 control-label">Featured Image</label>
                            <div class="col-lg-9">
                                <div class="col-lg-10" style="padding-left:  0px">
                                    <input type="file" class="file" name="image">
                                    <p>Recommended dimensions: 400*400 px</p>
                                </div>

                                <?php /* <div class="col-lg-5">
                                  if ($model->featured_image != '') {
                                  echo Html::img('@web/images/products/' . $model->featured_image, ['height' => '50', 'width' => '50']);
                                  ?> <a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $model->id; ?>" data-myval="<?php echo $model->id; ?>" data-image="featured_image">
                                  Remove</a><?php
                                  }

                                  </div>
                                 */ ?>

                            </div>
                        </div>
                        <div class="form-group"> 
                            <label class="col-lg-3 control-label">Product Name</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="name" value="<?php echo isset($model->name) ? htmlentities($model->name) : '' ?>">
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
                            <label class="col-lg-3 control-label">Product Description</label>
                            <div class="col-lg-9">
                                <textarea class="form-control" name='description' rows="8"><?php echo isset($model->description) ? $model->description : '' ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">SKU</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="sku" value="<?php echo isset($model->sku) ? $model->sku : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Status</label>
                            <div class="col-lg-9">
                                <select name="status" class="form-control" onchange="return changeStatus($(this));" id="drpStatus">
                                    <option value="0" <?php echo (isset($model->status) && $model->status == '0') ? 'selected' : '' ?>>Pending</option>
                                    <option value="1" <?php echo (isset($model->status) && $model->status == '1') ? 'selected' : '' ?>>Approved</option>
                                    <option value="2" <?php echo (isset($model->status) && $model->status == '2') ? 'selected' : '' ?>>Disapproved</option>
                                    <option value="3" <?php echo (isset($model->status) && $model->status == '3') ? 'selected' : '' ?>>Active</option>
                                    <option value="4" <?php echo (isset($model->status) && $model->status == '4') ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <?php 
                        $reason = '';
                        $reasontext = '';
                        if($model->status == '2'){
                            $reason = $model->disapprove_reason;
                            $arr = explode('other-',$model->disapprove_reason);
                            if(count($arr) == 2){
                                $reasontext = $arr[1];
                                $reason = 'Other';
                            }
                        }
                        
                        ?>
                        <div class="form-group" <?php echo ($reason == '')?'style="display: none;"':''; ?> id="drpReasons">
                            <label class="col-lg-3 control-label">Reason</label>
                            <div class="col-lg-9">
                                <select name="drpReasons" id="drpReason" class="form-control" onchange="return changeReason($(this));">
                                    <option value="">Select reason</option>
                                    <option value="Reason 1" <?php echo ($reason == 'Reason 1')?'selected':''; ?>>Reason 1</option>
                                    <option value="Reason 2" <?php echo ($reason == 'Reason 2')?'selected':''; ?>>Reason 2</option>
                                    <option value="Reason 3" <?php echo ($reason == 'Reason 3')?'selected':''; ?>>Reason 3</option>
                                    <option value="Other" <?php echo ($reason == 'Other')?'selected':''; ?>>Other</option>
                                </select>
                                 <label id="drpReasonError" style="display: none;" class="error">Please select reason.</label>
                            </div>
                        </div>
                        <div class="form-group" id="txtOtherReason" <?php echo ($reason != '' && $reason == 'Other')?'':'style="display: none;"'; ?>>
                            <label class="col-lg-3 control-label">Other Reason</label>
                            <div class="col-lg-9">
                                <textarea placeholder="Enter reason" class="form-control" name="otherReason" id="txtOtherReasontext"><?php echo $reasontext; ?></textarea>
                                 <label id="txtOtherReasonError" style="display: none;" class="error">Please enter reason.</label>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>  
                    </div>
                    <div class="col-sm-6">
                        <div style="text-align: center;">
                            <?php
                            if ($model->featured_image != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $model->featured_image)) {
                                echo Html::img('@web/images/products/' . $model->featured_image, ['height' => '400', 'width' => '400']);
                                ?> <br><a href="javascript:;" onclick="deleteImg($(this));" class="delImg" id="img<?php echo $model->id; ?>" data-myval="<?php echo $model->id; ?>" data-image="featured_image">
                                    Remove</a><?php
                            } else {
                                ?><div style="text-align: center;margin: 10px 25px 10px 25px ;padding: 150px 50px 150px 50px;border: 1px solid #dddddd;">
                                    <h3> Featured Image</h3>
                                </div><?php
                            }
                            ?>
                        </div>

                    </div>
                </div>
            </div>
            <div id="productVariation" class="tab-pane fade in <?php echo $tabVariation; ?>">
                <div class="row cleafix">

                    <?php
                    $form = ActiveForm::begin([
                                'options' => [
                                    'name' => 'frmProductVariation',
                                    'id' => 'frmProductVariation',
                                    'class' => 'bs-example form-horizontal',
                                    'tag' => false,
                                    'enctype' => 'multipart/form-data',
//                                    'onsubmit'=>'return validateForm();'
                                ]
                    ]);
                    ?>
                    <input type="hidden" name="save" value="0" id="btnSave" class="btnSave"/>
                    <input type="hidden" name="formType" value="productVariation"/>
                    <input type="hidden" name="saveQtyAndPrice" id="saveQtyAndPrice" value="no"/>
                    <input type="hidden" name="saveSizeAndFits" id="saveSizeAndFits" value="no"/>
                    <!--<input type="hidden" name="btnId" id="btnId" value=""/>-->
                    <div class="col-sm-6"> 
                        <div class="form-group">

                            <label class="col-lg-3 control-label">Add Color</label>
                            <div class="col-lg-9" style="border: 1px #c0c0c0 solid;padding: 15px;">
                                <div class="form-group">
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" id="color_name" name="color_name" placeholder="Color Name">
                                        <label for="color_name" id="color_name_error" class="error" style="display: none">Please enter color name.</label>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="col-lg-12" style="margin-bottom: 20px;">
                                        <label>Featured Image</label>
                                        <input type="file" class="file" name="featured_image_color">                                           
                                    </div>
                                    <div class="col-lg-12" >    
                                        <label>Other Images</label>
                                        <input type="file" class="file" name="other_color_images[]" style="margin-bottom: 20px;" multiple="">
                                        <!--<p><b style="color: #DB2929;">NOTE: </b><span style="color:#DB2929;">Featured image</span> will be one per one color. <span style="color:#DB2929;">Other images</span> will be max 3 per color.</p>-->
                                    </div>   

                                    <div class="pull-right col-lg-4">
                                        <?php
                                        if ($editPermossion == 'true') {
                                            ?> <input type="submit" name="btnAddColor" value="Add Color" class="btn btn-s-sm btn-primary" /><?php
                                        }
                                        ?>

                                    </div>
                                </div>

                                <div class="line line-dashed line-lg pull-in"></div>
                                <div id="addColors">

                                    <?php
//                                    echo '<pre>';
//                                    print_r($colorImgs);
//                                    echo '</pre>';
                                    if (count($productColor) > 0) {
                                        ?><h4>Colors</h4><?php
                                        foreach ($productColor as $color) {
                                            ?>
                                            <div id="colorDiv" style="margin-bottom: 15px;">
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control col-lg-4" name="txtColor[]" value="<?php echo $color['color'] ?>" readonly="">
                                                </div>
                                                <div class="col-lg-2">
                                                    <?php if ($editPermossion == 'true') {
                                                        ?><a id="removeRow" class="btn btn-sm btn-primary" onclick="removeRow('<?php echo $color['color']; ?>', '<?php echo $model->id; ?>')" style="cursor: pointer">
                                                            <span class="fa fa-minus"></span>
                                                        </a><?php }
                                                    ?>                                                    
                                                </div>

                                                <div class="col-lg-10" style="margin: 5px;">
                                                    <?php
                                                    if (isset($colorImgs[$color['color']]) && count($colorImgs[$color['color']]) > 0) {
                                                        $i = 0;
                                                        foreach ($colorImgs[$color['color']] as $img) {
                                                            ?>
                                                            <div style="width: 20%;float: left">
                                                                <?php
                                                                $featured = '<br>';
                                                                if ($i == 0) {
                                                                    $featured = 'Featured';
                                                                }
                                                                echo '<span style="font-size: smaller; font-weight: 700;">' . $featured . '</span>';
                                                                echo Html::img('@web/images/products/' . $img['image'], ['height' => '50', 'width' => '50']);
                                                                if ($editPermossion == 'true') {
                                                                    ?><a href="javascript:;" onclick="deleteColorImg($(this));" class="delImg" id="img<?php echo $img['id']; ?>" data-myval="<?php echo $img['id']; ?>" data-image="<?php echo $img['image']; ?>">
                                                                        Remove</a><?php
                                                                }
                                                                ?>
                                                            </div><?php
                                                            $i++;
                                                        }
                                                    } else {
                                                        echo 'No images uploaded.';
                                                    }
                                                    ?>
                                                </div>&nbsp;

                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo 'No color available.';
                                    }
                                    ?>
                                </div> 

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Does it have multiple sizes?</label>
                            <div class="col-lg-9" style="margin-top: 6px;">

<!--<input type="text" class="form-control" name="multiple_size" value="<?php // echo $sizeStr;                                                            ?>">-->
                                <?php
                                $disable = '';
                                if (in_array('One Size', $sizeStr)) {
                                    $disable = 'disabled="disabled"';
                                }
                                if ($editPermossion == '') {
                                    $disable = 'disabled="disabled"';
                                }
                                foreach ($categorySize as $key => $val) {
                                    ?>
                                    <input type="checkbox" class="" id="chk_<?php echo str_replace(' ', '-', $val['size']); ?>" name="multiple_size[]" value="<?php echo str_replace(' ', '-', $val['size']); ?>" <?php echo in_array($val['size'], $sizeStr) ? 'checked' : ''; ?> onchange="changeSize($(this));" <?php echo $val['size'] != 'One Size' ? $disable : ''; ?>> <label style="vertical-align: top;margin-top: 1px;"><?php echo $val['size']; ?>
                                    </label> &nbsp;
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Quantity & Price</label>
                            <div class="col-lg-9">
                                <label class="error" id="variationError" style="display: none;">Quantity and price should not be blank and greater than 0.</label>
                                <table class="">
                                    <tr>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                    <?php
                                    if (count($productVariation) > 0) {
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td><input type="checkbox" name="chkQty" id="setVariationQty"/> <label style="vertical-align: top;margin-top: 1px;">Apply to all</label></td>
                                            <td><input type="checkbox" name="chkPrice" id="setVariationPrice"/> <label style="vertical-align: top;margin-top: 1px;">Apply to all</label></td>
                                        </tr>
                                        <?php
                                        foreach ($productVariation as $variation) {
                                            ?>
                                            <tr id="variation-table">
                                                <td><?php echo ucfirst($variation['color']); ?></td>
                                                <td><?php echo$variation['size']; ?></td>

                                                <td><input type="text" name="variationQty[<?php echo $variation['id']; ?>]" value="<?php echo $variation['qty']; ?>" size="4" class="variationQty" onkeypress="return isNumber(event)"/></td>
                                                <td><input type="text" name="variationPrice[<?php echo $variation['id']; ?>]" value="<?php echo $variation['display_price']; ?>" size="6" class="variationPrice" onkeypress="return isBudget(event, $(this))"/></td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                                if (count($productVariation) > 0) {
                                    ?>
                                    <div class="pull-right">
    <!--                                        <input type="submit" name="btnSavePrice" value="Save Quantity & Price" class="btn btn-primary btn-xs"/>-->

                                        <?php
                                        if ($editPermossion == 'true') {
                                            echo Html::a('Save Quantity & Price', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'onclick' => 'saveQtyAndPrice("frmProductVariation")']);
                                        }

                                        /* if (count($sizeStr) > 0 && $sizeStr[0] != '') {
                                          echo Html::a('Save Quantity & Price', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'onclick' => 'saveQtyAndPrice("frmProductVariation")']);
                                          } else {
                                          echo Html::a('Save Quantity & Price', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'disabled' => 'disabled']);
                                          } */
                                        ?> 
                                    </div>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php
                            $sizeCount = count($categorySize);
                            $fitsCount = count($attributesSize);
                            if ($fitsCount > 0) {
                                ?><label class="col-lg-3 control-label">Size & Fit Info</label>
                                <div class="col-lg-9" style="padding-left: 0px;">
                                    <label class="error" id="fitsError" style="display: none;">Size and fit info should not be blank.</label>
                                    <table >

                                        <tr>
                                            <th>Size</th>
                                            <?php for ($j = 0; $j < $fitsCount; $j++) {
                                                ?><th><?php echo $attributesSize[$j]['attribute_name']; ?> (inches)</th><?php }
                                            ?>
                                        </tr>
                                        <?php
                                        $button = '';

                                        for ($i = 0; $i < $sizeCount; $i++) {
                                            $readonly = '';
                                            /* if (!in_array($categorySize[$i]['size'], $sizeStr)) {
                                              $readonly = 'readonly';
                                              } */
                                            if (in_array($categorySize[$i]['size'], $sizeStr)) {
//                                           $categorySize[$i]['size'] = str_replace(' ', '-', $categorySize[$i]['size']);
                                                $button = 'yes';
                                                ?><tr>
                                                    <td><?php echo $categorySize[$i]['size']; ?></td>
                                                    <?php for ($j = 0; $j < $fitsCount; $j++) {
                                                        ?>
                                                        <td><input class="fitBox" type="text" size="3" name="fits[<?php echo $categorySize[$i]['size'] . '-' . str_replace(' ', '_', $attributesSize[$j]['attribute_name']); ?>]" <?php echo $readonly; ?> value="<?php echo isset($productAttr[$categorySize[$i]['size'] . '-' . str_replace(' ', '_', $attributesSize[$j]['attribute_name'])]) ? $productAttr[$categorySize[$i]['size'] . '-' . str_replace(' ', '_', $attributesSize[$j]['attribute_name'])] : ''; ?>"/></td>
                                                    <?php }
                                                    ?>
                                                </tr><?php
                                            }
                                        }
                                        ?>
                                    </table>
                                    <div class="pull-right">
                                        <!--<input type="submit" name="btnSaveSize" value="Save Size & Fit Info" class="btn btn-primary btn-xs" />-->
                                        <?php
                                        if ($button != '' && $editPermossion == 'true') {
                                            echo Html::a('Save Size & Fit Info', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'onclick' => 'saveSizeAndFits("frmProductVariation")']);
                                        }

                                        /* if (count($sizeStr) > 0 && $sizeStr[0] != '') {
                                          echo Html::a('Save Size & Fit Info', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'onclick' => 'saveSizeAndFits("frmProductVariation")']);
                                          } else {
                                          echo Html::a('Save Size & Fit Info', 'javascript:;', ['class' => 'btn btn-primary btn-xs', 'disabled' => 'disabled']);
                                          } */
                                        ?> 
                                    </div>
                                </div><?php
                            }
                            ?>

                        </div>


                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

    </div>
    <div class="head-buttons" style="border-bottom: 0px;border-top:  1px solid #ddd;">
        <?php
        if ($fromVariation <= 0 && (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('edit', $rolePermitions['products']))) {
            echo Html::a('Save and Continue', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'submitProductForm(0)']);
            echo '&nbsp;';
            echo Html::a('Save Product', 'javascript:;', ['class' => 'btn btn-primary', 'onclick' => 'submitProductForm(1)']);
            echo '&nbsp;';
        }
        if ($fromVendor > 0) {
            echo Html::a('Back', ['vendors/update', 'id' => $model->vendor_id, 'fromProducts' => 1], ['class' => 'btn btn-default']);
        } else {
            echo Html::a('Back', ['products/index'], ['class' => 'btn btn-default']);
        }
        ?>
    </div>
</section>
<style>
    table{
        border: 1px #c0c0c0 solid;
        width: 100%;
        margin-bottom: 10px;
    }
    th,td{
        vertical-align: top;
        padding: 5px;
        border: 1px #c0c0c0 solid;
    }
</style>




