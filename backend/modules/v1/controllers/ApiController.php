<?php

namespace app\modules\v1\controllers;

use Yii;
// use yii\filters\auth\HttpBearerAuth;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;

class ApiController extends Controller
{

    public $request;
    public $params;

    public $enableCsrfValidation = false;

    public $headers;


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'except' => ['options'],
        ];

        $behaviors['authenticator']['except'] = ['options'];


        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => []
            ]

        ];

//        $behaviors['corsFilter'] = [
//            'class' => CorsCustom::className(),
//        ];


        return $behaviors;
    }

    public function actionOptions(){
        return '';
    }

    public function  createResponse($code, $data = []){

        return [
            'status' => $code,
            'data'   => $data,
        ];
    }

    public function getParam($name){
        return isset($this->params[$name]) ? $this->params[$name] : false;
    }

    public function init()
    {
        $this->request = json_decode(file_get_contents('php://input'), true);
        $params = Yii::$app->request->bodyParams;

        $this->params = array_merge($this->request ? $this->request : [], $params);
//        if($this->request && !is_array($this->request)){
//            Yii::$app->api->sendFailedResponse(['Invalid Json']);
//        }
    }

    public function permission_required($permission){
        if(!Yii::$app->user->can($permission)){
            throw new \Exception('Access Denied');
        }
    }

    public function beforeAction($action)
    {
        //your code
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            parent::beforeAction($action);
            Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Credentials', 'true');
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }

}
