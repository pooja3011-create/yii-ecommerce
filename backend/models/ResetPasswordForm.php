<?php

namespace backend\models;

use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\User;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model {

    public $password;
    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token = '') {
        $this->_user = User::findByPasswordResetToken($token);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                // ['password', 'required'],
                //  ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
//    public function resetPassword($password)
//    {
//        $user = $this->_user;
//        
//        $user->setPassword($password);
//        $user->removePasswordResetToken();
//
//        return $user->save(false);
//    }

    public function resetPassword() {
        $user = $this->_user;
        $this->password = $_POST['newPassword'];
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save(false);
    }

}
