<?php
/**
 * Created by prabath.
 * Date: 4/25/18
 * Time: 15:44
 */

namespace app\common\controllers;

use Yii;
use yii\filters\Cors;

class CorsCustom extends  Cors

{
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->getResponse()->getHeaders()->set('Allow', 'POST GET PUT');
            Yii::$app->end();
        }

        return true;
    }
}