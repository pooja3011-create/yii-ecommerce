<?php

namespace app\models;

use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "collection".
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property string $created_date
 * @property string $updated_date
 * @property string $updated_by
 * @property string $status
 */
class Collection extends \yii\db\ActiveRecord {

    public $imageFiles;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'collection';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created_date', 'updated_date', 'updated_by'], 'safe'],
            [['status'], 'string'],
            [['title', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'image' => 'Image',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }

    /** get product category list */
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

    function getVendors($category) {

        $query = 'SELECT v.vendor_code, v.id, v.name, v.shop_name from vendors v 
            LEFT JOin vendor_assigned_category c on c.vendor_id=v.id
            WHERE c.category_id="' . $category . '"
            ';
        $vendors = Yii::$app->db->createCommand($query)->queryAll();
        return $vendors;
    }

    function getProducts($term, $category, $vendor) {
        $where = '';
        $query = 'SELECT id,name from products
            WHERE  name like "%' . $term . '%"';
        if ($category != '') {
            $where .= ' and category_id="' . $category . '"';
        }
        if ($vendor != '') {
            $where .= ' and $vendor_id="' . $vendor . '"';
        }
        $products = Yii::$app->db->createCommand($query)->queryAll();
        return $products;
    }

    function getProductDetail($productId) {
        $query = 'SELECT p.id,p.featured_image,p.name,p.product_code,c.name as category_name, v.name as vendor_name from products p 
             LEFT JOIN category c on c.id=p.category_id
             LEFT JOIN vendors v on v.id=p.vendor_id
            WHERE p.id="' . $productId . '"';
        $product = Yii::$app->db->createCommand($query)->queryOne();
        if (!empty($product)) {
            if (isset($product['featured_image']) && $product['featured_image'] != '') {
                $product['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true);
            } else {
                $product['featured_image'] = Url::to('@web/images/no_image.png', true);
            }
        }
        return $product;
    }

    function saveCollection() {
        $saveArr['title'] = isset($_POST['Collection']['title']) ? $_POST['Collection']['title'] : '';

        $count = (new \yii\db\Query())
                ->from('collection')
                ->where(['title' => $saveArr['title']])
                ->count();
        if ($count > 0) {
            Yii::$app->session->setFlash('error', Yii::$app->params['duplicatCollection']);
            return false;
        }
        $saveArr['status'] = isset($_POST['Collection']['status']) ? $_POST['Collection']['status'] : '';
        $saveArr['created_date'] = date('Y:m:d H:i:s');
        $saveArr['updated_date'] = date('Y:m:d H:i:s');
        $saveArr['updated_by'] = Yii::$app->user->id;

        Yii::$app->db->createCommand()->insert('collection', $saveArr)->execute();
        $collectionId = Yii::$app->db->getLastInsertID();

        $productArr = array();

        if (isset($_POST['collectionProduct']) && !empty($_POST['collectionProduct'])) {
            foreach ($_POST['collectionProduct'] as $key => $val) {
                $productArr[] = array($collectionId, $val);
            }
        }
        if (!empty($productArr)) {
            Yii::$app->db->createCommand()->batchInsert('collection_product', ['collection_id', 'product_id'], $productArr)->execute();
        }
        Yii::$app->session->setFlash('success', Yii::$app->params['saveCollection']);

        return $collectionId;
    }

    function getCollection($id) {
        $collection = (new \yii\db\Query())
                ->from('collection')
                ->select(['id', 'title', 'status', 'image'])
                ->where(['id' => $id])
                ->one();

        $query = 'SELECT p.id,p.featured_image,p.name,p.product_code,c.name as category_name, v.name as vendor_name from products p 
             LEFT JOIN category c on c.id=p.category_id
             LEFT JOIN vendors v on v.id=p.vendor_id
             LEFT JOIN collection_product cp on p.id=cp.product_id
            WHERE cp.collection_id="' . $id . '"';
        $products = Yii::$app->db->createCommand($query)->queryAll();
        $collection['products'] = $products;
        return $collection;
    }

    function editCollection($id) {
        $saveArr['title'] = isset($_POST['Collection']['title']) ? $_POST['Collection']['title'] : '';
        $count = (new \yii\db\Query())
                ->from('collection')
                ->where(['title' => $saveArr['title']])
                ->andWhere(['!=', 'id', $id])
                ->count();
        if ($count > 0) {
            Yii::$app->session->setFlash('error',Yii::$app->params['duplicatCollection']);
            return false;
        }
        $saveArr['status'] = isset($_POST['Collection']['status']) ? $_POST['Collection']['status'] : '';
        $saveArr['updated_date'] = date('Y:m:d H:i:s');
        $saveArr['updated_by'] = Yii::$app->user->id;

        Yii::$app->db->createCommand()->update('collection', $saveArr, ['id' => $id])->execute();

        $products = (new \yii\db\Query())
                ->select(['product_id'])
                ->from('collection_product')
                ->where(['collection_id' => $id])
                ->all();

        $collectionProduct = array();
        foreach ($products as $product) {
            array_push($collectionProduct, $product['product_id']);
        }

        $productArr = array();
        if (isset($_POST['collectionProduct']) && !empty($_POST['collectionProduct'])) {
            foreach ($_POST['collectionProduct'] as $key => $val) {
                if (!in_array($val, $collectionProduct)) {
                    $productArr[] = array($id, $val);
                } else {
                    $arrKey = array_search($val, $collectionProduct);
                    unset($collectionProduct[$arrKey]);
                }
            }
        }
        if (!empty($collectionProduct)) {
            $ids = implode(',', $collectionProduct);
            if ($ids != '') {
                Yii::$app->db->createCommand('delete from collection_product where collection_id="' . $id . '" and product_id IN (' . $ids . ')')->execute();
            }
        }
        if (!empty($productArr)) {
            Yii::$app->db->createCommand()->batchInsert('collection_product', ['collection_id', 'product_id'], $productArr)->execute();
        }
        Yii::$app->session->setFlash('success', Yii::$app->params['editCollection']);
        return true;
    }

    function deleteCollection($id) {
        Yii::$app->db->createCommand("delete from collection where id=" . $id)->execute();
        Yii::$app->db->createCommand("delete from collection_product where collection_id=" . $id)->execute();
        return TRUE;
    }

    public function delImage($id, $imgName) {
        Yii::$app->db->createCommand(
                'UPDATE collection SET ' . $imgName . '="" WHERE id="' . $id . '"')->execute();

        return true;
    }

}
