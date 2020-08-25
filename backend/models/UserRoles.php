<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_roles".
 *
 * @property integer $id
 * @property string $name
 * @property string $created_date
 * @property string $updated_date
 * @property string $status
 */
class UserRoles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_date', 'updated_date'], 'safe'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
            'status' => 'Status',
        ];
    }
}
