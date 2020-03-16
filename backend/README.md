Directory Structure
-------------------
      commands/                 Console commands for permission generation and user creation
      migrations/               Database migrations
      models/                   Shared model classes
      modules/v1/               API version 1 module
      modules/v1/controllers/   Controllers for api v1
      
Special Classes
---------------
Below classes are base classes for certain classes,

    app\modules\v1\controllers\ApiController    Used as base class for non ActiveControllers. Extending from this will automatically configure authentication & authorization, CORS and optionally rate limiting
    app\modules\v1\controllers\RestController   Used as ActiveController base class. Extending from this will automatically configure authentication & authorization, CORS and optionally rate limiting

API Endpoints
------------    
   * POST auth/token             
   Retrieve JWT token & Refresh token by either providing username & password or refresh token. For email/password set grant_type=password, for resfresh token set grant_type=refresh_token
   * GET users?search={query}&per-page=10&page=2&sort=-id    
   Get list of users. search,per-page,page,sort params are optional. If search param is provided will search against displayName, email & phone attributes. Result can be paged by providing `per-page` and `page` attributes. Paging information will be available in the response header. Sort param will sort the result per specified attribute (ASC id => id, DESC id => -id)
   * GET users/{id}
   Get user specified by id
   * POST users
   Create new user based on the provided attributes
   * PUT users/{id}
   Update user
   * DELETE users/{id}
   Delete specified user
    
Authentications
--------------
Authentication handled via JWT tokens. JWT generation code can be found in models/User
    
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
        
Authorizations
--------------
Yii2 Database RBAC is used. However in controllers instead of restricting access based on roles, permissions are used to manage access. Permissions has a special format,
    
    controller:action
        Ex: user:create, user:view
Base class rest controller is configured to check access based on this permission definition format,

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
        
        
Available Console Commands
--------------------------
Apart from basic yii console commands, below two commands are available
* user/create {email} {password} : create new user account by providing email/ password
* user/permissions  : Generate permissions and roles. Modify this command to include required permissions. 