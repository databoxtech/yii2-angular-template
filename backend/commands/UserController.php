<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UserController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionCreate($username, $password)
    {
        $user = new User();
        $user->email = $username;
        $user->setPassword($password);
        $user->save(false);

        echo "User created, $username => $password => {$user->id}\n";

    }


    public function actionPermissions(){
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        $permissionsArray = [
            'user:index' => 'View users',
            'user:view' => 'View user',
            'user:create' => 'Add users',
            'user:update' => 'Edit users',
            'user:delete' => 'Delete users',
            'user:available-roles' => 'Get available roles',
            'user:assign-role' => 'Assign role to a specified user',
        ];

        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $permissions = [];
        foreach ($permissionsArray as $perm => $desc){
            $permissions[$perm] = $auth->createPermission($perm);
            $permissions[$perm]->description = $desc;
            $auth->add($permissions[$perm]);
            $auth->addChild($admin, $permissions[$perm]);
        }

        $user =$auth->createRole('user');
        $auth->add($user);
        $auth->addChild($user, $permissions['user:index']);
        $auth->addChild($user, $permissions['user:view']);


        $user = new User();
        $user->displayName = 'Test Admin';
        $user->email = 'admin@template.com';
        $user->setPassword('test@123');
        $user->save(false);

        $auth->assign($admin, $user->id);
    }
}
