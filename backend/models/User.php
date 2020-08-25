<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $role
 * @property string $photo
 * @property string $last_login
 * @property string $device_id
 * @property string $device_type
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
        ];
    }

    /**
     * get customer detail
     * @id customer id
     * @type user type 
     * @return customer detail
     * * */
    function getUserData($id, $type) {

        if ($type == 'registered') {
            $data = (new \yii\db\Query())
                    ->select(['user_code', 'first_name', 'email', 'phone', 'billing_address1', 'billing_city', 'billing_country', 'gender', 'birthdate', 'status', 'created_at'])
                    ->from('user')
                    ->where(['id' => $id])
                    ->one();
        } else {
            $data = (new \yii\db\Query())
                    ->select(['user_code', 'first_name', 'email', 'phone', 'billing_address1', 'billing_city', 'billing_country', 'gender', 'birthdate', 'status', 'created_at'])
                    ->from('guest_user')
                    ->where(['id' => $id])
                    ->one();
        }
        return $data;
    }

    /**
     * get customer detail
     * @id customer id
     * @type user type 
     * */
    function saveCustomer($id, $type) {
        $saveArr['first_name'] = isset($_POST['User']['first_name']) ? $_POST['User']['first_name'] : '';
        $saveArr['email'] = isset($_POST['User']['email']) ? $_POST['User']['email'] : '';
        $saveArr['phone'] = isset($_POST['User']['phone']) ? $_POST['User']['phone'] : '';
        $saveArr['gender'] = isset($_POST['User']['gender']) ? $_POST['User']['gender'] : '';
        $saveArr['status'] = isset($_POST['User']['status']) ? $_POST['User']['status'] : '';
        $saveArr['billing_address1'] = isset($_POST['User']['billing_address1']) ? $_POST['User']['billing_address1'] : '';
        $saveArr['billing_city'] = isset($_POST['User']['billing_city']) ? $_POST['User']['billing_city'] : '';
        $saveArr['billing_country'] = isset($_POST['User']['billing_country']) ? $_POST['User']['billing_country'] : '';
        $saveArr['birthdate'] = isset($_POST['User']['birthdate']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['User']['birthdate']))) : '';
        $saveArr['updated_at'] = date('Y-m-d H:i:s');
        $password = isset($_POST['User']['password']) ? $_POST['User']['password'] : '';
        $confirm_password = isset($_POST['User']['confirm_password']) ? $_POST['User']['confirm_password'] : '';

        if ($type == 'registered') {
            if ($password != '' && $confirm_password != '') {
                $saveArr['password_hash'] = Yii::$app->security->generatePasswordHash($password);
            }
            Yii::$app->db->createCommand()->update('user', $saveArr, ['id' => $id])->execute();
        } else {
            Yii::$app->db->createCommand()->update('guest_user', $saveArr, ['id' => $id])->execute();
        }
        Yii::$app->session->setFlash('success', Yii::$app->params['editUser']);
        return true;
    }

    public function getUserName() {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getPhone() {
        return $this->phone;
    }

    function getCartTotal($id) {
        $query = 'SELECT SUM(v.display_price)  as total
FROM `shopping_cart` s
left JOIN `product_variation` v ON v.id = s.variation_id
WHERE s.user_id = "' . $id . '"';
        $data = Yii::$app->db->createCommand($query)->queryOne();
        return $data;
    }

}
