<?php

namespace app\modules\v1\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\HttpException;

class AuthController extends ApiController
{

    public function behaviors()
    {

        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['login', 'index'];

        return $behaviors + [
               'access' => [
                   'class' => AccessControl::className(),
                   'only' => ['logout', 'me'],
                   'rules' => [
                       [
                           'actions' => ['logout'],
                           'allow' => true,
                           'roles' => ['@'],
                       ],
                       [
                           'actions' => ['login'],
                           'allow' => true,
                           'roles' => ['?'],
                       ],
                   ],
               ],
           ];
    }

    public function actionLogin()
    {
        if(!$username = $this->getParam("email")){
            throw new HttpException(500, 'Email cannot be empty');
        }
        if(!$password = $this->getParam("password")){
            throw new HttpException(500, 'password cannot be empty');
        }
        
        if(!$user = User::findByUsername($username)){
            throw new HttpException(404, 'User not found');
        }

        if($user->validatePassword($password)){
            if($user->deleted || $user->blocked) {
                throw new HttpException(401, "Unauthorized user.");
            }
            $obj = $user->getPublic();
            $obj['jwt'] = $user->createJwt($user);
            $perms = Yii::$app->authManager->getPermissionsByUser($user->id);
            $obj['permissions'] = [];
            foreach ($perms as $perm){
                $obj['permissions'][] = $perm->name;
            }
            return $obj;
        } else {
            throw new HttpException(403, "Invalid Credentials {$username} {$password}");
        }
    }

    public function actionLogout()
    {
//        $headers = Yii::$app->getRequest()->getHeaders();
//        $access_token = $headers->get('x-access-token');
//
//        if(!$access_token){
//            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
//        }
//
//        $model = AccessToken::findOne(['token' => $access_token]);
//
//        if ($model->delete()) {
//            Yii::$app->api->sendSuccessResponse(["Logged Out Successfully"]);
//        } else {
//            Yii::$app->api->sendFailedResponse("Invalid Request");
//        }


    }

}
