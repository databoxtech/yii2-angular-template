<?php

namespace app\modules\v1\controllers;

use app\models\RefreshToken;
use app\models\User;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

class AuthController extends ApiController
{

    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['token'];
        unset($behaviors['access']);

        return $behaviors;
    }

    public function actionToken()
    {
        $grant_type = Yii::$app->request->getBodyParam('grant_type', 'password');
        $user = null;

        if($grant_type == 'password') {
            if (!$username = $this->getParam("email")) {
                throw new HttpException(500, 'Email cannot be empty');
            }
            if (!$password = $this->getParam("password")) {
                throw new HttpException(500, 'password cannot be empty');
            }

            if (!$user = User::findByUsername($username)) {
                throw new HttpException(404, 'User not found');
            }

            if ($user->validatePassword($password)) {
                if ($user->deleted || $user->blocked) {
                    throw new HttpException(401, "Unauthorized user.");
                }

            } else {
                throw new HttpException(403, "Invalid Credentials {$username} {$password}");
            }
        }else if($grant_type == 'refresh_token'){
            if (!$refresh_token = $this->getParam("refresh_token")) {
                throw new HttpException(500, 'refresh_token cannot be empty');
            }
            if(!$rt = RefreshToken::findOne(['token' => $refresh_token])){
                throw new HttpException(500, 'Invalid refresh_token');
            }

            if($rt->isExpired()){
                $rt->delete();
                throw new HttpException(500, 'Refresh token expired.');
            }
            $user = $rt->user0;

            $rt->delete();
        }

        $user_permissions = Yii::$app->authManager->getPermissionsByUser($user->id);
        $permissions = [];
        foreach ($user_permissions as $key => $user_permission) {
            array_push($permissions, $key);
        }

        $refresh_token = $this->createRefreshToken($user);
        $jwt = $this->createJwt($user, $permissions);

        return [
            'refresh_token' => $refresh_token,
            'jwt' => $jwt,
            'expires_in' => Yii::$app->params['jwt.expire.duration'],
            'user' => $user,
            'permissions' => $permissions
        ];
    }


    protected function createJwt($user, $permissions)
    {
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;

        $token = $jwt->getBuilder()
            ->setIssuer('https://github.com/databoxtech/yii2-angular-template')// Configures the issuer (iss claim)
            ->setAudience('yii2-angular-template')// Configures the audience (aud claim)
            ->setId('6O5457V2RW', true)// Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setExpiration(time() + Yii::$app->params['jwt.expire.duration'])// Configures the expiration time (60 days) of the token (exp claim)
            ->set('uid',  $user->id)// Configures a new claim, called "id"
            ->set('displayName',  $user->displayName)
            ->set('permission',  $permissions)
            ->sign($signer, $jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken();

        return (string)$token;
    }

    protected function createRefreshToken($user){
        $rt = new RefreshToken();
        $rt->user = $user->id;
        $rt->token = $this->random_str(192);
        $rt->expires_at = date('Y-m-d H:i:s', (time() + Yii::$app->params['refresh_token.expire.duration']));
        if(!$rt->save()){
            throw new ServerErrorHttpException('Internal error occurred');
        }
        return $rt->token;
    }

    /**
     * Ref: https://stackoverflow.com/a/31107425/2177996
     *
     * @param int $length Length of the generated key
     * @param string $keyspace Character space to be used
     * @return string
     * @throws Exception
     */
    protected function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=$'): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

}
