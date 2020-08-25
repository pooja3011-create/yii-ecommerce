<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Helper;
use yii\widgets\ActiveForm;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use app\models\Products;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProductsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
$categoryArr = Products::getProductCategory();

$rolePermitions = Helper::getRolePermission();
$columnArr = [
    [
        'class' => 'yii\grid\CheckboxColumn',
        'checkboxOptions' => function ($model, $key, $index, $column) {
            $class = array('id' => $model->id);

            return $class;
        }
            ],
            'product_code',
            [
                'label' => 'Image',
                'format' => 'html',
                'value' => function ($data) {
                    return (empty($data["featured_image"])) ? Html::img(Yii::getAlias('@web') . '/images/no_image.png', ['width' => '70px', 'height' => '60']) : Html::img('@web/images/products/' . $data["featured_image"], ['width' => '70px', 'height' => '60']);
                },
                    ],
                    'name',
                    [
                        'attribute' => 'category_name',
                        'label' => 'Category',
                        'value' => 'category.name',
                        'filter' => Html::DropDownList('ProductsSearch[category_id]', $searchModel->category_id, $categoryArr, array('class' => 'form-control', 'prompt' => 'View All')),
                    ],
                    [
                        'attribute' => 'sku',
                        'label' => 'SKU',
                        'value' => 'sku',
                    ],
                    [
                        'attribute' => 'vendor_name',
                        'label' => 'Vendor',
                        'value' => 'vendors.name',
                    ],
                    [
                        'options' => ['style' => 'width:15%;'],
                        'attribute' => 'status',
                        'format' => 'html',
                        'contentOptions' => function ($searchModel) {
                    if ($searchModel->status == 0) {
                        return array('class' => 'status yellow');
                    } else if ($searchModel->status == 1) {
                        return array('class' => 'status green');
                    } else if ($searchModel->status == 2) {
                        return array('class' => 'status red');
                    } else if ($searchModel->status == 3) {
                        return array('class' => 'status green');
                    } else {
                        return array('class' => 'status gray');
                    }
                },
                        'value' => function ($searchModel) {
                    if ($searchModel->status == 0) {
                        return '<span>Pending</span>';
                    } else if ($searchModel->status == 1) {
                        return '<span>Approved</span>';
                    } else if ($searchModel->status == 2) {
                        return '<span>Disapproved</span>';
                    } else if ($searchModel->status == 3) {
                        return '<span>Active</span>';
                    } else {
                        return '<span>Inactive</span>';
                    }
                },
                        'filter' => Html::DropDownList('ProductsSearch[status]', $searchModel->status, array('0' => 'Pending', "1" => "Approved", "2" => "Disapproved", "3" => 'Active', "4" => "Inactive"), array('class' => 'form-control', 'prompt' => 'View All')),
                    ],
                ];

                if (Yii::$app->user->id == '1' || (isset($rolePermitions['products']) && (in_array('edit', $rolePermitions['products']) || in_array('view', $rolePermitions['products'])))) {
                    $columnArr[] = ['class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'contentOptions' => ['class' => 'action'],
                        'template' => '{approve} {disapprove} {view}',
                        'buttons' => [
                            'approve' => function ($url, $model, $key) {
                                $rolePermitions = Helper::getRolePermission();

                                if ($model->status == '0' && (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('edit', $rolePermitions['products']))) {
                                    return Html::a('<span class="fa fa-check"></span>', 'javascript:;', ['onclick' => 'approveProduct("' . URL::to(['/products'], true) . '","' . $model->id . '")', 'title' => 'Approved']);
                                }
                            }, 'disapprove' => function ($url, $model, $key) {
                                $rolePermitions = Helper::getRolePermission();

                                if ($model->status == '0' && (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('edit', $rolePermitions['products']))) {
                                    return Html::a('<span class="fa fa-times"></span>', 'javascript:;', ['onclick' => 'disapproveProduct("' . URL::to(['/products'], true) . '","' . $model->id . '","' . URL::to(['/products/disapproved'], true) . '")', 'title' => 'Disapproved']);
                                }
                            }, 'view' => function ($url, $model, $key) {
                                $rolePermitions = Helper::getRolePermission();
                                if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('view', $rolePermitions['products'])) {
                                    return Html::a('<span class="fa fa-eye"></span>', ['update', 'id' => $model->id], ['title' => 'View']);
                                }
                            }
                                ],
                            ];
                        }
                        ?>
                        <!-- .vbox -->
                        <section class="panel">
                            <div class="action-row custom-border">
                                <div class="row m-t-sm clearfix">
                                    <div class="col-sm-6 m-b-xs ">
                                        <?php
                                        if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('edit', $rolePermitions['products'])) {
                                            ?> <select class="form-control input-s inline" id="productAction" name="productAction">
                                                <option value="">Actions</option>
                                                <option value="0">Pending</option>
                                                <option value="1">Approved</option>
                                                <option value="2">Disapproved</option>
                                                <option value="3">Active</option>
                                                <option value="4">Inactive</option>
                                            </select>
                                            <button class="btn btn-primary" onclick="actionProduct('<?php echo URL::to(['/products'], true); ?>', '<?php echo URL::to(['/products/disapproved'], true); ?>')">Apply</button><?php
                                        }
                                        ?>

                                    </div>
                                    <div class="col-sm-6 m-b-xs text-right">
                                        <?php
                                        if (Yii::$app->user->id == '1' || isset($rolePermitions['products']) && in_array('add', $rolePermitions['products'])) {
                                            echo Html::a('Add Product', ['create'], ['class' => 'btn custom-btn']);
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                            <div class="search-bar">
                                <div class="row m-t-sm clearfix">
                                    <div class="col-sm-6 m-b-xs ">
                                        <div class="page-counter">
                                            <?php
                                            $arr = Yii::$app->request->queryParams;
                                            $url = Url::to(['index'], true);
                                            if (isset($_GET['ProductsSearch'])) {
                                                foreach ($_GET['ProductsSearch'] as $key => $val) {
                                                    if (strpos($url, '?') > 0) {
                                                        $url .= '&ProductsSearch[' . $key . ']=' . $val;
                                                    } else {
                                                        $url .= '?ProductsSearch[' . $key . ']=' . $val;
                                                    }
                                                }
                                            }
                                            $page = (isset($_GET['per-page'])) ? $_GET['per-page'] : Yii::$app->params['list-pagination'];
                                            echo Helper::paginationHtml($page, $url);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 m-b-xs">
                                        <?php
                                        $form = ActiveForm::begin([
                                                    'action' => ['index'],
                                                    'method' => 'get',
                                        ]);
                                        ?>
                                        <div class="input-group pull-right">
                                            <?php /** <input type="text" placeholder="search by product code, name or sku" size="50" value="<?php echo isset($_GET['ProductsSearch']['searchProduct']) ? $_GET['ProductsSearch']['searchProduct'] : '' ?>" name="ProductsSearch[searchProduct]" class="input-sm form-control "> */ ?>
                                        </div>
                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <?=
                                GridView::widget([
                                    'dataProvider' => $dataProvider,
                                    'filterModel' => $searchModel,
                                    'layout' => "{items}\n{summary}\n{pager}",
                                    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' -- '],
                                    'columns' => $columnArr,
                                ]);
                                ?>
    </div>
    <div class="table-legends">
        <div class="icon-box approved">
            <i class="fa fa-check"></i> Approved
        </div>

        <div class="icon-box disapproved">
            <i class="fa fa-times"></i> Disapproved
        </div>

        <div class="icon-box view">
            <i class="fa fa-eye"></i> View
        </div>

    </div>
</section>
<!-- /.vbox -->
