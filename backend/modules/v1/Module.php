<?php

namespace app\modules\v1;
use app\models\User;
use Yii;
use yii\base\Event;
use yii\db\ActiveRecord;

/**
 * v1 module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\v1\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        \Yii::$app->user->enableSession = false;
        \Yii::$app->response->format    = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->charset   = 'UTF-8';
    }
}
