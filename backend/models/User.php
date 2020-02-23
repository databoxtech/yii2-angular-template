<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $password
 * @property string $displayName
 * @property string $email
 * @property string $phone
 * @property int $deleted
 * @property int $blocked
 * @property string $created_at
 * @property string $updated_at
 */
class User extends \yii\db\ActiveRecord implements  IdentityInterface
{

    public $role;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    public static function findByUsername($username)
    {
        return User::findOne(['email' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['deleted', 'blocked'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['password', 'displayName', 'email'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 15],
            [['email'], 'unique'],
            [['email'], 'required'],

            //generate password hash, if password field is dirty (updated)
            ['password', 'filter', 'skipOnEmpty' => true, 'filter' => function ($plain_password) {
                return Yii::$app->getSecurity()->generatePasswordHash($plain_password);
            }, 'when' => function ($model, $attribute) {
                return isset($model->dirtyAttributes['password']);
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'password' => 'Password',
            'displayName' => 'Display Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'deleted' => 'Deleted',
            'blocked' => 'Blocked',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function fields() {
        return [
            'id',
            'displayName',
            'email',
            'phone',
            'deleted',
            'blocked',
            'role'
        ];
    }

    /*
     * @return array
     */
    public function getPublic(){
        return [
            'id' => $this->id,
            'displayName' => $this->displayName,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return User::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $id = $token->getClaim('uid');
        return User::findOne(['id' => $id]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }


    public function createJwt($user)
    {

        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;


        $user_permissions = Yii::$app->authManager->getPermissionsByUser($user->id);
        $permissions = [];
        foreach ($user_permissions as $key => $user_permission) {
            array_push($permissions, $key);
        }


        $token = $jwt->getBuilder()
            ->setIssuer('https://github.com/databoxtech/yii2-angular-template')// Configures the issuer (iss claim)
            ->setAudience('yii2-angular-template')// Configures the audience (aud claim)
            ->setId('6O5457V2RW', true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + 5184000)// Configures the expiration time (60 days) of the token (exp claim)
            ->set('uid',  $user->id)// Configures a new claim, called "id"
            ->set('displayName',  $user->displayName)
            ->set('permission',  $permissions)
            ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken();

        return (string)$token;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function setPassword($plain_password){
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($plain_password);
    }
}
