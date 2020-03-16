<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "refresh_tokens".
 *
 * @property int $id
 * @property int $user
 * @property string $token
 * @property string $created_at
 * @property string $expires_at
 *
 * @property User $user0
 */
class RefreshToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'refresh_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user'], 'integer'],
            [['created_at', 'expires_at'], 'safe'],
            [['token'], 'string', 'max' => 192],
            [['user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'token' => 'Token',
            'created_at' => 'Created At',
            'expires_at' => 'Expires At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(User::className(), ['id' => 'user']);
    }

    public function isExpired(){
        return strtotime($this->expires_at) < time();
    }
}
