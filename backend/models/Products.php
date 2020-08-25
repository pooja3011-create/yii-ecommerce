<?php

namespace app\models;

use app\models\ProductVariation;
use Yii;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $name
 * @property string $product_code
 * @property string $sku
 * @property string $description
 * @property string $featured_image
 * @property integer $category_id
 * @property string $disapprove_reason
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 * @property string $status
 */
class Products extends \yii\db\ActiveRecord {

    public $imageFiles;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'products';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'product_code', 'sku', 'category_id'], 'required'],
            [['description', 'status'], 'string'],
            [['category_id', 'updated_by'], 'integer'],
            [['created_date', 'updated_date'], 'safe'],
            [['name', 'product_code', 'sku', 'featured_image', 'disapprove_reason'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'product_code' => 'Product Code',
            'sku' => 'Sku',
            'description' => 'Description',
            'featured_image' => 'Featured Image',
            'category_id' => 'Category ID',
            'disapprove_reason' => 'Disapprove Reason',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVendors() {
        return $this->hasOne(Vendors::className(), ['id' => 'vendor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getProductVariation() {
//        return $this->hasOne(ProductVariation::className(), ['product_id' => 'id']);
//    }

    function getProductSize($id) {
        $data = (new \yii\db\Query())
                ->select(['size'])
                ->from('product_variation')
                ->where(['product_id' => $id])
                ->groupBy('size')
                ->all();
        return $data;
    }

    function getCategorySize($id) {
        $data = (new \yii\db\Query())
                ->select(['size'])
                ->from('product_size_variation')
                ->where(['category_id' => $id])
                ->orderBy(['order' => SORT_ASC])
                ->all();
        return $data;
    }

    function getCategoryFits($id) {
        $data = (new \yii\db\Query())
                ->select(['attribute_name'])
                ->from('product_size_attributes')
                ->where(['category_id' => $id])
                ->all();
        return $data;
    }

    function getProductColor($id) {
        $data['colors'] = (new \yii\db\Query())
                ->select(['color'])
                ->from('product_variation')
                ->where(['product_id' => $id])
                ->groupBy('color')
                ->orderBy(['id' => SORT_DESC])
                ->all();
        $variationArr = array();
        $data['images'] = array();
        if (count($data['colors']) > 0) {
            foreach ($data['colors'] as $color) {
                $variationArr = array();
                $variations = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('product_variation')
                        ->where(['product_id' => $id])
                        ->andWhere(['color' => $color['color']])
                        ->all();
                foreach ($variations as $variation) {
                    array_push($variationArr, $variation['id']);
                }
                $ids = implode(',', $variationArr);
                $data['images'][$color['color']] = (new \yii\db\Query())
                        ->select(['id', 'image'])
                        ->from('product_images')
                        ->where('variation_id in (' . $ids . ')')
                        ->orderBy(['is_featured' => SORT_DESC, 'image' => SORT_ASC])
                        ->all();
            }
        }

        return $data;
    }

    function getProductVariations($id) {
        $data = (new \yii\db\Query())
                ->select(['id', 'size', 'color', 'qty', 'display_price', 'display_currency'])
                ->from('product_variation')
                ->where(['product_id' => $id])
//                ->andWhere(['status' => '1'])
                ->orderBy(['color' => SORT_ASC])
                ->all();
        return $data;
    }

    function getProductCategory() {
        $categoryArr = array();
        $category = (new \yii\db\Query())
                ->from('category')
                ->select(['id', 'name', 'parent_id'])
                ->where(['level' => 3])
                ->orderBy('id')
                ->all();

        if (count($category) > 0) {
            foreach ($category as $cat) {
                $subParent = (new \yii\db\Query())
                        ->from('category')
                        ->select(['name', 'parent_id'])
                        ->where(['id' => $cat['parent_id']])
                        ->one();
                $parent = (new \yii\db\Query())
                        ->from('category')
                        ->select(['name'])
                        ->where(['id' => $subParent['parent_id']])
                        ->one();
                $categoryArr[$cat['id']] = $parent['name'] . ' > ' . $subParent['name'] . ' > ' . $cat['name'];
            }
        }

        return $categoryArr;
    }

    /** function editProduct($id, $featuredImg) {
      $postData = $_POST;
      //        $removeSize = array();
      $sizeArr = (isset($_POST['multiple_size']) && count($_POST['multiple_size']) > 0) ? $_POST['multiple_size'] : array();

      $productVariation = (new \yii\db\Query())
      ->from('product_variation')
      ->select(['size'])
      ->where(['product_id' => $id])
      ->groupBy(['size'])
      ->all();
      ;
      foreach ($productVariation as $variation) {
      if (!in_array($variation['size'], $sizeArr)) {
      Yii::$app->db->createCommand('delete from product_variation where  size="' . $variation['size'] . '" and product_id="' . $id . '"')->execute();
      //                array_push($removeSize, $variation['size']);
      }
      }

      if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
      if ($featuredImg != "") {
      $this->delImage($id, "featured_image");
      if (file_exists(Yii::$app->basePath . '/web/images/products/' . $featuredImg)) {
      unlink(Yii::$app->basePath . '/web/images/products/' . $featuredImg);
      }
      }
      $target_dir = Yii::$app->basePath . '/web/images/products/';
      $target_file = $target_dir . basename($_FILES["image"]["name"]);
      $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
      $fileArr = explode('.', basename($_FILES["image"]["name"]));
      $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
      $filePath = $target_dir . $file;
      if (!move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
      Yii::$app->session->setFlash('error', 'Sorry, there was an error uploading your file.');
      return false;
      }
      $postParam['featured_image'] = $file;
      }

      $postParam['updated_date'] = date('Y-m-d H:i:s');
      $postParam['updated_by'] = Yii::$app->user->id;
      $postParam['name'] = $postData['name'];
      $postParam['description'] = $postData['description'];
      $postParam['sku'] = $postData['sku'];
      //        $postParam['category_id'] = $postData['category'];
      $postParam['status'] = $postData['status'];
      $postParam['product_attributes'] = serialize($postData['fits']);

      Yii::$app->db->createCommand()->update('products', $postParam, ['id' => $id])->execute();
      //        Yii::$app->db->createCommand()->update('product_variation', ['status' => '1'], ['product_id' => $id])->execute();
      if(isset($postData['variationQty'])){
      foreach ($postData['variationQty'] as $key => $val) {
      Yii::$app->db->createCommand()->update('product_variation', ['display_price' => $postData['variationPrice'][$key], 'qty' => $val, 'updated_date' => $postParam['updated_date'], 'updated_by' => $postParam['updated_by']], ['id' => $key])->execute();
      }
      }


      Yii::$app->session->setFlash('success', 'Product updated successfully.');
      return true;
      exit;
      }* */
    function editProduct($id, $featuredImg) {
        $postData = $_POST;

        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            if ($featuredImg != "") {
                $this->delImage($id, "featured_image");
                if (file_exists(Yii::$app->basePath . '/web/images/products/' . $featuredImg)) {
                    unlink(Yii::$app->basePath . '/web/images/products/' . $featuredImg);
                }
            }
            $target_dir = Yii::$app->basePath . '/web/images/products/';
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileArr = explode('.', basename($_FILES["image"]["name"]));
            $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
            $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
            $filePath = $target_dir . $file;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
                Yii::$app->session->setFlash('error', Yii::$app->params['fileUploadError']);
                return false;
            }
            $postParam['featured_image'] = $file;
        }

        $postParam['updated_date'] = date('Y-m-d H:i:s');
        $postParam['updated_by'] = Yii::$app->user->id;
        $postParam['name'] = $postData['name'];
        $postParam['description'] = $postData['description'];
        $postParam['sku'] = $postData['sku'];
        $postParam['status'] = $postData['status'];

        if ($postParam['status'] == '2') {
            $reason = $postData['drpReasons'];
            if ($reason == 'Other') {
                $reason = 'other-' . $postData['otherReason'];
            }
            $postParam['disapprove_reason'] = $reason;
        }
        
        Yii::$app->db->createCommand()->update('products', $postParam, ['id' => $id])->execute();

        Yii::$app->session->setFlash('success', Yii::$app->params['productUpdate']);
        return true;
    }

    public function delImage($id, $imgName) {
        Yii::$app->db->createCommand(
                'UPDATE products SET ' . $imgName . '="" WHERE id="' . $id . '"')->execute();

        return true;
    }

    public function delColorImage($id) {

        Yii::$app->db->createCommand('delete from product_images where id = ' . $id)->execute();
        return true;
    }

    function editColors($id) {
        $postData = $_POST;
        $sizeArr = array();
        $variation = array();
        $variation1 = array();

        if (isset($_POST['multiple_size']) && count($_POST['multiple_size']) > 0) {
//            echo 'aaaa';exit;
            $sizeArr = $_POST['multiple_size'];
            foreach ($sizeArr as $key => $val) {
                $variation[] = array($id, $postData['color_name'], $val, '0', 'S$', '0', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), Yii::$app->user->id);
            }
            Yii::$app->db->createCommand()->batchInsert('product_variation', ['product_id', 'color', 'size', 'display_price', 'display_currency', 'qty', 'created_date', 'updated_date', 'updated_by'], $variation)->execute();
            $insertId = Yii::$app->db->getLastInsertID();
        } else {
//            echo 'bbbb';exit;
            $variation1 = array('product_id' => $id, 'color' => $postData['color_name'], 'size' => '', 'display_price' => '0', 'display_currency' => 'S$', 'qty' => '0', 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => Yii::$app->user->id);

            if (!empty($variation1)) {
                Yii::$app->db->createCommand()->insert('product_variation', $variation1)->execute();
            }

            unset($variation1);
            unset($_POST);
            unset($postData);
            $insertId = Yii::$app->db->getLastInsertID();
        }


        $imgArr = array();
        $variation_id = $insertId;
        $created_date = date('Y-m-d H:i:s');
        $updated_date = date('Y-m-d H:i:s');
        $updated_by = Yii::$app->user->id;
        if (isset($_FILES['featured_image_color']['name']) && $_FILES['featured_image_color']['name'] != '') {
            $target_dir = Yii::$app->basePath . '/web/images/products/';
            $target_file = $target_dir . basename($_FILES["featured_image_color"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileArr = explode('.', basename($_FILES["featured_image_color"]["name"]));
            $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
            $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
            $filePath = $target_dir . $file;
            if (!move_uploaded_file($_FILES["featured_image_color"]["tmp_name"], $filePath)) {
                Yii::$app->session->setFlash('error', 'Sorry, there was an error uploading your file.');
            }
            $imgArr[] = array($variation_id, $file, '1', $created_date, $updated_date, $updated_by);
        }

        if (isset($_FILES['other_color_images']['name']) && $_FILES['other_color_images']['name'][0] != '') {
            $error = array();
            $extension = array("jpeg", "jpg", "png");

            foreach ($_FILES["other_color_images"]["tmp_name"] as $key => $tmp_name) {
                $target_dir = Yii::$app->basePath . '/web/images/products/';
                $file_name = $_FILES["other_color_images"]["name"][$key];
                $file_tmp = $_FILES["other_color_images"]["tmp_name"][$key];
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $fileArr = explode('.', basename($file_name));
                $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
                $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
                $filePath = $target_dir . $file;
                if (!move_uploaded_file($file_tmp, $filePath)) {
                    Yii::$app->session->setFlash('error', Yii::$app->params['fileUploadError']);
                }
                $imgArr[] = array($variation_id, $file, '0', $created_date, $updated_date, $updated_by);
            }
        }

        Yii::$app->db->createCommand()->batchInsert('product_images', ['variation_id', 'image', 'is_featured', 'created_date', 'updated_date', 'updated_by'], $imgArr)->execute();

        return TRUE;
    }

    function removeColor($color, $id) {
        Yii::$app->db->createCommand('delete from product_variation where color="' . $color . '" and product_id="' . $id . '"')->execute();
        return TRUE;
    }

    function changeSize($id) {
        $size = $_GET['size'];
        $add = $_GET['add'];
        $colors = $this->getProductColor($id);
        $size = str_replace('-', ' ', $size);
        if ($add == 'yes') {

            $variation = array();
            foreach ($colors['colors'] as $key => $val) {
                if (strtolower($_GET['size']) == 'one-size') {
                    Yii::$app->db->createCommand('delete from product_variation where product_id="' . $id . '" and color="' . $val['color'] . '" ')->execute();
                }
                $colorArr = array();
                $colorArr = (new \yii\db\Query())
                        ->from('product_variation')
                        ->select(['id'])
                        ->where(['color' => $val['color']])
                        ->andWhere(['size' => ''])
                        ->andWhere(['product_id' => $id])
                        ->one();
                if (isset($colorArr['id']) > 0) {
                    Yii::$app->db->createCommand()->update('product_variation', ['size' => $size], ['id' => $colorArr['id']])->execute();
                } else {

                    $count = (new \yii\db\Query())
                            ->from('product_variation')
                            ->where(['color' => $val['color']])
                            ->andWhere(['size' => $size])
                            ->andWhere(['product_id' => $id])
                            ->count();
                    if ($count <= 0) {
                        $variation[] = array($id, $val['color'], $size, '0', 'S$', '0', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), Yii::$app->user->id);
                    }
                }
            }

            if (count($variation) > 0) {
                Yii::$app->db->createCommand()->batchInsert('product_variation', ['product_id', 'color', 'size', 'display_price', 'display_currency', 'qty', 'created_date', 'updated_date', 'updated_by'], $variation)->execute();
            }
        } else {
            foreach ($colors['colors'] as $key => $val) {
                $sizeVariation = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('product_variation')
                        ->where(['color' => $val['color']])
                        ->andWhere(['size' => $size])
                        ->andWhere(['product_id' => $id])
                        ->one();

                $sizeVariationId = isset($sizeVariation['id']) ? $sizeVariation['id'] : '';
                Yii::$app->db->createCommand('delete from product_variation where color="' . $val['color'] . '" and size="' . $size . '" and product_id="' . $id . '"')->execute();
                if ($sizeVariationId != '') {
                    $countImg = (new \yii\db\Query())
                            ->from('product_images')
                            ->where(['variation_id' => $sizeVariationId])
                            ->count();
                    if ($countImg > 0) {
                        $newVariation = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('product_variation')
                                ->where(['color' => $val['color']])
                                ->andWhere(['product_id' => $id])
                                ->andWhere(['!=', 'id', $sizeVariationId])
                                ->one();
                        $newVariationId = isset($newVariation['id']) ? $newVariation['id'] : '';
                        if ($newVariationId != '') {
                            Yii::$app->db->createCommand()->update('product_images', ['variation_id' => $newVariationId], ['variation_id' => $sizeVariationId])->execute();
                        }
                    }
                }
            }
        }
    }

    function getVendorDetail($id) {
        $vendor = (new \yii\db\Query())
                ->from('vendors')
                ->select(['vendor_code', 'name', 'shop_name'])
                ->where(['id' => $id])
                ->one();
        return $vendor;
    }

    function saveVariation($id) {
        $postData = $_POST;
        $postParam['updated_date'] = date('Y-m-d H:i:s');
        $postParam['updated_by'] = Yii::$app->user->id;
        if (isset($postData['saveQtyAndPrice']) && $postData['saveQtyAndPrice'] == 'yes') {
            if (isset($postData['variationQty'])) {
                foreach ($postData['variationQty'] as $key => $val) {
                    Yii::$app->db->createCommand()->update('product_variation', ['display_price' => $postData['variationPrice'][$key], 'qty' => $val, 'updated_date' => $postParam['updated_date'], 'updated_by' => $postParam['updated_by']], ['id' => $key])->execute();
                }
            }
        } else if (isset($postData['saveSizeAndFits']) && $postData['saveSizeAndFits'] == 'yes') {

            $postParam['product_attributes'] = serialize($postData['fits']);

            Yii::$app->db->createCommand()->update('products', $postParam, ['id' => $id])->execute();
        }

        Yii::$app->session->setFlash('success', Yii::$app->params['productUpdate']);
        return true;
    }

    function getVendorList() {
        $vendor = (new \yii\db\Query())
                ->from('vendors')
                ->select(['id', 'vendor_code', 'shop_name'])
                ->all();
        return $vendor;
    }

    function saveProduct() {
        $postData = $_POST;

        $postParam['created_date'] = date('Y-m-d H:i:s');
        $postParam['updated_date'] = date('Y-m-d H:i:s');
        $postParam['updated_by'] = Yii::$app->user->id;
        $postParam['name'] = isset($postData['name']) ? $postData['name'] : '';
        $postParam['vendor_id'] = isset($postData['vendor']) ? $postData['vendor'] : '';
        $postParam['category_id'] = isset($postData['category']) ? $postData['category'] : '';
        $postParam['description'] = isset($postData['description']) ? $postData['description'] : '';
        $postParam['sku'] = isset($postData['sku']) ? $postData['sku'] : '';
        $postParam['status'] = isset($postData['status']) ? $postData['status'] : '0';

        $vendorCode = (new \yii\db\Query())
                ->from('vendors')
                ->select(['vendor_code'])
                ->where(['id' => $postParam['vendor_id']])
                ->one();
        if (empty($vendorCode)) {
            Yii::$app->session->setFlash('error', 'Please select vendor.');
            return FALSE;
        }
        $product = (new \yii\db\Query())
                ->from('products')
                ->select(['id'])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        if (empty($product)) {
            $postParam['product_code'] = $vendorCode['vendor_code'] . '-' . 1;
        } else {
            $productId = $product['id'];
            $postParam['product_code'] = $vendorCode['vendor_code'] . '-' . ($productId + 1);
        }

        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            $target_dir = Yii::$app->basePath . '/web/images/products/';
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileArr = explode('.', basename($_FILES["image"]["name"]));
            $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
            $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
            $filePath = $target_dir . $file;
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
                Yii::$app->session->setFlash('error', Yii::$app->params['fileUploadError']);
                return false;
            }
            $postParam['featured_image'] = $file;
        }

        Yii::$app->db->createCommand()->insert('products', $postParam)->execute();
        Yii::$app->session->setFlash('success', Yii::$app->params['productSave']);
        $insertId = Yii::$app->db->getLastInsertID();
        return $insertId;
    }

    function getVendorCategory($id) {

        $categoryArr = array();
        $category = (new \yii\db\Query())
                ->from('category')
                ->select(['category.id as id', 'category.name as name', 'category.parent_id as parent_id'])
                ->join('LEFT JOIN', 'vendor_assigned_category', 'vendor_assigned_category.category_id = category.id')
                ->where(['category.level' => 3])
                ->andWhere(['vendor_assigned_category.vendor_id' => $id])
                ->orderBy('category.id')
                ->all();

        if (count($category) > 0) {
            foreach ($category as $cat) {
                $subParent = (new \yii\db\Query())
                        ->from('category')
                        ->select(['name', 'parent_id'])
                        ->where(['id' => $cat['parent_id']])
                        ->one();
                $parent = (new \yii\db\Query())
                        ->from('category')
                        ->select(['name'])
                        ->where(['id' => $subParent['parent_id']])
                        ->one();
                $categoryArr[$cat['id']] = $parent['name'] . ' > ' . $subParent['name'] . ' > ' . $cat['name'];
            }
        }
        $vendorCat = array();
        foreach ($categoryArr as $key => $val) {
            $vendorCat[] = array('id' => $key, 'name' => $val);
        }
        return $vendorCat;
    }

}
