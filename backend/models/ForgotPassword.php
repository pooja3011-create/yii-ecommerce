<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
use common\models\User;
use common\models\Quotation;
use common\models\Configuration;
use yii\base\ErrorException;

/**
 * Password reset form
 */
class ForgotPassword extends Model {

    public $password;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct() {
        
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function requestResetPassword($email) {

        $user = User::findOne([
                    'status' => User::STATUS_ACTIVE,
                    'email' => $email,
        ]);

        if (!$user) {
            Yii::$app->session->setFlash('error', "Sorry, we could not find this email address in our records; please try again or contact our support.");
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }

        if (!$user->save()) {
            return false;
        }

//        $config = Configuration::find()->where(['config_key' => 'email'])->one();
//        if (isset($config->config_val) && $config->config_val != '') {
//            $adminEmail = $config->config_val;
//        } else {
        $adminEmail = \Yii::$app->params['adminEmail'];
//        }

        $params['email'] = $user->email;
        $params['first_name'] = $user->first_name;
        $params['last_name'] = $user->last_name;
        $params['password_reset_token'] = $user->password_reset_token;

        try {
            \Yii::$app->mailer->compose('/site/forgotPasswordFrom', ['params' => $params])
                    ->setFrom([\Yii::$app->params['adminEmail'] => 'Boucle'])
                    ->setTo($user->email)
                    ->setSubject('Reset password')
                    ->send();
        } catch (ErrorException $e) {
//Yii::$app->session->setFlash('error', "Your account is not activated.");

            Yii::$app->session->setFlash('error', 'Error in sending mail.');
            return false;
        }

        Yii::$app->session->setFlash('success', 'Your request was successful. Please check your email for password reset instructions.');
    }

}
