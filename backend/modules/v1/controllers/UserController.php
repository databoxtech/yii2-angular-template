<?php

namespace app\modules\v1\controllers;

use app\models\Customer;
use app\models\Inquiry;
use app\models\CustomerSearch;
use app\models\Organization;
use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends RestController
{

    public $modelClass = 'app\models\User';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function prepareDataProvider()
    {
        $searchModel = new UserSearch();
        return $searchModel->search(\Yii::$app->request->queryParams);
    }

    public function actionAvailableRoles(){
        $roles = Yii::$app->authManager->getRoles();
        return array_values($roles);
    }

    public function actionAssignRole(){
        $roleName = Yii::$app->request->getBodyParam('role', null);
        $userid = Yii::$app->request->getBodyParam('user', null);
        if($roleName == null || $userid == null){
            throw new ServerErrorHttpException("Some data not found in request");
        }

        if($role = Yii::$app->authManager->getRole($roleName) == null){
            throw new NotFoundHttpException("Specified role, \"{$roleName}\" not found.");
        }

        Yii::$app->authManager->assign($role, $userid);

        $response = Yii::$app->getResponse();
        $response->setStatusCode(200);
    }

    /** @noinspection DuplicatedCode */
    public function actionCreate(){
        $model = new User();

        //validate role
        $role = null;
        if(($roleName = Yii::$app->request->getBodyParam('role', false)) !== false &&
            ($role = Yii::$app->authManager->getRole($roleName) === null)){
                throw new NotFoundHttpException("Specified role, \"{$roleName}\" not found.");
        }

        $model->load(Yii::$app->request->getBodyParams(), '');
        if ($model->save()) {
            //role is set
            if($role = Yii::$app->authManager->getRole($roleName)){
                Yii::$app->authManager->assign($role, $model->id);
            }
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['user/view', 'id' => $model->id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionUpdate($id){
        $model = User::findOne(['id' => $id]);
        //validate role
        $role = null;
        if(($roleName = Yii::$app->request->getBodyParam('role', false)) !== false &&
            ($role = Yii::$app->authManager->getRole($roleName) === null)){
            throw new NotFoundHttpException("Specified role, \"{$roleName}\" not found.");
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        if($role = Yii::$app->authManager->getRole($roleName)){
            Yii::$app->authManager->revokeAll($model->id);
            Yii::$app->authManager->assign($role, $model->id);
        }

        return $model;
    }

    public function actionMe(){
        return  User::findOne(['id' => Yii::$app->user->id]);
    }

}
