<?php

namespace app\modules\v1\controllers;

use Yii;
// use yii\filters\auth\HttpBearerAuth;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\filters\AccessControl;
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

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        return Yii::$app->user->can(Yii::$app->controller->id.':'.$action->id);
                    },
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\ForbiddenHttpException('You are not allowed to access this page: '. json_encode($action));
            }
        ];

        return $behaviors;
    }

    public function getParam($name){
        return isset($this->params[$name]) ? $this->params[$name] : false;
    }

    public function init()
    {
        $this->request = json_decode(file_get_contents('php://input'), true);
        $params = Yii::$app->request->bodyParams;

        $this->params = array_merge($this->request ? $this->request : [], $params);

    }


    public function beforeAction($action)
    {
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Origin', '*');

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            parent::beforeAction($action);
            Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Credentials', 'true');
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }

}
