<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property string $name
 * @property string $product_code
 * @property integer $vendor_id
 * @property string $sku
 * @property string $description
 * @property string $featured_image
 * @property integer $category_id
 * @property string $product_attributes
 * @property string $disapprove_reason
 * @property string $created_date
 * @property string $updated_date
 * @property integer $updated_by
 * @property string $status
 */
class Products extends \yii\db\ActiveRecord {

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
            [['vendor_id', 'category_id', 'updated_by'], 'integer'],
            [['description', 'product_attributes', 'status'], 'string'],
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
            'vendor_id' => 'Vendor ID',
            'sku' => 'Sku',
            'description' => 'Description',
            'featured_image' => 'Featured Image',
            'category_id' => 'Category ID',
            'product_attributes' => 'Product Attributes',
            'disapprove_reason' => 'Disapprove Reason',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }

    /**
     * product list
     * @return array
     * * */
    function products($category, $child_cat = '') {
        $childCat = array();
        $productCat = array();
        $patentCat = (new \yii\db\Query())
                ->select(['id'])
                ->from('category')
                ->where(['canonical_url' => $category])
                ->one();
        if ($child_cat != '') {
            $childCat = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('category')
                    ->where(['canonical_url' => $child_cat])
                    ->andWhere(['parent_id' => $patentCat['id']])
                    ->all();
        } else {
            $childCat = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('category')
                    ->where(['parent_id' => $patentCat['id']])
                    ->all();
        }

        $childCatIds = '';
        foreach ($childCat as $child) {
            $childCatIds .= ($childCatIds != '') ? ',' . $child['id'] : $child['id'];
        }

        if ($childCatIds != '') {
            $productCat = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('category')
                    ->where('parent_id in (' . $childCatIds . ')')
                    ->all();
        }

        $productCatIds = '';
        foreach ($productCat as $cat) {
            $productCatIds .= ($productCatIds != '') ? ',' . $cat['id'] : $cat['id'];
        }

        $where = '';
        $orderBy = ' order by p.id desc';
        $start = 1;
        $pagination = 100;
        
        $filterOption['category'] = $productCatIds;
        if (isset($_GET['productSearch']) && $_GET['productSearch'] != '') {
            $searchParams = $_GET['productSearch'];
            $filterOption = $_GET['productSearch'];
            if (isset($searchParams['category']) && trim($searchParams['category']) != '') {
                $productCatIds = $searchParams['category'];
            }
            if (isset($searchParams['vendor']) && trim($searchParams['vendor']) != '') {
                $catIds = explode(',', $productCatIds);
                $str = '';
                foreach ($catIds as $key => $val) {
                    $str .= (trim($str) != '') ? ' Or ' : '';
                    $str .= ' (p.category_id="' . $val . '" AND p.vendor_id in (' . $searchParams['vendor'] . ')) ';
                }
                $where = ' Where (' . $str . ')';
            } else {
                $where = ' Where p.category_id in (' . $productCatIds . ')';
            }
            if (isset($searchParams['min_price']) && $searchParams['min_price'] != '') {
                $where .= ' and pa.display_price < "' . $searchParams['min_price'] . '"';
            }
            if (isset($searchParams['max_price']) && $searchParams['max_price'] != '') {
                $where .= ' and pa.display_price > "' . $searchParams['max_price'] . '"';
            }
            if (isset($searchParams['color']) && $searchParams['color'] != '') {
                $colors = '';
                $where .= ' and v.color in (' . $colors . ')';
            }
            if (isset($searchParams['size']) && $searchParams['size'] != '') {
                $size = '';
                $where .= ' and v.size in (' . $size . ')';
            }
        }

        Yii::$app->session->set('filterOption', $filterOption);
        if (trim($where) == '') {
            $where = ' Where p.category_id in (' . $productCatIds . ')';
        }

        $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.sku,pa.display_price,pa.display_currency,p.category_id,p.vendor_id
FROM products p
LEFT JOIN order_products op ON p.id = op.product_id
'
                . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id' .
                $where . ' Group by p.id  ' . $orderBy . ' limit ' . $start . ',' . $pagination;


        $products = Yii::$app->db->createCommand($query)->queryAll();

        return $products;
    }

    /**
     * searched products
     * @return array
     * * */
    function productSearch($keyword) {

        $keyword = urldecode($keyword);
        $where = ' Where p.name like "%' . $keyword . '%" OR p.description like "%' . $keyword . '%"';
        $orderBy = ' order by p.id desc';
        $start = 1;
        $pagination = 100;

        $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.sku,pa.display_price,pa.display_currency
FROM products p
LEFT JOIN order_products op ON p.id = op.product_id
'
                . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id' .
                $where . ' Group by p.id  ' . $orderBy . ' limit ' . $start . ',' . $pagination;


        $products = Yii::$app->db->createCommand($query)->queryAll();

        return $products;
    }

    /**
     * product detail
     * @return array
     * * */
    function productDetail($product_code) {
        $query = 'SELECT p.id,p.name,p.description,p.product_code,p.featured_image,p.sku,pa.display_price,pa.display_currency,v.name as vendor_name, v.vendor_code,v.shop_name, v.shop_description
FROM products p
LEFT JOIN order_products op ON p.id = op.product_id
'
                . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id 
    Left join vendors v On v.id = p.vendor_id
    where p.product_code="' . $product_code . '"';


        $products = Yii::$app->db->createCommand($query)->queryOne();
        $attr = Yii::$app->db->createCommand('select * from product_variation where product_id="' . $products['id'] . '"')->queryAll();
        $products['attr'] = $attr;
        return $products;
    }

    /**
     * category list
     * @return array
     * * */
    function category($category) {
        $childCat = '';
        $patentCat = (new \yii\db\Query())
                ->select(['id'])
                ->from('category')
                ->where(['canonical_url' => $category])
                ->one();
        if (!empty($patentCat)) {
            $childCat = (new \yii\db\Query())
                    ->select(['id', 'name'])
                    ->from('category')
                    ->where(['parent_id' => $patentCat['id']])
                    ->all();
        }
        return $childCat;
    }

    /**
     * size list
     * @return array
     * * */
    function sizeList() {
        $sizeArr = (new \yii\db\Query())
                ->select(['size'])
                ->from('product_variation')
                ->where(['!=', 'size', ''])
                ->groupBy(['size'])
                ->all();
        return $sizeArr;
    }

    /**
     * color list for product filter
     * @return array
     * * */
    function colorList() {
        $colorArr = (new \yii\db\Query())
                ->select(['color'])
                ->from('product_variation')
                ->where(['!=', 'color', ''])
                ->groupBy(['color'])
                ->all();
        return $colorArr;
    }

    /**
     * similar products in detail page
     * @return array
     * * */
    function similarProducts() {
        $filter = Yii::$app->session->get('filterOption');
    }

}
