<?php

namespace app\modules\v1\controllers;

use Yii;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\rest\ActiveController;

class RestController extends ActiveController
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

//        $behaviors['rateLimiter']['enableRateLimitHeaders'] = true;

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
                'Access-Control-Expose-Headers' => [
                    'X-Total-Count',
                    'X-Paging-PageSize',
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Per-Page',
                    'X-Pagination-Total-Count',
                ]
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
