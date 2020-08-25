<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property integer $level
 * @property string $created_date
 * @property string $updated_date
 * @property string $status
 */
class Site extends \yii\db\ActiveRecord {

    /**
     * get slider
     * @return array of slider
     * * */
    function sliders() {
        $data = (new \yii\db\Query())
                ->select(['id', 'title', 'image', 'link'])
                ->from('slider')
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        return $data;
    }

    /*     * *
     * get new arrival products
     * * */

    function newProducts() {

        $query = 'SELECT p.id,p.name,p.featured_image,pa.display_price,pa.display_currency
FROM products p
left JOIN category c ON p.category_id = c.id 
LEFT JOIN order_products op ON p.id = op.product_id
'
                . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id Where display_price != "" and display_price != "0" Group by p.id  order by p.id desc limit 10';


        $products = Yii::$app->db->createCommand($query)->queryAll();
        return $products;
    }

    function collection() {
        $collection = (new \yii\db\Query())
                ->from('collection')
                ->select(['id', 'title', 'image'])
                ->where(['status' => '1'])
                ->all();

        return $collection;
    }

    function instagramFeed() {
        $url = 'https://api.instagram.com/v1/tags/android/media/recent/?access_token=3540462049.10aa48c.ab9300a6e47648f3bd4de01ef2cb8874';
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
        
    }

    /** get category list
     * @return array category list ( upto 2 level )
     * * */
    function categoryList() {
        $categoryArr = array();
        $mainCategory = (new \yii\db\Query())
                ->from('category')
                ->select(['id', 'name'])
                ->where(['level' => 1])
                ->orderBy('id')
                ->all();

        foreach ($mainCategory as $parent) {
            $category = (new \yii\db\Query())
                    ->from('category')
                    ->select(['id', 'name'])
                    ->where(['level' => 2])
                    ->andWhere(['parent_id' => $parent['id']])
                    ->orderBy('id')
                    ->all();
            if (!empty($category)) {
                $categoryArr[$parent['name']] = $category;
            }
        }
        return $categoryArr;
    }

    
    
}
