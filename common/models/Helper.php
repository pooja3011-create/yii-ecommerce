<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use common\models\User;

/**
 * Helper functions
 */
class Helper extends Model {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
        ];
    }

    /**
     * function to encrypt string
     * 
     * @return string
     *      */
    function encryptIt($q) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $q, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
        return( $qEncoded );
    }

    /**
     * function to decrypt string
     * 
     * @return string
     *      */
    function decryptIt($q) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($q), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
        return( $qDecoded );
    }

    /**
     * function generate random code
     * 
     * @return string 
     *      */
    function generateRandom($table, $field, $len) {
        $string = '';
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $len; $i++) {
            $string .= $characters[mt_rand(0, $max)];
        }
        $count = (new \yii\db\Query())
                ->from($table)
                ->where([$field => $string])
                ->count();
        if ($count > 0) {
            $this->generateRandom($table, $field, $len);
        } else {
            return $string;
        }
    }

    function generateVendorCode() {
        $str = '';

        $vendorNum = (new \yii\db\Query())
                ->from('vendors')
                ->select(['vendor_code'])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        if (isset($vendorNum['vendor_code']) && $vendorNum['vendor_code'] != '') {
            $vendorNum = $vendorNum['vendor_code'];
            $last = substr($vendorNum, -1);
            if ($last < 9) {
                $str = substr($vendorNum, 0, 3) . ($last + 1);
            } else {
                $last = 1;
                $char = substr($vendorNum, 2, 1);
                if ($char != 'Z') {
                    $str = substr($vendorNum, 0, 2) . ++$char . $last;
                } else {
                    $last = substr($vendorNum, 1, 1);
                    if ($last < 9) {
                        $str = substr($vendorNum, 0, 1) . ($last + 1) . substr($vendorNum, 2, 2);
                    } else {
                        $char = substr($vendorNum, 0, 1);
                        $str = ++$char . substr($vendorNum, 1, 3);
                    }
                }
            }
        } else {
            $str = 'A1A1';
        }

        return $str;
    }

    /**
     * upload file
     * @param string $field name of database image field
     * @param string $table name of table name
     * @param array $imageFiles file array
     * @param int $id record id
     */
    public function upload($basePath, $field, $table, $imageFiles, $id) {

        if (!is_dir($basePath . $table)) {
            mkdir($basePath . $table);
        }

        foreach ($imageFiles as $file) {
            $fileName = $id . '_' . date('mdY_His') . '.' . $file->extension;
            $file->saveAs($basePath . $table . '/' . $fileName);

            Yii::$app->db->createCommand()->update($table, [$field => $fileName], 'id=' . $id)->execute();
        }
        return true;
    }

    function fetchCategoryTreeList($catArr = array(), $parent = 0, $user_tree_array = '') {

        if (!is_array($user_tree_array))
            $user_tree_array = array();

        $category = (new \yii\db\Query())
                ->from('category')
                ->select(['id', 'name', 'level'])
                ->where(['parent_id' => $parent])
                ->orderBy('id')
                ->all();

        if (count($category) > 0) {
            $user_tree_array[] = "<ul>";


            foreach ($category as $cat) {
                $checked = '';
                if (count($catArr) > 0 && in_array($cat['id'], $catArr)) {
                    $checked = 'checked="checked"';
                }
                if ($cat['level'] == '3') {
                    $user_tree_array[] = '<li><input type="checkbox" name="category[]" value="' . $cat['id'] . '" ' . $checked . '/>' . $cat['name'] . "</li>";
                } else {
                    if ($cat['level'] == '1') { 
                        $user_tree_array[] = "<li style='font-size: medium; font-weight: 600;'>" . $cat['name'] . "</li>";
                    }else{
                        $user_tree_array[] = "<li>" . $cat['name'] . "</li>";
                    }
                    
                }
                $user_tree_array = $this->fetchCategoryTreeList($catArr, $cat['id'], $user_tree_array);
            }

            $user_tree_array[] = "</ul>";
        }
        return $user_tree_array;
    }

    /** pagination html */
    function paginationHtml($page, $url) {
        $html = Html::dropDownList('pagesize', $page, array(10 => "10 Records", 20 => "20 Records", 50 => "50 Records", 100 => "100 Records"), array('id' => 'pagesize', 'onchange' => 'getPageRecord("' . $url . '",$(this));'));
        $html .= '<label>&nbsp;Per Page</label>';
        return $html;
    }

    /* user role permissions 
     *      */

    function getRolePermission($id = '') {
        if ($id != '') {
            $roleId = $id;
        } else {
            if (!Yii::$app->session->isActive) {
                Yii::$app->session->open();
            }
            $userArr = Yii::$app->session->get('userArr');
            if (empty($userArr)) {
                $userArr = User::find()->where(['id' => Yii::$app->user->id])->One();
            }
            $roleId = $userArr->user_role;
        }
        $data = Yii::$app->db->createCommand('SELECT m.module,m.action FROM modules m
            LEFT JOIN user_permissions r ON r.module_id=m.id    
            Where r.role_id="' . $roleId . '"')->queryAll();


        $moduleArr = array();
        foreach ($data as $module) {
            if (isset($moduleArr[$module['module']]) && !empty($moduleArr[$module['module']])) {
                array_push($moduleArr[$module['module']], $module['action']);
            } else {
                $moduleArr[$module['module']] = array($module['action']);
            }
        }
        return $moduleArr;
    }

    /** get all configuration */
    public function getConfiguration() {
        $configArr = array();
        $subArr = array();
        $flag = 0;
        $data = (new \yii\db\Query())
                ->from('configuration')
                ->orderBy('config_key')
                ->all();
        foreach ($data as $config) {
            if (array_key_exists($config['config_key'], $configArr)) {
                if ($flag == 0) {
                    $flag = 1;
                    array_push($subArr, $configArr[$config['config_key']]);
                }
                array_push($subArr, $config['config_val']);
                $configArr[$config['config_key']] = $subArr;
            } else {
                $subArr = array();
                $flag = 0;
                $configArr[$config['config_key']] = $config['config_val'];
            }
        };
        return $configArr;
    }

    /**
     * return order status 
     *     */
    function getOrderStatus() {
        /* $orderStatus = array(
          '0' => 'Processing',
          '1' => 'Vendor Shipped',
          '10' => 'Warehouse Received',
          '2' => 'Warehouse Accepted',
          '3' => 'Warehouse Rejected',
          '4' => 'Admin Shipped',
          '5' => 'Delivered',
          '6' => 'Admin Cancelled',
          '7' => 'Vendor Cancelled',
          '8' => 'Returned',
          '9' => 'Payment Failed',
          ); */
        $orderStatus = array(
            '0' => 'Processing',
            '1' => 'Delivered',
            '2' => 'Admin Shipped',
            '4' => 'Admin Cancelled',
        );
        return $orderStatus;
    }

    /**
     * return order shipment status 
     *     */
    function getOrderShipmentStatus() {
        $orderShipmentStatus = array(
            '0' => 'Pending',
            '1' => 'Delivered',
            '5' => 'Vendor Shipped',
            '9' => 'Warehouse Received',
            '2' => 'Warehouse Accepted',
            '3' => 'Warehouse Rejected',
            '4' => 'Admin Shipped',
            '6' => 'Admin Cancelled',
            '7' => 'Vendor Cancelled',
            '8' => 'Returned',
        );
        return $orderShipmentStatus;
    }

    /**
     * return order item status 
     *     */
    function getOrderItemStatus() {
        $orderShipmentStatus = array(
            '0' => array(
                '6' => 'Admin Cancelled',
                '7' => 'Vendor Cancelled'
            ),
            '5' => array(
                '9' => 'Warehouse Received',
                '2' => 'Warehouse Accepted',
                '3' => 'Warehouse Rejected',
            ),
            '9' => array(
                '2' => 'Warehouse Accepted',
                '3' => 'Warehouse Rejected',
            ),
            '2' => array(
                '4' => 'Admin Shipped'
            ),
            '3' => array(
                '5' => 'Vendor Shipped',
                '6' => 'Admin Cancelled',
                '7' => 'Vendor Cancelled'
            ),
            '4' => array(
                '1' => 'Delivered'
            )
        );
        return $orderShipmentStatus;
    }

    //function to send push notification
    public function sendPushNotification($registrationIds = array(), $message = array(), $deviceType) {

        
        $message = array
                (
                'message' => 'Congratulations.',
                'title' => 'Your account has been registered successfully.',
                'vibrate' => 1,
                'sound' => 1,
            );

        
        if ($deviceType == 'android') {
            $url = 'https://android.googleapis.com/gcm/send';
            $fields = array(
                'registration_ids' => $registrationIds,
                'data' => $message,
            );

// Update your Google Cloud Messaging API Key
            if (!defined('GOOGLE_API_KEY')) {
                define("GOOGLE_API_KEY", "AIzaSyAORBucLJxuSCa2DP0lxLl0Tw2OwF-AwyI");
            }
            $headers = array(
                'Authorization: key=' . GOOGLE_API_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($result);

            return TRUE;
        } else if (strtolower($deviceType) == 'iphone') {

            
            $deviceToken = $registrationIds[0];
            $passphrase = '';


            $fileName = 'Laundry_Distribution.pem';
//            $gateway = 'ssl://gateway.push.apple.com:2195';
            $gateway = 'ssl://gateway.sandbox.push.apple.com:2195';
//            $fileName = 'LaundryPush.pem';

            $ctx = stream_context_create();
            // ck.pem is your certificate file
            stream_context_set_option($ctx, 'ssl', 'local_cert', Yii::$app->basePath . '/web/' . $fileName);
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);


            // Open a connection to the APNS server
            $fp = stream_socket_client($gateway, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

            $msg = $message['message'] . ($message['line1'] != '' ? ' ' . $message['line1'] : '') . ($message['line2'] != '' ? ' ' . $message['line2'] : '') . ($message['line3'] != '' ? ' ' . $message['line3'] : '');

            // Create the payload body
            $body['aps'] = array(
                'alert' => array(
                    'title' => $message['title'],
                    'body' => $msg,
                ),
                'badge' => (int) $message['notification_count'],
                'sound' => 'default'
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

//       echo $msg;
//        print_r($result);exit;
            // Close the connection to the server
            fclose($fp);

            if (!$result)
                return 'Message not delivered' . PHP_EOL;
            else
                return 'Message successfully delivered' . PHP_EOL;
        }
    }
}
