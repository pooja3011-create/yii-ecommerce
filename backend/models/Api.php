<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Helper;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Country;

class Api extends Model {

    /**
     * get json array for service response
     * @data: result array
     * */
    public function getJson($data) {

        if (!isset($_REQUEST['debug'])) {
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;
        } else {
            echo '<pre>';
            print_r($data['data']);
            exit;
        }
    }

    /**
     * Functoin for web service message code 
     * */
    public function getStatusCodeMessage() {
        $codes = Array(
            200 => 'OK.',
            10 => 'Bad Request.',
            20 => 'Database exception!',
            30 => 'Unknown method exception!',
            40 => 'Unauthorized User.',
            402 => 'Payment Required.',
            403 => 'Forbidden.',
            404 => 'Record Not Found.',
            500 => 'Internal Server Error.',
            501 => 'Not Implemented.',
        );
        return $codes;
    }

    /**
     * generate access token
     * */
    public function generateToken() {
        $random = rand();
        $token = md5($random);
        $user = (new \yii\db\Query())
                ->from('vendors')
                ->where(['access_token' => $token])
                ->count();
        if ($user > 0) {
            $this->generateToken();
        }
        return $token;
    }

    /** check user authentication */
    public function validateUser($id, $token) {

        $data = (new \yii\db\Query())
                ->from('vendors')
                ->where(['id' => $id])
                ->andWhere(['access_token' => $token])
                ->count();
        if ($data > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /** function to send mails */
    public function sendMail($mailTo, $mailFrom, $subject, $params, $template) {
        \Yii::$app->mailer->compose($template, ['params' => $params])
                ->setFrom($mailFrom)
                ->setTo($mailTo)
                ->setSubject($subject)
//                ->setHtmlBody($content)
                ->send();
        return TRUE;
    }

    /*
     * category hierarchy
     *      */

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

    /**
     * vendor login
     * @email string
     * @password string
     * */
    function login($email, $password) {
        $helper = new Helper();
        $count = (new \yii\db\Query())
                ->from('vendors')
                ->where(['email' => $email])
                ->count();
        if ($count > 0) {
            $user = (new \yii\db\Query())
                    ->from('vendors')
                    ->select(['id', 'vendor_code', 'shop_logo_image', 'name', 'shop_name', 'password'])
                    ->where(['email' => $email])
                    ->one();

            $passwordHash = $helper->decryptIt($user['password']);
            if (trim($password) == $passwordHash) {
                $deviceType = isset($_REQUEST['device_type']) ? $_REQUEST['device_type'] : '';
                $deviceId = isset($_REQUEST['device_id']) ? $_REQUEST['device_id'] : '';
                $token = $this->generateToken();
                Yii::$app->db->createCommand()->update('vendors', ['access_token' => $token, 'device_type' => $deviceType, 'device_id' => $deviceId, 'updated_date' => date('Y-m-d H:i:s')], 'id=' . $user['id'])->execute();

                if (isset($user['shop_logo_image']) && $user['shop_logo_image'] != '') {
                    $shop_logo_image = Url::to('@web/images/vendors/' . $user['shop_logo_image'], true);
                } else {
                    $shop_logo_image = Url::to('@web/images/no_image.png', true);
                }
                $data = array(
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'shop_name' => $user['shop_name'],
                    'vendor_code' => $user['vendor_code'],
                    'logo' => $shop_logo_image,
                    'access_token' => $token
                );
                $this->getJson(array('status' => '1', 'data' => $data, 'message' => 'Vendor logged in successfully.'));
            } else {
                $this->getJson(array('status' => '0', 'message' => 'Invalid username or password.'));
            }
        } else {
            $this->getJson(array('status' => '0', 'message' => 'Email does not exist.'));
        }
    }

    /**
     * forgot password
     * @email string
     * */
    function forgotPassword($email) {
        $helper = new Helper();
        $user = (new \yii\db\Query())
                ->from('vendors')
                ->select(['id', 'name'])
                ->where(['email' => $email])
                ->one();

        if (!empty($user)) {
            $random = '1234';
            $password = $helper->encryptIt($random);
            $params['name'] = $user['name'];
            $params['password'] = $random;
            $mailTo = $email;
            $mailFrom = Yii::$app->params['adminEmail'];
            $subject = 'Reset password.';
            $this->sendMail($mailTo, $mailFrom, $subject, $params, '/site/vendorForgotPassword');

            Yii::$app->db->createCommand()->update('vendors', ['password' => $password, 'updated_date' => date('Y-m-d H:i:s')], 'id=' . $user['id'])->execute();

            $this->getJson(array('status' => '1', 'message' => 'New password has been sent to your registered email address.'));
        } else {
            $this->getJson(array('status' => '0', 'message' => 'Email does not exist.'));
        }
    }

    /**
     *  Vendor detail
     *  @id vendor id (string)
     *  @type vendor type detail (string)   
     * */
    function vendorDetail($id, $type) {

        if ($type == 'account_info') {
            $user = (new \yii\db\Query())
                    ->from('vendors')
                    ->select(['name', 'email', 'phone', 'gender'])
                    ->where(['id' => $id])
                    ->one();
            $user['gender'] = ucfirst($user['gender']);
        } else if ($type == 'bank_info') {
            $user = (new \yii\db\Query())
                    ->from('vendors')
                    ->select(['bank_name', 'account_number', 'account_holder_name', 'swift_code', 'account_notes'])
                    ->where(['id' => $id])
                    ->one();
        } else if ($type == 'shop_info') {
            $user = (new \yii\db\Query())
                    ->from('vendors')
                    ->select(['shop_name', 'country_id', 'shop_description', 'tax_vat_number', 'shop_banner_image', 'shop_logo_image'])
                    ->where(['id' => $id])
                    ->one();

            $query = 'SELECT c.id, c.name, c.parent_id
FROM category c
left JOIN `vendor_assigned_category` v ON v.category_id = c.id
WHERE v.vendor_id = "' . $id . '"';
            $category = Yii::$app->db->createCommand($query)->queryAll();
            $categoryArr = array();
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
                    array_push($categoryArr, $parent['name'] . ' > ' . $subParent['name'] . ' > ' . $cat['name']);
                }
            }
            $user['category'] = $categoryArr;
            if (isset($user['shop_banner_image']) && $user['shop_banner_image'] != '') {
                $user['shop_banner_image'] = Url::to('@web/images/vendors/' . $user['shop_banner_image'], true);
            } else {
                $user['shop_banner_image'] = '';
//                $user['shop_banner_image'] = Url::to('@web/images/no_image.png', true);
            }
            if (isset($user['shop_logo_image']) && $user['shop_logo_image'] != '') {
                $user['shop_logo_image'] = Url::to('@web/images/vendors/' . $user['shop_logo_image'], true);
            } else {
                $user['shop_logo_image'] = '';
//                $user['shop_logo_image'] = Url::to('@web/images/no_image.png', true);
            }
        }

        $this->getJson(array('status' => '1', 'data' => $user, 'message' => 'Success.'));
    }

    /**
     * Edit Vendor detail
     *  @id vendor id (string)
     *  @type vendor type detail (string) 
     * */
    function vendorEdit($id, $type) {
        if ($type == 'account_info') { // update account info
            if (isset($_REQUEST['name']) && $_REQUEST['name'] != '' && isset($_REQUEST['email']) && $_REQUEST['email'] != '') {
                $saveArr = array();
                $saveArr['name'] = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
                $saveArr['email'] = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
                $saveArr['phone'] = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : '';
                $saveArr['gender'] = isset($_REQUEST['gender']) ? $_REQUEST['gender'] : 'male';
                $saveArr['updated_date'] = date('Y-m-d H:i:s');
                $saveArr['updated_by'] = $id;

                if (isset($_REQUEST['oldPassword']) && $_REQUEST['oldPassword'] != '' && isset($_REQUEST['newPassword']) && $_REQUEST['newPassword'] != '') {
                    $helper = new Helper();
                    $user = (new \yii\db\Query())
                            ->from('vendors')
                            ->select(['password'])
                            ->where(['id' => $id])
                            ->one();

                    $oldPassword = $_REQUEST['oldPassword'];
                    $passwordHash = $helper->decryptIt($user['password']);

                    if ($passwordHash == $oldPassword) {
                        $newPassword = $_REQUEST['newPassword'];
                        $password = $helper->encryptIt($newPassword);
                        $saveArr['password'] = $password;
                    } else {
                        $this->getJson(array('status' => '0', 'message' => 'Invalid old password.'));
                    }
                }
                Yii::$app->db->createCommand()->update('vendors', $saveArr, 'id=' . $id)->execute();
            } else {
                $this->getJson(array('status' => '0', 'message' => 'Bad Request.'));
            }
        } else if ($type == 'bank_info') { // update bank info
            $saveArr = array();

            $saveArr['bank_name'] = isset($_REQUEST['bank_name']) ? $_REQUEST['bank_name'] : '';
            $saveArr['account_number'] = isset($_REQUEST['account_number']) ? $_REQUEST['account_number'] : '';
            $saveArr['account_holder_name'] = isset($_REQUEST['account_holder_name']) ? $_REQUEST['account_holder_name'] : '';
            $saveArr['swift_code'] = isset($_REQUEST['swift_code']) ? $_REQUEST['swift_code'] : '';
            $saveArr['account_notes'] = isset($_REQUEST['account_notes']) ? $_REQUEST['account_notes'] : '';
            $saveArr['updated_date'] = date('Y-m-d H:i:s');
            $saveArr['updated_by'] = $id;

            Yii::$app->db->createCommand()->update('vendors', $saveArr, 'id=' . $id)->execute();
        } else if ($type == 'shop_info') { // update vendor shop info
            if (isset($_REQUEST['country_id']) && $_REQUEST['country_id'] != '') {
                $saveArr = array();

                $saveArr['country_id'] = isset($_REQUEST['country_id']) ? $_REQUEST['country_id'] : '';
                $saveArr['tax_vat_number'] = isset($_REQUEST['tax_vat_number']) ? $_REQUEST['tax_vat_number'] : '';
                $saveArr['shop_description'] = isset($_REQUEST['shop_description']) ? $_REQUEST['shop_description'] : '';

                if (isset($_FILES['shop_logo_image']) && !empty($_FILES['shop_logo_image'])) {
                    $path = Yii::$app->basePath . "/web/images/vendors/";
                    $file = basename($_FILES['shop_logo_image']['name']);
                    $temp = explode(".", $file);
                    $newfilename = $id . 'logo_' . date('mdY_His') . '.' . end($temp);
                    $uploadfile = $path . $newfilename;
                    $photo = $newfilename;
                    $saveArr['shop_logo_image'] = $photo;
                    if (!move_uploaded_file($_FILES['shop_logo_image']['tmp_name'], $uploadfile)) {
                        $this->getJson(array('status' => '0', 'message' => 'not uploaded.'));
                    }
                }
                if (isset($_FILES['shop_banner_image']) && !empty($_FILES['shop_banner_image'])) {
                    $path = Yii::$app->basePath . "/web/images/vendors/";
                    $file = basename($_FILES['shop_banner_image']['name']);
                    $temp = explode(".", $file);
                    $newfilename = $id . 'banner_' . date('mdY_His') . '.' . end($temp);
                    $uploadfile = $path . $newfilename;
                    $photo = $newfilename;
                    $saveArr['shop_banner_image'] = $photo;
                    if (!move_uploaded_file($_FILES['shop_banner_image']['tmp_name'], $uploadfile)) {
                        $this->getJson(array('status' => '0', 'message' => 'not uploaded.'));
                    }
                }
                $saveArr['updated_date'] = date('Y-m-d H:i:s');
                $saveArr['updated_by'] = $id;

                Yii::$app->db->createCommand()->update('vendors', $saveArr, 'id=' . $id)->execute();
            } else {
                $this->getJson(array('status' => '0', 'message' => 'Bad Request.'));
            }
        }
        $user = (new \yii\db\Query())
                ->from('vendors')
                ->select(['shop_logo_image'])
                ->where(['id' => $id])
                ->one();
        if (isset($user['shop_logo_image']) && $user['shop_logo_image'] != '') {
            $user['shop_logo_image'] = Url::to('@web/images/vendors/' . $user['shop_logo_image'], true);
        } else {
            $user['shop_logo_image'] = Url::to('@web/images/no_image.png', true);
        }
        $this->getJson(array('status' => '1', 'data' => $user['shop_logo_image'], 'message' => 'Profile details updated successfully.'));
    }

    /**
     * Product list and search
     * @id vendor id (string)
     * */
    function products($id) {
        $products = array();
        $category = $this->getProductCategory();

        $vendorCat = (new \yii\db\Query())
                ->from('vendor_assigned_category')
                ->where(['vendor_id' => $id])
                ->count();
        if ($vendorCat > 0) {
            $catAssigned = '1';
        } else {
            $catAssigned = '0';
        }

        $pagination = isset(Yii::$app->params['pagination']) ? Yii::$app->params['pagination'] : 10;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '1';
        $start = ($page * $pagination) - $pagination;

        $where = ' WHERE p.vendor_id= "' . $id . '"';
        $orderBy = ' order by p.id desc';
        if (isset($_REQUEST['sort']) && $_REQUEST['sort'] != '') {
            if ($_REQUEST['sort'] == 'price_asc') {
                $orderBy = ' order by pa.display_price asc';
            } else if ($_REQUEST['sort'] == 'price_desc') {
                $orderBy = ' order by pa.display_price desc';
            } else if ($_REQUEST['sort'] == 'popular_asc') {
                $orderBy = ' order by product_sell asc';
            } else if ($_REQUEST['sort'] == 'popular_desc') {
                $orderBy = ' order by product_sell desc';
            }
        }

        if (isset($_REQUEST['category']) && $_REQUEST['category'] != '') {
            $where .= ' and p.category_id in (' . $_REQUEST['category'] . ')';
        }

        if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
//            $where .= ' and p.status="' . $_REQUEST['status'] . '"';
            $status = explode(',', $_REQUEST['status']);
            $str = '';
            foreach ($status as $key => $val) {
                $str .= ($str != '') ? ',' . "'" . $val . "'" : "'" . $val . "'";
            }
            if ($str != '') {
                $where .= ' and p.status in(' . $str . ')';
            }
        }

        if (isset($_REQUEST['product_name']) && $_REQUEST['product_name'] != '') {
            $where .= ' and (p.name LIKE "%' . $_REQUEST['product_name'] . '%" OR p.product_code ="' . $_REQUEST['product_name'] . '" OR p.sku ="' . $_REQUEST['product_name'] . '")';
        }

        if (isset($_REQUEST['duration']) && $_REQUEST['duration'] != '') {
            $date = date('Y-m-d');
            $where .= ' and DATEDIFF("' . $date . '", DATE_FORMAT( p.created_date, "%Y-%m-%d" )) <= ' . $_REQUEST['duration'];
        }

        $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.sku,p.status,c.id as category,pa.display_price,pa.display_currency,COUNT( op.product_id ) as product_sell
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
    ON p.id=pa.product_id' .
                $where . ' Group by p.id  ' . $orderBy . ' limit ' . $start . ',' . $pagination;


        $products = Yii::$app->db->createCommand($query)->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(p.id) as cnt
FROM products p
left JOIN category c ON p.category_id = c.id' . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id' . $where)->queryOne();
        $totalPage = isset($count['cnt']) ? ceil($count['cnt'] / $pagination) : 0;

        if (!empty($products)) {
            $i = 0;
            foreach ($products as $product) {
                $products[$i]['category'] = $category[$product['category']];
                if ($product['status'] == '0') {
                    $products[$i]['status'] = 'Pending';
                } else if ($product['status'] == '1') {
                    $products[$i]['status'] = 'Approved';
                } else if ($product['status'] == '2') {
                    $products[$i]['status'] = 'Disapproved';
                } else if ($product['status'] == '3') {
                    $products[$i]['status'] = 'Active';
                } else {
                    $products[$i]['status'] = 'Inactive';
                }

                if ($product['featured_image'] != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $product['featured_image'])) {
//                    $products[$i]['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true) . '?t=' . date('dmYHis');
                    $products[$i]['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true);
                } else {
                    $products[$i]['featured_image'] = Url::to('@web/images/no_image.png', true);
//                    $products[$i]['featured_image'] = Url::to('@web/images/no_image.png', true) . '?t=' . date('dmYHis');
                }
                if ($products[$i]['sku'] != '') {
                    $products[$i]['sku'] = (string) $products[$i]['sku'];
                }
                if ($products[$i]['display_price'] != '') {
                    $products[$i]['display_price'] = $products[$i]['display_currency'] . $products[$i]['display_price'];
                } else {
                    $products[$i]['display_price'] = '';
                }

                $i++;
            }

            $this->getJson(array('status' => '1', 'data' => $products, 'catAssigned' => $catAssigned, 'totalPage' => $totalPage, 'message' => 'Success.'));
        } else {
            $this->getJson(array('status' => '1', 'data' => $products, 'catAssigned' => $catAssigned, 'message' => 'No product found.'));
        }
    }

    /**
     * Product detail
     * @id product id (int)
     * */
    function productDetail($id) {
        $productArr = array();
        $measurment = array();
        $color = '';
        $colorArr = array();

        $category = $this->getProductCategory();
        $query = 'SELECT p.name,p.product_code,p.featured_image,p.description,p.sku,p.product_attributes,c.id as category,p.status
FROM products p
left JOIN category c ON p.category_id = c.id 
Where p.id=' . $id;
        $product = Yii::$app->db->createCommand($query)->queryOne();

        // product featured image
        if ($product['featured_image'] != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $product['featured_image'])) {
            $image = Url::to('@web/images/products/' . $product['featured_image'], true);
        } else {
            $image = Url::to('@web/images/no_image.png', true);
        }

        //get product variations
        $variation = (new \yii\db\Query())
                ->from('product_variation')
                ->select(['color', 'size', 'display_price', 'qty'])
                ->where(['product_id' => $id])
                ->all();

        // prepare color array
        $colors = (new \yii\db\Query())
                ->from('product_variation')
                ->select(['id', 'color'])
                ->where(['product_id' => $id])
                ->groupBy(['color'])
                ->orderBy(['id' => SORT_ASC])
                ->all();

        foreach ($colors as $data) {
            $color .= ($color != '') ? ',' . $data['color'] : $data['color'];
            $colorImg = (new \yii\db\Query())
                    ->from('product_images')
                    ->select(['id', 'image'])
                    ->where(['variation_id' => $data['id']])
                    ->orderBy(['image' => SORT_ASC])
                    ->all();
            $imgArr = array();
            foreach ($colorImg as $img) {
                if ($img['image'] != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $img['image'])) {
                    $imgArr[] = array(
                        'id' => $img['id'],
                        'url' => Url::to('@web/images/products/' . $img['image'], true)
                    );
                } else {
                    $imgArr[] = array(
                        'id' => $img['id'],
                        'url' => Url::to('@web/images/products/no_image.png', true)
                    );
                }
            }
            $colorArr[] = array(
                'name' => $data['color'],
                'images' => $imgArr
            );
        }

        // prepare measurment array
        $product['product_attributes'] = unserialize($product['product_attributes']);

        if (!empty($product['product_attributes']) && !empty($colorArr)) {
            foreach ($product['product_attributes'] as $key => $val) {
                $temp = explode('-', $key);
                $temp[1] = str_replace('_', " ", $temp[1]);
                $measurment[$temp[0]][$temp[1]] = $val;
            }
        }
        $productArr = array(
            'name' => $product['name'],
            'sku' => $product['sku'],
            'status' => $product['status'],
            'description' => $product['description'],
            'category' => $category[$product['category']],
            'category_id' => $product['category'],
            'image' => $image,
            'colors' => $color,
            'colorArr' => $colorArr,
            'price_qty' => $variation,
            'measurement' => (object) $measurment,
        );
        $this->getJson(array('status' => '1', 'data' => $productArr, 'message' => 'Success.'));
    }

    /**
     * Add Product
     * @postParam array
     * */
    function productAdd($postParam) {
        set_time_limit(0);

        $saveArr['name'] = isset($postParam['name']) ? $postParam['name'] : '';
        $saveArr['sku'] = isset($postParam['sku']) ? $postParam['sku'] : '';
        $saveArr['category_id'] = isset($postParam['category']) ? $postParam['category'] : '';
        $saveArr['description'] = isset($postParam['description']) ? $postParam['description'] : '';
        $saveArr['vendor_id'] = isset($postParam['user_id']) ? $postParam['user_id'] : '';
        $saveArr['created_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $saveArr['vendor_id'];
        $saveArr['status'] = '0';
        $saveArr['featured_image'] = '';

        /** generate product code start */
        $vendorCode = (new \yii\db\Query())
                ->from('vendors')
                ->select(['vendor_code', 'product_approval'])
                ->where(['id' => $saveArr['vendor_id']])
                ->one();

        if (empty($vendorCode)) {
            $this->getJson(array('status' => '0', 'message' => 'Please enter vendor id.'));
        }
        if ($vendorCode['product_approval'] == 'not required') {
            $saveArr['status'] = '1';
        }
        $vendor_id = $postParam['user_id'];
        $count = (new \yii\db\Query())
                ->from('products')
                ->where(['sku' => $saveArr['sku']])
                ->andWhere(['vendor_id' => $vendor_id])
                ->count();

        if ($count > 0) {
            $this->getJson(array('status' => '0', 'message' => 'There is already another product having same SKU number. Please check.'));
        }

        $product = (new \yii\db\Query())
                ->from('products')
                ->select(['id'])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        if (empty($product)) {
            $saveArr['product_code'] = $vendorCode['vendor_code'] . '-' . 1;
        } else {
            $productId = $product['id'];
            $saveArr['product_code'] = $vendorCode['vendor_code'] . '-' . ($productId + 1);
        }
        /** generate product code end */
        // upload featured image
        if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image'])) {
            $target_dir = Yii::$app->basePath . '/web/images/products/';
            $target_file = $target_dir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileArr = explode('.', basename($_FILES["featured_image"]["name"]));
            $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
            $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
            $filePath = $target_dir . $file;
            if (!move_uploaded_file($_FILES["featured_image"]["tmp_name"], $filePath)) {
                $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
            }
            $saveArr['featured_image'] = $file;
        }

        Yii::$app->db->createCommand()->insert('products', $saveArr)->execute();
        $product_id = Yii::$app->db->getLastInsertID();

        $measurement = isset($_REQUEST['measurement']) ? $_REQUEST['measurement'] : '';
        $priceQty = isset($_REQUEST['price_qty']) ? $_REQUEST['price_qty'] : '';


        $measurementArr = array();
        $priceQtyArr = array();
        $colorArr = array();

        if ($measurement != '') {
            $measurementArr = json_decode($measurement);
        }
        if ($priceQty != '') {
            $priceQtyArr = json_decode($priceQty);
        }

        $productAttr = array();

        // add product variations
        if (!empty($priceQtyArr)) {
            foreach ($priceQtyArr as $variation) {
                Yii::$app->db->createCommand()->insert('product_variation', ['product_id' => $product_id, 'color' => $variation->color, 'size' => $variation->size, 'display_price' => $variation->display_price, 'qty' => $variation->qty, 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']])->execute();
            }
        }
        // add product attributes
        if (!empty($measurementArr)) {
            if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'ios') {
                foreach ($measurementArr as $temp) {
                    $key = key($temp);
                    $val = $temp->$key;
                    foreach ($val as $k => $v) {
                        $attr = str_replace(' ', '_', $k);
                        $productAttr[$key . '-' . $attr] = $v;
                    }
                }
            } else {
                foreach ($measurementArr as $key => $val) {
                    foreach ($val as $k => $v) {
                        $attr = str_replace(' ', '_', $k);
                        $productAttr[$key . '-' . $attr] = $v;
                    }
                }
            }
        }
        if (!empty($productAttr)) {
            $saveArr['product_attributes'] = serialize($productAttr);
        }
        Yii::$app->db->createCommand()->update('products', $saveArr, ['id' => $product_id])->execute();

        /** add product variations images start */
        $colorArr = (new \yii\db\Query())
                ->select(['color'])
                ->from(['product_variation'])
                ->where(['product_id' => $product_id])
                ->groupBy(['color'])
                ->all();

        foreach ($colorArr as $color) {
            $colorName = str_replace(' ', '_', $color['color']);
            $colorVariation = (new \yii\db\Query())
                    ->select(['id'])
                    ->from(['product_variation'])
                    ->where(['product_id' => $product_id])
                    ->andWhere(['color' => $color['color']])
                    ->orderBy(['id' => SORT_ASC])
                    ->one();
            $variationId = isset($colorVariation['id']) ? $colorVariation['id'] : '';
            if ($variationId != '') {
                $saveColorArr = array();
                if (isset($_FILES['color-' . $colorName . '-featuredImg']) && !empty($_FILES['color-' . $colorName . '-featuredImg'])) {
                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-featuredImg']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-featuredImg-' . $product_id . '.' . $imageFileType;
                    $filePath = $target_dir . $file;
                    if (!move_uploaded_file($_FILES['color-' . $colorName . '-featuredImg']["tmp_name"], $filePath)) {
                        $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                    }
                    $saveColorArr[] = array($variationId, $file, '1', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $postParam['user_id']);
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg1']) && !empty($_FILES['color-' . $colorName . '-otherImg1'])) {

                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg1']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg1-' . $product_id . '.' . $imageFileType;
                    $filePath = $target_dir . $file;
                    if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg1']["tmp_name"], $filePath)) {
                        $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                    }
                    $saveColorArr[] = array($variationId, $file, '0', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $postParam['user_id']);
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg2']) && !empty($_FILES['color-' . $colorName . '-otherImg2'])) {

                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg2']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg2-' . $product_id . '.' . $imageFileType;
                    $filePath = $target_dir . $file;
                    if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg2']["tmp_name"], $filePath)) {
                        $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                    }
                    $saveColorArr[] = array($variationId, $file, '0', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $postParam['user_id']);
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg3']) && !empty($_FILES['color-' . $colorName . '-otherImg3'])) {

                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg3']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg3-' . $product_id . '.' . $imageFileType;
                    $filePath = $target_dir . $file;
                    if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg3']["tmp_name"], $filePath)) {
                        $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                    }
                    $saveColorArr[] = array($variationId, $file, '0', date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $postParam['user_id']);
                }
                Yii::$app->db->createCommand()->batchInsert('product_images', ['variation_id', 'image', 'is_featured', 'created_date', 'updated_date', 'updated_by'], $saveColorArr)->execute();
            }
        }
        /** add product variations images start */
        $this->getJson(array('status' => '1', 'message' => 'Product saved successfully.'));
    }

    /**
     * Category list
     * 
     * */
    function category($id) {
        $categoryArr = array();
        $categoryArr2 = array();

        $vendorCat = (new \yii\db\Query())
                ->from('vendor_assigned_category')
                ->select(['category_id', 'vendor_id'])
                ->where(['vendor_id' => $id])
                ->orderBy('category_id')
                ->all();
        $listData = ArrayHelper::map($vendorCat, 'category_id', 'vendor_id');
        $array_keys = array_keys($listData);

        $catIds = implode(',', $array_keys);

        $mainCategory = (new \yii\db\Query())
                ->from('category')
                ->select(['id', 'name'])
                ->where(['level' => 1])
                ->orderBy('id')
                ->all();

        foreach ($mainCategory as $parent) {
            $subCat = array();
            $category = (new \yii\db\Query())
                    ->from('category')
                    ->select(['id', 'name'])
                    ->where(['level' => 2])
                    ->andWhere(['parent_id' => $parent['id']])
                    ->orderBy('id')
                    ->all();

            foreach ($category as $cat) {
                $childCat = (new \yii\db\Query())
                        ->from('category')
                        ->select(['id', 'name'])
                        ->where(['level' => 3])
                        ->andWhere(['parent_id' => $cat['id']])
                        ->andWhere(" id in (" . $catIds . ")")
                        ->orderBy('id')
                        ->all();

                if (!empty($childCat)) {
                    $subCat[] = array('name' => $cat['name'], 'child' => $childCat);
                    $categoryArr[$parent['name']][$cat['name']] = $childCat;
                }
            }
            if (!empty($subCat)) {
                $categoryArr2[] = array('name' => $parent['name'], 'subCat' => $subCat);
            }
        }

        if (!empty($categoryArr)) {
            $this->getJson(array('status' => '1', 'data' => $categoryArr, 'data_ios' => $categoryArr2, 'message' => 'Success.'));
        } else {
            $this->getJson(array('status' => '1', 'data' => $categoryArr, 'data_ios' => $categoryArr2, 'message' => 'No category found.'));
        }
    }

    /**
     * Edit Product
     * @postParam array
     * */
    function productEdit($postParam) {

        $saveArr['name'] = isset($postParam['name']) ? $postParam['name'] : '';
        $saveArr['sku'] = isset($postParam['sku']) ? $postParam['sku'] : '';
        $saveArr['category_id'] = isset($postParam['category']) ? $postParam['category'] : '';
        $saveArr['description'] = isset($postParam['description']) ? $postParam['description'] : '';
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $postParam['user_id'];
        if (isset($postParam['status']) && $postParam['status'] != '') {
            $saveArr['status'] = $postParam['status'];
        }
        $product_id = $postParam['product_id'];
        $vendor_id = $postParam['user_id'];
        $count = (new \yii\db\Query())
                ->from('products')
                ->where(['sku' => $saveArr['sku']])
                ->andWhere(['!=', 'id', $product_id])
                ->andWhere(['vendor_id' => $vendor_id])
                ->count();

        if ($count > 0) {
            $this->getJson(array('status' => '0', 'message' => 'There is already another product having same SKU number. Please check.'));
        }
        // upload featured image
        if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) {
            $target_dir = Yii::$app->basePath . '/web/images/products/';
            $target_file = $target_dir . basename($_FILES["featured_image"]["name"]);
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
            $fileArr = explode('.', basename($_FILES["featured_image"]["name"]));
            $fileArr[0] = preg_replace("/[^a-zA-Z0-9-_]+/", "", $fileArr[0]);
            $file = $fileArr[0] . '_' . date('mdY_His') . '.' . $imageFileType;
            $filePath = $target_dir . $file;
            if (!move_uploaded_file($_FILES["featured_image"]["tmp_name"], $filePath)) {
                $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
            }
            $saveArr['featured_image'] = $file;
        }

        $measurement = isset($_REQUEST['measurement']) ? $_REQUEST['measurement'] : '';
        $priceQty = isset($_REQUEST['price_qty']) ? $_REQUEST['price_qty'] : '';

        $measurementArr = array();
        $priceQtyArr = array();
        $colorArr = array();

        if ($measurement != '') {
            $measurementArr = json_decode($measurement);
        }
        if ($priceQty != '') {
            $priceQtyArr = json_decode($priceQty);
        }

        // edit product variation
        if (!empty($priceQtyArr)) {
            $productColor = (new \yii\db\Query())
                    ->select(['color'])
                    ->from('product_variation')
                    ->where(['product_id' => $product_id])
                    ->groupBy(['color'])
                    ->all();
            $productColorArr = array();
            foreach ($productColor as $colors) {
                $productColorArr[] = $colors['color'];
            }
            $productSize = (new \yii\db\Query())
                    ->select(['size', 'id'])
                    ->from('product_variation')
                    ->where(['product_id' => $product_id])
                    ->all();
            $productSizeArr = array();
            foreach ($productSize as $size) {
                $productSizeArr[$size['id']] = $size['size'];
            }

            $flipped = array_flip($productSizeArr);

//            print_r($flipped);

            foreach ($priceQtyArr as $variation) {
                $index = array_search($variation->color, $productColorArr);
                if ($index !== false) {
                    unset($productColorArr[$index]);
                }
                if (isset($flipped[$variation->size])) {
                    unset($flipped[$variation->size]);
                }
                $count = (new \yii\db\Query())
                        ->from('product_variation')
                        ->where(['product_id' => $product_id])
                        ->andWhere(['color' => $variation->color])
                        ->andWhere(['size' => $variation->size])
                        ->count();
                if ($count > 0) {
                    Yii::$app->db->createCommand()->update('product_variation', ['display_price' => $variation->display_price, 'qty' => $variation->qty], ['product_id' => $product_id, 'color' => $variation->color, 'size' => $variation->size])->execute();
                } else {
                    Yii::$app->db->createCommand()->insert('product_variation', ['product_id' => $product_id, 'color' => $variation->color, 'size' => $variation->size, 'display_price' => $variation->display_price, 'qty' => $variation->qty, 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']])->execute();
                }
            }

            // remove deleted colors start
            if (!empty($productColorArr)) {
                $colorsName = '';
                foreach ($productColorArr as $key => $val) {
                    $colorsName .= ($colorsName != '') ? ", '" . $val . "'" : "'" . $val . "'";
                    $productColorId = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('product_variation')
                            ->where(['product_id' => $product_id])
                            ->andWhere(['color' => $val])
                            ->all();
                    $colorIds = '';
                    foreach ($productColorId as $colorsId) {
                        $colorIds .= ($colorIds != '') ? ',' . $colorsId['id'] : $colorsId['id'];
                    }
                    Yii::$app->db->createCommand('delete from product_images where  variation_id IN (' . $colorIds . ')')->execute();
                }
                Yii::$app->db->createCommand('delete from product_variation where product_id="' . $product_id . '" and color IN (' . $colorsName . ')')->execute();
            }
            // remove deleted colors end
            // remove deleted size start

            if (!empty($flipped)) {
                foreach ($flipped as $k => $v) {
                    $productColor = (new \yii\db\Query())
                            ->select(['color'])
                            ->from('product_variation')
                            ->where(['product_id' => $product_id])
                            ->where(['size' => $k])
                            ->groupBy(['color'])
                            ->all();
                    foreach ($productColor as $color) {
                        $sizeVariation = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('product_variation')
                                ->where(['color' => $color['color']])
                                ->andWhere(['size' => $k])
                                ->andWhere(['product_id' => $product_id])
                                ->one();
                        $sizeVariationId = isset($sizeVariation['id']) ? $sizeVariation['id'] : '';

                        if ($sizeVariationId != '') {
                            $countImg = (new \yii\db\Query())
                                    ->from('product_images')
                                    ->where(['variation_id' => $sizeVariationId])
                                    ->count();
                            if ($countImg > 0) {
                                $newVariation = (new \yii\db\Query())
                                        ->select(['id'])
                                        ->from('product_variation')
                                        ->where(['color' => $color['color']])
                                        ->andWhere(['product_id' => $product_id])
                                        ->andWhere(['!=', 'id', $sizeVariationId])
                                        ->one();
                                $newVariationId = isset($newVariation['id']) ? $newVariation['id'] : '';
                                if ($newVariationId != '') {
                                    Yii::$app->db->createCommand()->update('product_images', ['variation_id' => $newVariationId], ['variation_id' => $sizeVariationId])->execute();
                                }
                            }
                        }
                    }
                    Yii::$app->db->createCommand('delete from product_variation where  size="' . $k . '" and product_id="' . $product_id . '"')->execute();
                }
            }
            // remove deleted size end
        }

        // edit product attributes
        $productAttr = array();
        if (!empty($measurementArr)) {
            if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'ios') {
                foreach ($measurementArr as $temp) {
                    $key = key($temp);
                    $val = $temp->$key;
                    foreach ($val as $k => $v) {
                        $attr = str_replace(' ', '_', $k);
                        $productAttr[$key . '-' . $attr] = $v;
                    }
                }
            } else {
                foreach ($measurementArr as $key => $val) {
                    foreach ($val as $k => $v) {
                        $attr = str_replace(' ', '_', $k);
                        $productAttr[$key . '-' . $attr] = $v;
                    }
                }
            }
        }

        if (!empty($productAttr)) {
            $saveArr['product_attributes'] = serialize($productAttr);
        }
        Yii::$app->db->createCommand()->update('products', $saveArr, ['id' => $product_id])->execute();

        /** edit product variations images start */
        $colorArr = (new \yii\db\Query())
                ->select(['color'])
                ->from(['product_variation'])
                ->where(['product_id' => $product_id])
                ->groupBy(['color'])
                ->all();

        foreach ($colorArr as $color) {
//            $colorName = $color['color'];
            $colorName = str_replace(' ', '_', $color['color']);
            $colorVariation = (new \yii\db\Query())
                    ->select(['id'])
                    ->from(['product_variation'])
                    ->where(['product_id' => $product_id])
                    ->andWhere(['color' => $color['color']])
                    ->orderBy(['id' => SORT_ASC])
                    ->one();
            $variationId = isset($colorVariation['id']) ? $colorVariation['id'] : '';
            if ($variationId != '') {

                if (isset($_FILES['color-' . $colorName . '-featuredImg']) && !empty($_FILES['color-' . $colorName . '-featuredImg'])) {
                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-featuredImg']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-featuredImg-' . $product_id . '.' . $imageFileType;

                    if (isset($_REQUEST['color-' . $colorName . '-featuredImg']) && $_REQUEST['color-' . $colorName . '-featuredImg'] != '') {
                        $imagePath = $_REQUEST['color-' . $colorName . '-featuredImg'];
                        $imagePathArr = explode('/', $imagePath);
                        $oldFile = end($imagePathArr);
                        if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                            unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                        }
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-featuredImg']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }

                        Yii::$app->db->createCommand()->update('product_images', ['updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']], ['image' => $file])->execute();
                    } else {
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-featuredImg']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        $saveColorArr = array('variation_id' => $variationId, 'image' => $file, 'is_featured' => '1', 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']);
                        Yii::$app->db->createCommand()->insert('product_images', $saveColorArr)->execute();
                    }
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg1']) && !empty($_FILES['color-' . $colorName . '-otherImg1'])) {
                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg1']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg1-' . $product_id . '.' . $imageFileType;
                    if (isset($_REQUEST['color-' . $colorName . '-otherImg1']) && $_REQUEST['color-' . $colorName . '-otherImg1'] != '') {
                        $imagePath = $_REQUEST['color-' . $colorName . '-otherImg1'];
                        $imagePathArr = explode('/', $imagePath);
                        $oldFile = end($imagePathArr);
                        if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                            unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                        }
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg1']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        Yii::$app->db->createCommand()->update('product_images', ['updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']], ['image' => $file])->execute();
                    } else {
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg1']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        $saveColorArr = array('variation_id' => $variationId, 'image' => $file, 'is_featured' => '0', 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']);
                        Yii::$app->db->createCommand()->insert('product_images', $saveColorArr)->execute();
                    }
                } else if (isset($_REQUEST['color-' . $colorName . '-otherImg1']) && $_REQUEST['color-' . $colorName . '-otherImg1'] != '') {
                    $imagePath = $_REQUEST['color-' . $colorName . '-otherImg1'];
                    $imagePathArr = explode('/', $imagePath);
                    $oldFile = end($imagePathArr);
                    if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                        unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                    }
                    Yii::$app->db->createCommand('delete from product_images where image = "' . $oldFile . '" ')->execute();
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg2']) && !empty($_FILES['color-' . $colorName . '-otherImg2'])) {
                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg2']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg2-' . $product_id . '.' . $imageFileType;
                    if (isset($_REQUEST['color-' . $colorName . '-otherImg2']) && $_REQUEST['color-' . $colorName . '-otherImg2'] != '') {
                        $imagePath = $_REQUEST['color-' . $colorName . '-otherImg2'];
                        $imagePathArr = explode('/', $imagePath);
                        $oldFile = end($imagePathArr);
                        if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                            unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                        }
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg2']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        Yii::$app->db->createCommand()->update('product_images', ['updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']], ['image' => $file])->execute();
                    } else {
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg2']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        $saveColorArr = array('variation_id' => $variationId, 'image' => $file, 'is_featured' => '0', 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']);
                        Yii::$app->db->createCommand()->insert('product_images', $saveColorArr)->execute();
                    }
                } else if (isset($_REQUEST['color-' . $colorName . '-otherImg2']) && $_REQUEST['color-' . $colorName . '-otherImg2'] != '') {
                    $imagePath = $_REQUEST['color-' . $colorName . '-otherImg2'];
                    $imagePathArr = explode('/', $imagePath);
                    $oldFile = end($imagePathArr);
                    if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                        unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                    }
                    Yii::$app->db->createCommand('delete from product_images where image = "' . $oldFile . '" ')->execute();
                }
                if (isset($_FILES['color-' . $colorName . '-otherImg3']) && !empty($_FILES['color-' . $colorName . '-otherImg3'])) {
                    $target_dir = Yii::$app->basePath . '/web/images/products/';
                    $target_file = $target_dir . basename($_FILES['color-' . $colorName . '-otherImg3']["name"]);
                    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                    $file = 'color-' . $colorName . '-otherImg3-' . $product_id . '.' . $imageFileType;
                    if (isset($_REQUEST['color-' . $colorName . '-otherImg3']) && $_REQUEST['color-' . $colorName . '-otherImg3'] != '') {
                        $imagePath = $_REQUEST['color-' . $colorName . '-otherImg3'];
                        $imagePathArr = explode('/', $imagePath);
                        $oldFile = end($imagePathArr);
                        if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                            unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                        }
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg3']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        Yii::$app->db->createCommand()->update('product_images', ['updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']], ['image' => $file])->execute();
                    } else {
                        $filePath = $target_dir . $file;
                        if (!move_uploaded_file($_FILES['color-' . $colorName . '-otherImg3']["tmp_name"], $filePath)) {
                            $this->getJson(array('status' => '0', 'message' => 'Sorry, there was an error uploading your file.'));
                        }
                        $saveColorArr = array('variation_id' => $variationId, 'image' => $file, 'is_featured' => '0', 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $postParam['user_id']);
                        Yii::$app->db->createCommand()->insert('product_images', $saveColorArr)->execute();
                    }
                } else if (isset($_REQUEST['color-' . $colorName . '-otherImg3']) && $_REQUEST['color-' . $colorName . '-otherImg3'] != '') {
                    $imagePath = $_REQUEST['color-' . $colorName . '-otherImg3'];
                    $imagePathArr = explode('/', $imagePath);
                    $oldFile = end($imagePathArr);
                    if (file_exists(Yii::$app->basePath . '/web/images/products/' . $oldFile)) {
                        unlink(Yii::$app->basePath . '/web/images/products/' . $oldFile);
                    }
                    Yii::$app->db->createCommand('delete from product_images where image = "' . $oldFile . '" ')->execute();
                }
            }
        }
        /** edit product variations images start */
        $this->getJson(array('status' => '1', 'message' => 'Product updated successfully.'));
    }

    /**
     * Category Size and Attributes
     * @category_id string category id
     * @return array category size and attributes
     * */
    function categoryAttributes($category_id) {
        $sizeArr = (new \yii\db\Query())
                ->select(['size'])
                ->from('product_size_variation')
                ->where(['category_id' => $category_id])
                ->orderBy(['order' => SORT_ASC])
                ->all();

        $attrArr = (new \yii\db\Query())
                ->select(['attribute_name'])
                ->from('product_size_attributes')
                ->where(['category_id' => $category_id])
                ->all();

        $data = array(
            'size' => array(),
            'attributes' => array()
        );
        if (!empty($sizeArr)) {
            foreach ($sizeArr as $size) {
                array_push($data['size'], $size['size']);
            }
        }
        if (!empty($attrArr)) {
            foreach ($attrArr as $attr) {
                array_push($data['attributes'], $attr['attribute_name']);
            }
        }
        $this->getJson(array('status' => '1', 'data' => $data, 'message' => 'Success.'));
    }

    /**
     * Country List
     * @return mixed values 
     * * */
    function countryList() {
        $countries = Country::find()
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $listData = ArrayHelper::map($countries, 'id', 'name');

        $this->getJson(array('status' => '1', 'data' => $listData, 'message' => 'Success.'));
    }

    /**
     * get order list
     * @return  array order list array
     * * */
    function orders($id) {
        $helper = new Helper();
        $config = $helper->getConfiguration();
        $statusArr = $helper->getOrderStatus();

        $orderArr = array();

        /** pagination start * */
        $pagination = isset(Yii::$app->params['pagination']) ? Yii::$app->params['pagination'] : 10;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '1';
        $start = ($page * $pagination) - $pagination;
        $limit = ' limit ' . $start . ', ' . $pagination;
        /** pagination end * */
        /** order filter start * */
        $where = ' WHERE op.vendor_id="' . $id . '"';

        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
            $where .= " and o.id='" . $_REQUEST['keyword'] . "'";
        }
        if (isset($_REQUEST['duration']) && $_REQUEST['duration'] != '') {
            $date = date('Y-m-d');
            $where .= ' and DATEDIFF("' . $date . '", DATE_FORMAT( o.order_date, "%Y-%m-%d" )) <= ' . $_REQUEST['duration'];
        }
        if (isset($_REQUEST['order_status']) && $_REQUEST['order_status'] != '') {

            $status = explode(',', $_REQUEST['order_status']);
            $str = '';
            foreach ($status as $key => $val) {
                $str .= ($str != '') ? ',' . "'" . $val . "'" : "'" . $val . "'";
            }
            if ($str != '') {
                $where .= ' and o.status in(' . $str . ')';
            }
        }
        /** order filter end * */
        /** order sorting start* */
        $orderBy = ' order by o.id desc';

        if (isset($_REQUEST['sort']) && $_REQUEST['sort']) {
            $sort = explode('-', $_REQUEST['sort']);
            $orderBy = ' order by ' . $sort[0] . ' ' . $sort[1];
        }
        /** order sorting end * */
        $query = 'SELECT o.id, o.status, o.order_date, sum(op.price) as price, o.order_date + INTERVAL ' . $config['vendor_shipping_deadline'] . ' HOUR as ship_by from orders o
            LEFT JOIN order_products op ON o.id=op.order_id
            ' . $where . ' group by op.order_id ' . $orderBy . $limit;

        $result = Yii::$app->db->createCommand($query)->queryAll();


        $count = Yii::$app->db->createCommand('SELECT op.order_id from orders o
            LEFT JOIN order_products op ON o.id=op.order_id
            ' . $where . ' group by op.order_id')->queryAll();


        $totalPage = (isset($count['0']) && $count['0'] != '') ? ceil(count($count) / $pagination) : 0;

        if (!empty($result)) {
            $i = 0;
            foreach ($result as $order) {
                $orderArr[$i] = $result[$i];
                $orderArr[$i]['order_date'] = date('d/m/Y', strtotime($result[$i]['order_date']));
                $orderArr[$i]['ship_by'] = date('d/m/Y', strtotime($result[$i]['ship_by']));
                $orderArr[$i]['status'] = $statusArr[$result[$i]['status']];
                $orderArr[$i]['price'] = 'S$' . $result[$i]['price'];
                $i++;
            }

            $this->getJson(array('status' => '1', 'data' => $orderArr, 'totalPage' => $totalPage, 'message' => 'Success.'));
        } else {
            $this->getJson(array('status' => '1', 'data' => $orderArr, 'message' => 'No order found.'));
        }
    }

    /**
     * get order status list
     * @return  array order status array
     * * */
    function orderStatus() {
        $helper = new Helper();
        $statusArr = $helper->getOrderStatus();

        if (!empty($statusArr)) {
            $this->getJson(array('status' => '1', 'data' => $statusArr, 'message' => 'Success.'));
        } else {
            $this->getJson(array('status' => '1', 'data' => $statusArr, 'message' => 'No status found.'));
        }
    }

    /**
     * get order detail
     * @return  array order detail array
     * * */
    function orderDetail($id, $user_id) {
        $helper = new Helper();
        $config = $helper->getConfiguration();
        $statusArr = $helper->getOrderStatus();
        $shipmentStatusArr = $helper->getOrderShipmentStatus();

        $query = 'SELECT o.id, o.status, o.order_date, CONCAT_WS( "S$", "", sum(op.price) ) price, o.order_date + INTERVAL ' . $config['vendor_shipping_deadline'] . ' HOUR as ship_by,o.shipping_method from orders o
            LEFT JOIN order_products op ON o.id=op.order_id
            Where op.order_id="' . $id . '" group by op.order_id ';
        $result = Yii::$app->db->createCommand($query)->queryOne();
        $date = date('Y-m-d H:i:s');
        $remainingTime = round((strtotime($result['ship_by']) - strtotime($date)) / 60);
        if ($remainingTime > 0) {
            $d = floor($remainingTime / 1440);
            $h = floor(($remainingTime - $d * 1440) / 60);
            $m = $remainingTime - ($d * 1440) - ($h * 60);
            $result['remaining_time'] = $h . 'h ' . $m . 'min remaining';
        } else {
            $result['remaining_time'] = '--';
        }
        $result['status'] = $statusArr[$result['status']];
        $result['order_date'] = date('d/m/Y', strtotime($result['order_date']));
        $result['ship_by'] = date('d/m/Y', strtotime($result['ship_by']));

        $products = Yii::$app->db->createCommand('SELECT p.name, p.sku, v.size, op.product_id, op.variation_id ,CONCAT_WS( "S$", "", op.price ) price, op.qty,op.shipment_status as status from order_products op
            LEFT JOIN products p ON p.id=op.product_id
            LEFT JOIN product_variation v ON v.id=op.variation_id
            Where op.order_id = "' . $id . '" and op.vendor_id = "' . $user_id . '"
            ')->queryAll();

        $j = 0;
        foreach ($products as $product) {
            $products[$j]['status'] = $shipmentStatusArr[$products[$j]['status']];

            $j++;
        }
        $result['products'] = $products;

        $shipmentArr = Yii::$app->db->createCommand('SELECT shipment_status, carrier, traking_number, shipped_date, shipment_note from order_products 
            Where order_id = "' . $id . '" and vendor_id = "' . $user_id . '" and carrier != "" and carrier is not null
            GROUP BY carrier, traking_number
            ')->queryAll();

        if (!empty($shipmentArr)) {
            $i = 0;
            foreach ($shipmentArr as $shipment) {
                $productArr = Yii::$app->db->createCommand('SELECT p.name, p.id as product_id from order_products op
            LEFT JOIN products p ON p.id=op.product_id
            Where op.order_id = "' . $id . '" and op.vendor_id = "' . $user_id . '" and op.carrier = "' . $shipment['carrier'] . '" and op.traking_number = "' . $shipment['traking_number'] . '"
            ')->queryAll();
                $shipmentArr[$i]['products'] = $productArr;
                $shipmentArr[$i]['shipment_status'] = $shipmentStatusArr[$shipmentArr[$i]['shipment_status']];
                $shipmentArr[$i]['shipped_date'] = date('d/m/Y', strtotime($shipmentArr[$i]['shipped_date']));
                $i++;
            }
            $result['shipments'] = $shipmentArr;
        } else {
            $result['shipments'] = array();
        }

        $payment = (new \yii\db\Query())
                ->select(['payment_date', 'payment_ref_number', 'notes'])
                ->from('vendor_payment')
                ->where(['order_id' => $id])
                ->andWhere(['vendor_id' => $user_id])
                ->andWhere('payment_ref_number !="" and payment_ref_number is not null')
                ->one();
        if (!empty($payment)) {
            $payment['payment_date'] = date('d/m/Y', strtotime($payment['payment_date']));
            $result['payment'] = $payment;
        } else {
            $result['payment'] = array();
        }
        $this->getJson(array('status' => '1', 'data' => $result, 'message' => 'Success.'));
    }

    /**
     * get shipment products
     * @return array 
     * * */
    function shipmentProducts($id, $user_id) {
        $productArr = (new \yii\db\Query())
                ->select(['order_products.product_id', 'products.name'])
                ->from('order_products')
                ->join('LEFT JOIN', 'products', 'products.id=order_products.product_id')
                ->where(['order_products.order_id' => $id])
                ->andWhere(['order_products.vendor_id' => $user_id])
                ->andWhere("`order_products`.`traking_number` = ''  OR `order_products`.`traking_number` IS NULL Or shipment_status='3'")
                ->all();
        $this->getJson(array('status' => '1', 'data' => $productArr, 'message' => 'Success.'));
    }

    /**
     * add product shipment
     *      * * */
    function addShipment($id, $user_id) {
        $saveArr['carrier'] = isset($_REQUEST['carrier']) ? $_REQUEST['carrier'] : '';
        $saveArr['traking_number'] = isset($_REQUEST['traking_number']) ? $_REQUEST['traking_number'] : '';
        $count = (new \yii\db\Query())
                ->from('order_products')
                ->where(['traking_number' => $saveArr['traking_number']])
                ->andWhere(['order_id' => $id])
                ->andWhere(['carrier' => $saveArr['carrier']])
                ->count();
        if ($count > 0) {
            $this->getJson(array('status' => '0', 'message' => 'Tracking number already exist.'));
        }
        $saveArr['shipped_date'] = (isset($_REQUEST['shipped_date']) && $_REQUEST['shipped_date'] != '') ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_REQUEST['shipped_date']))) : '';
        $saveArr['shipment_note'] = isset($_REQUEST['shipment_note']) ? $_REQUEST['shipment_note'] : '';
        $saveArr['shipment_to'] = 'Administrator';
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $user_id;
        $saveArr['shipment_status'] = '5';

        $vendor = (new \yii\db\Query())
                ->select(['vendor_code', 'shop_name'])
                ->from('vendors')
                ->where(['id' => $user_id])
                ->one();
        $saveArr['shipment_from'] = $vendor['vendor_code'] . '-' . $vendor['shop_name'];
        $productId = isset($_REQUEST['productId']) ? explode(',', $_REQUEST['productId']) : array();

        $ids = '';
        foreach ($productId as $key => $val) {
            $ids .= ($ids != '') ? ',' . $val : $val;
            $shipmentArr = (new \yii\db\Query())
                    ->select(['id', 'shipment_status', 'carrier', 'traking_number', 'shipped_date', 'delivered_date', 'shipment_from', 'shipment_to', 'shipment_note', 'shipment_history'])
                    ->from('order_products')
                    ->where(['order_id' => $id])
                    ->andWhere(['product_id' => $val])
                    ->one();
            if (isset($shipmentArr['shipment_status']) && $shipmentArr['shipment_status'] == '3') {
                $historyArr = array();
                if (isset($shipmentArr['shipment_history']) && $shipmentArr['shipment_history'] != '') {
                    $historyArr = unserialize($shipmentArr['shipment_history']);
                }
                unset($shipmentArr['shipment_history']);
                $historyArr[] = $shipmentArr;
                $shipmentHistory = serialize($historyArr);
                Yii::$app->db->createCommand()->update('order_products', ['shipment_history' => $shipmentHistory], 'product_id="' . $val . '" and order_id="' . $id . '"')->execute();
            }
        }
        Yii::$app->db->createCommand()->update('order_products', $saveArr, 'product_id in (' . $ids . ')  and order_id="' . $id . '"')->execute();
        $this->getJson(array('status' => '1', 'message' => 'Shipment saved successfully.'));
    }

    /**
     * add product shipment
     *      * * */
    function editShipment($id, $user_id) {
        $saveArr['carrier'] = isset($_REQUEST['carrier']) ? $_REQUEST['carrier'] : '';
        $saveArr['traking_number'] = isset($_REQUEST['traking_number']) ? $_REQUEST['traking_number'] : '';
        $productId = isset($_REQUEST['productId']) ? $_REQUEST['productId'] : '';
        $count = (new \yii\db\Query())
                ->from('order_products')
                ->where(['traking_number' => $saveArr['traking_number']])
                ->andWhere(['order_id' => $id])
                ->andWhere(['carrier' => $saveArr['carrier']])
                ->andWhere('product_id not in (' . $productId . ')')
                ->count();
        if ($count > 0) {
            $this->getJson(array('status' => '0', 'message' => 'Tracking number already exist.'));
        }
        $saveArr['shipped_date'] = (isset($_REQUEST['shipped_date']) && $_REQUEST['shipped_date'] != '') ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $_REQUEST['shipped_date']))) : '';
        $saveArr['shipment_note'] = isset($_REQUEST['shipment_note']) ? $_REQUEST['shipment_note'] : '';
        $saveArr['updated_date'] = date('Y-m-d H:i:s');
        $saveArr['updated_by'] = $user_id;

        Yii::$app->db->createCommand()->update('order_products', $saveArr, 'product_id in (' . $productId . ')  and order_id="' . $id . '"')->execute();

        $this->getJson(array('status' => '1', 'message' => 'Shipment updated successfully.'));
    }

    /**
     * cancel order or order products
     * * */
    function cancelOrder($id, $user_id) {
        $type = (isset($_REQUEST['type']) && $_REQUEST['type'] != '') ? $_REQUEST['type'] : '';
        $note = (isset($_REQUEST['cancel_note']) && $_REQUEST['cancel_note'] != '') ? $_REQUEST['cancel_note'] : '';
        if ($type == 'order-cancel') {
//            Yii::$app->db->createCommand()->update('orders', ['status' => '4'], 'id=' . $id)->execute();
            Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => '7', 'order_cancel_note' => $note], 'order_id=' . $id)->execute();

            $this->getJson(array('status' => '1', 'message' => 'Order cancelled successfully.'));
        } else if ($type == 'product-cancel') {
            $productId = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
            if ($productId != '') {
                Yii::$app->db->createCommand()->update('order_products', ['shipment_status' => '7', 'order_cancel_note' => $note], 'order_id=' . $id . ' and product_id in (' . $productId . ')')->execute();
                $this->getJson(array('status' => '1', 'message' => 'Order product cancelled successfully.'));
            } else {
                $this->getJson(array('status' => '10', 'message' => 'Bad Request.'));
            }
        }
    }

    /**
     * get reports
     * @return array 
     * * */
    function reports($type, $vendor_id) {
        $helper = new Helper();
        $config = $helper->getConfiguration();
        $statusArr = $helper->getOrderStatus();
        $pagination = isset(Yii::$app->params['pagination']) ? Yii::$app->params['pagination'] : 10;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '1';
        $start = ($page * $pagination) - $pagination;

        $reports = array();

        if ($type == 'sales') {
            $duration = isset($_REQUEST['duration']) ? $_REQUEST['duration'] : '7';
            $date = date('Y-m-d');
            if ($duration > 365) {
                $where = ' WHERE op.vendor_id= "' . $vendor_id . '" ';
            } else {
                $where = ' WHERE op.vendor_id= "' . $vendor_id . '" and DATEDIFF("' . $date . '", DATE_FORMAT( o.order_date, "%Y-%m-%d" )) <= ' . $duration;
            }

            $limit = ' limit ' . $start . ', ' . $pagination;
            $query = 'SELECT o.id, o.status, o.order_date, op.price, o.order_date + INTERVAL ' . $config['vendor_shipping_deadline'] . ' HOUR as ship_by from orders o
            LEFT JOIN order_products op ON o.id=op.order_id
            ' . $where . ' group by op.order_id ' . $limit;

            $result = Yii::$app->db->createCommand($query)->queryAll();
            $finalPrice = 0;
            if (!empty($result)) {
                foreach ($result as $report) {
                    $reports[] = array(
                        'order_id' => $report['id'],
                        'status' => $statusArr[$report['status']],
                        'order_date' => date('d/m/Y', strtotime($report['order_date'])),
                        'price' => 'S$' . $report['price'],
                        'ship_by' => date('d/m/Y', strtotime($report['ship_by'])),
                    );
                    $finalPrice = $finalPrice + $report['price'];
                }
            } else {
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'No order found.'));
            }
            $finalPrice = 'S$' . $finalPrice;
            $this->getJson(array('status' => '1', 'data' => $reports, 'total-amount' => $finalPrice, 'message' => 'Success.'));
        } else if ($type == 'best-selling') {
            $duration = isset($_REQUEST['duration']) ? $_REQUEST['duration'] : '7';
            if ($duration > 365) {
                $where = ' WHERE p.vendor_id= "' . $vendor_id . '" AND (

SELECT count( product_id )
FROM order_products
WHERE product_id = op.product_id
) >0';
            } else {
                $date = date('Y-m-d');
                $where = ' WHERE p.vendor_id= "' . $vendor_id . '" and DATEDIFF("' . $date . '", DATE_FORMAT( p.created_date, "%Y-%m-%d" )) <= ' . $duration . '  AND (

SELECT count( product_id )
FROM order_products
WHERE product_id = op.product_id
) >0';
            }

            $limit = ' limit 10';
            $orderBy = ' order by product_sell desc';

            $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.status,c.id as category,pa.display_price,pa.display_currency,COUNT( op.product_id ) as product_sell,COUNT(op.order_id) as total_order
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
    ON p.id=pa.product_id' .
                    $where . ' Group by p.id  ' . $orderBy . $limit;

            $products = Yii::$app->db->createCommand($query)->queryAll();
            if (!empty($products)) {
                $category = $this->getProductCategory();
                $i = 0;
                foreach ($products as $product) {
                    $products[$i]['category'] = $category[$product['category']];

                    if ($product['featured_image'] != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $product['featured_image'])) {
                        $products[$i]['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true);
                    } else {
                        $products[$i]['featured_image'] = Url::to('@web/images/no_image.png', true);
                    }

                    if ($products[$i]['display_price'] != '') {
                        $products[$i]['display_price'] = $products[$i]['display_currency'] . $products[$i]['display_price'];
                    } else {
                        $products[$i]['display_price'] = '';
                    }
                    unset($products[$i]['display_currency']);
                    unset($products[$i]['product_sell']);
                    unset($products[$i]['status']);
                    $i++;
                }
                $reports = $products;
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'Success.'));
            } else {
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'No product found.'));
            }
        } else if ($type == 'low-stock') {
            $where = ' WHERE p.vendor_id= "' . $vendor_id . '" AND (
                        SELECT count( id )
                        FROM product_variation
                        WHERE product_id = p.id
                        AND qty <10
                        ) > 0';
            $limit = ' limit ' . $start . ', ' . $pagination;
            $query = 'SELECT p.id,p.name,p.product_code,p.featured_image,p.sku,p.status,c.id as category,pa.display_price,pa.display_currency,(

SELECT min( qty )
FROM product_variation
WHERE product_id = p.id
) AS qty
FROM products p
left JOIN category c ON p.category_id = c.id 
'
                    . ' LEFT JOIN 
(
    select MIN(product_variation.display_price) display_price,product_id,display_currency
    from product_variation
    group by product_id
) pa 
    ON p.id=pa.product_id' .
                    $where . ' Group by p.id  order by qty asc' . $limit;

            $products = Yii::$app->db->createCommand($query)->queryAll();
            if (!empty($products)) {
                $category = $this->getProductCategory();
                $i = 0;
                foreach ($products as $product) {
                    $products[$i]['category'] = $category[$product['category']];

                    if ($product['featured_image'] != '' && file_exists(Yii::$app->basePath . '/web/images/products/' . $product['featured_image'])) {
                        $products[$i]['featured_image'] = Url::to('@web/images/products/' . $product['featured_image'], true);
                    } else {
                        $products[$i]['featured_image'] = Url::to('@web/images/no_image.png', true);
                    }
                    if ($products[$i]['display_price'] != '') {
                        $products[$i]['display_price'] = $products[$i]['display_currency'] . $products[$i]['display_price'];
                    } else {
                        $products[$i]['display_price'] = '';
                    }

                    if ($products[$i]['sku'] != '') {
                        $products[$i]['sku'] = (string) $products[$i]['sku'];
                    }
                    unset($products[$i]['display_currency']);
                    unset($products[$i]['product_sell']);
                    unset($products[$i]['status']);
                    $i++;
                }
                $reports = $products;
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'Success.'));
            } else {
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'No product found.'));
            }
        } else if ($type == 'payment-due') {
            $where = ' WHERE op.vendor_id= "' . $vendor_id . '"  and o.status = "1"';

            $limit = ' limit ' . $start . ', ' . $pagination;
            $query = 'SELECT o.id, o.status, o.order_date, sum(op.vendor_payment) as price, o.order_date + INTERVAL ' . $config['vendor_shipping_deadline'] . ' HOUR as ship_by from orders o
            LEFT JOIN order_products op ON o.id=op.order_id
            ' . $where . ' group by op.order_id ' . $limit;

            $result = Yii::$app->db->createCommand($query)->queryAll();
            $finalPrice = 0;
            if (!empty($result)) {
                foreach ($result as $report) {
                    $reports[] = array(
                        'order_id' => $report['id'],
                        'status' => $statusArr[$report['status']],
                        'order_date' => date('d/m/Y', strtotime($report['order_date'])),
                        'price' => 'S$' . $report['price'],
                        'ship_by' => date('d/m/Y', strtotime($report['ship_by'])),
                    );
                    $finalPrice = $finalPrice + $report['price'];
                }
            } else {
                $this->getJson(array('status' => '1', 'data' => $reports, 'message' => 'No order found.'));
            }
            $finalPrice = 'S$' . $finalPrice;
            $this->getJson(array('status' => '1', 'data' => $reports, 'total-amount' => $finalPrice, 'message' => 'Success.'));
        }
    }

    function notifications($id) {

        $pagination = isset(Yii::$app->params['pagination']) ? Yii::$app->params['pagination'] : 10;
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '1';
        $start = ($page * $pagination) - $pagination;
//        $limit = ' limit ' . $start . ', ' . $pagination;

        $data = (new \yii\db\Query())
                ->select(['id', 'notification','DATE_FORMAT( created_date, "%d/%m/%Y %h:%i %p" ) as created_date'])
                ->from('notifications')
                ->where(['user_id' => $id])
                ->andWhere(['status' => '1'])
                ->orderBy(['id' => SORT_DESC])
                ->offset($start)
                ->limit($pagination)
                ->all();
        if (!empty($data)) {
            $this->getJson(array('status' => '1', 'data' => $data, 'message' => 'Success.'));
        } else {
            $this->getJson(array('status' => '1', 'data' => $data, 'message' => 'No notification found.'));
        }
    }

    function deleteNotification($id) {
        Yii::$app->db->createCommand()->update('notifications', ['status' => '0'], ['id' => $id])->execute();
        $this->getJson(array('status' => '1', 'message' => 'Notification removed successfully.'));
    }

}
