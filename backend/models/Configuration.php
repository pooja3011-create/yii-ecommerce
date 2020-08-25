<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuration".
 *
 * @property integer $id
 * @property string $config_key
 * @property string $config_val
 */
class Configuration extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['config_key', 'config_val'], 'required'],
            [['config_val'], 'string'],
            [['config_key'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'config_key' => 'Config Key',
            'config_val' => 'Config Val',
        ];
    }

    public function saveConfig($postData = array()) {
        if ($postData && count($postData) > 0) {
            $configArr = Yii::$app->db->createCommand('TRUNCATE TABLE configuration')->execute();
            foreach ($postData as $k => $val) {
                Yii::$app->db->createCommand()->insert('configuration', ['config_key' => $k, 'config_val' => $val])->execute();
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['saveConfig']);
        }
    }

    function getModels() {
        $modules = (new \yii\db\Query())
                ->from('modules')
                ->where(['status' => '1'])
                ->all();

        $moduleArr = array();
        foreach ($modules as $module) {
            $moduleArr[$module['module']][$module['action']] = $module['id'];
        }
        return $moduleArr;
    }

    function saveUserRole() {
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $id = $_GET['id'];
            $saveArr['name'] = $_POST['UserRolesSearch']['name'];
            $saveArr['status'] = $_POST['UserRolesSearch']['status'];
            $saveArr['updated_date'] = date('Y-m-d H:i:s');
            $permission = isset($_POST['permission']) ? $_POST['permission'] : '';
            $count = (new \yii\db\Query())
                    ->from('user_roles')
                    ->where(['name' => $saveArr['name']])
                    ->andWhere(['!=', 'id', $id])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateRole']);
                return false;
            }

            $user_id = isset(Yii::$app->user->id) ? Yii::$app->user->id : '0';
            if (!empty($permission)) {
                Yii::$app->db->createCommand('delete from user_permissions where role_id="' . $id . '"')->execute();
                foreach ($permission as $key => $val) {
                    Yii::$app->db->createCommand()->insert('user_permissions', ['module_id' => $key, 'role_id' => $id, 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $user_id])->execute();
                }
            }
            Yii::$app->db->createCommand()->update('user_roles', $saveArr, ['id' => $id])->execute();
            Yii::$app->session->setFlash('success',  Yii::$app->params['editRole']);
            return $id;
        } else {

            $saveArr['name'] = $_POST['UserRolesSearch']['name'];
            $saveArr['status'] = $_POST['UserRolesSearch']['status'];
            $saveArr['created_date'] = date('Y-m-d H:i:s');
            $saveArr['updated_date'] = date('Y-m-d H:i:s');
            $permission = isset($_POST['permission']) ? $_POST['permission'] : '';
            $count = (new \yii\db\Query())
                    ->from('user_roles')
                    ->where(['name' => $saveArr['name']])
                    ->count();
            if ($count > 0) {
                Yii::$app->session->setFlash('error', Yii::$app->params['duplicateRole']);
                return false;
            }

            Yii::$app->db->createCommand()->insert('user_roles', $saveArr)->execute();
            $id = Yii::$app->db->getLastInsertID();
            $user_id = isset(Yii::$app->user->id) ? Yii::$app->user->id : '0';
            if (!empty($permission)) {
                foreach ($permission as $key => $val) {
                    Yii::$app->db->createCommand()->insert('user_permissions', ['module_id' => $key, 'role_id' => $id, 'created_date' => date('Y-m-d H:i:s'), 'updated_date' => date('Y-m-d H:i:s'), 'updated_by' => $user_id])->execute();
                }
            }
            Yii::$app->session->setFlash('success', Yii::$app->params['saveRole']);
            return $id;
        }
    }

    function getUserRole($id) {
        $data = (new \yii\db\Query())
                ->select(['id', 'name', 'status'])
                ->from('user_roles')
                ->where(['id' => $id])
                ->one();
        $data['permission'] = array();
        $permission = (new \yii\db\Query())
                ->select(['module_id'])
                ->from('user_permissions')
                ->where(['role_id' => $id])
                ->all();
        if (!empty($permission)) {
            foreach ($permission as $module) {
                array_push($data['permission'], $module['module_id']);
            }
        }
        return $data;
    }

    function userRole() {
        $userRole = (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('user_roles')
                ->where(['!=', 'id', '1'])
                ->andWhere(['!=', 'id', '2'])
                ->all();
        $userRoleArr = array();
        if (!empty($userRole)) {
            foreach ($userRole as $role) {
                $userRoleArr[$role['id']] = $role['name'];
            }
        }
        return $userRoleArr;
    }

    function saveAdminUser($postParam) {
        if (!empty($postParam)) {
            $saveArr['email'] = isset($postParam['email']) ? $postParam['email'] : '';
            if ($saveArr['email'] != '') {
                $count = (new \yii\db\Query())
                        ->from('user')
                        ->where(['email' => $saveArr['email']])
                        ->count();
                if ($count > 0) {
                    Yii::$app->session->setFlash('error', Yii::$app->params['duplicateEmail']);
                    return false;
                }
                $userCode = (new \yii\db\Query())
                        ->select(['user_code'])
                        ->from('user')
                        ->where(['!=', 'user_role', '1'])
                        ->andWhere(['!=', 'user_role', '2'])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();
                if (!empty($userCode) && isset($userCode['user_code'])) {
                    $code = trim(str_replace('A', '', $userCode['user_code']));
                    $saveArr['user_code'] = 'A' . ($code + 1);
                } else {
                    $saveArr['user_code'] = 'A1';
                }
                $saveArr['first_name'] = isset($postParam['first_name']) ? $postParam['first_name'] : '';
                $saveArr['user_role'] = isset($postParam['user_role']) ? $postParam['user_role'] : '';
                $password = isset($postParam['password']) ? $postParam['password'] : '';
                $saveArr['status'] = isset($postParam['status']) ? $postParam['status'] : '';
                $saveArr['password_hash'] = Yii::$app->security->generatePasswordHash($password);
                $saveArr['created_at'] = date('Y-m-d H:i:s');
                $saveArr['updated_at'] = date('Y-m-d H:i:s');

                Yii::$app->db->createCommand()->insert('user', $saveArr)->execute();
                $userId = Yii::$app->db->getLastInsertID();

                Yii::$app->session->setFlash('success', Yii::$app->params['saveAdminUser']);
                return true;
            } else {
                Yii::$app->session->setFlash('error', 'Please enter valid email.');
                return false;
            }
        } else {
            return false;
        }
    }

    function UpdateAdminUser($id, $postParam) {
        if (!empty($postParam)) {
            $saveArr['email'] = isset($postParam['email']) ? $postParam['email'] : '';
            if ($saveArr['email'] != '') {
                $count = (new \yii\db\Query())
                        ->from('user')
                        ->where(['email' => $saveArr['email']])
                        ->andWhere(['!=', 'id', $id])
                        ->count();
                if ($count > 0) {
                    Yii::$app->session->setFlash('error', 'Email already exists.');
                    return false;
                }
                $saveArr['first_name'] = isset($postParam['first_name']) ? $postParam['first_name'] : '';
                $saveArr['user_role'] = isset($postParam['user_role']) ? $postParam['user_role'] : '';
                $password = isset($postParam['password']) ? $postParam['password'] : '';
                $Confirmpassword = isset($postParam['confirmPassword']) ? $postParam['confirmPassword'] : '';
                $saveArr['status'] = isset($postParam['status']) ? $postParam['status'] : '';
                $saveArr['updated_at'] = date('Y-m-d H:i:s');

                if ($Confirmpassword != '' && $password != '') {
                    $saveArr['password_hash'] = Yii::$app->security->generatePasswordHash($password);
                }
                Yii::$app->db->createCommand()->update('user', $saveArr, ['id' => $id])->execute();
                Yii::$app->session->setFlash('success', Yii::$app->params['editAdminUser']);
                return true;
            } else {
                Yii::$app->session->setFlash('error', 'Please enter valid email.');
                return false;
            }
        } else {
            return false;
        }
    }

    function getUser($id) {
        $data = (new \yii\db\Query())
                ->from('user')
                ->where(['id' => $id])
                ->one();
        return $data;
    }

    function deleteRole($id) {
        $count = (new \yii\db\Query())
                ->from('user')
                ->where(['user_role' => $id])
                ->count();
        if ($count > 0) {
            Yii::$app->session->setFlash('error', Yii::$app->params['roleDeleteError']);
            return false;
        }
        Yii::$app->db->createCommand('delete from user_roles where id="' . $id . '"')->execute();
        Yii::$app->session->setFlash('success', Yii::$app->params['delRole']);
        return true;
    }

    function deleteUser($id) {
        Yii::$app->db->createCommand('delete from user where id="' . $id . '"')->execute();
        Yii::$app->session->setFlash('success', Yii::$app->params['delAdminUser']);
    }

}
