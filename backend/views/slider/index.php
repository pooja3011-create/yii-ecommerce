
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SliderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Slider';

$rolePermitions = Helper::getRolePermission();
?>
<!-- .vbox -->
<section class="panel">
    <div class="action-row custom-border">
        <div class="row m-t-sm clearfix">
            <div class="col-sm-6 m-b-xs ">
                <div class="page-counter">

                </div>
            </div>
            <div class="col-sm-6 m-b-xs text-right">
                <?php
                if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('add', $rolePermitions['products'])) {
                    echo Html::a('Add Slide', ['create'], ['class' => 'btn custom-btn']);
                }
                ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <div class="grid-view" id="w0">
            <table class="table table-striped table-bordered">                               
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Image</th>                      
                        <th>Status</th>
                        <th class="action-column">Actions</th>
                    </tr>                  
                </thead>
                <tbody id="sortable">
                    <?php
                    if (!empty($sliderArr)) {
                        foreach ($sliderArr as $slide) {
                            ?>
                            <tr id="<?php echo $slide['id']; ?>">
                                <td><?php echo $slide['title']; ?></td>
                                <td>
                                    <?php
                                    if (isset($slide['image']) && $slide['image'] != '' && file_exists(Yii::$app->basePath . '/web/images/slider/' . $slide['image'])) {
                                        $image = Url::to('@web/images/slider/' . $slide['image'], TRUE);
                                    } else {
                                        $image = Url::to('@web/images/no_image.png', TRUE);
                                    }
                                    ?>
                                    <img width="70" height="60" alt="" src="<?php echo $image; ?>">
                                </td>
                                <td><?php echo ($slide['status'] == '1') ? 'Active' : 'Inactive'; ?></td>
                                <td class="action">
                                    <a title="Edit" href="<?php echo Url::to(['/slider/update','id' => $slide['id']], TRUE); ?>"><span class="fa fa-pencil"></span></a> 
                                    <a onclick="javascript:return confirm('<?php echo Yii::$app->params['removeSlideConf']; ?>');" title="Approved" href="<?php echo Url::to(['/slider/delete', 'id' => $slide['id']], TRUE); ?>"><span class="fa fa-times"></span></a>
                                </td>
                            </tr>   <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-legends">
        <div class="icon-box">
            <i class="fa fa-pencil"></i> Edit
        </div>

        <div class="icon-box disapproved">
            <i class="fa fa-times"></i> Delete
        </div>

    </div>
</section>
<!-- /.vbox -->

