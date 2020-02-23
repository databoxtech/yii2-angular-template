<p align="center">
    <a href="http://databoxtech.com" target="_blank">
        <img src="https://avatars2.githubusercontent.com/u/60692131?s=200&v=4" height="100px" />
    </a>
    <h1 align="center">Yii2 Angular Template</h1>
    <br>
</p>

[![Build Status](https://travis-ci.org/databoxtech/yii2-angular-template.svg?branch=master)](https://travis-ci.org/databoxtech/yii2-angular-template)


Yii2 Angular Template is a skelaton project with a Yii2 Rest API and a Angular (v9) client.
The template contains the basic features including,
------------------------------
    JWT authentication
    User Management (via angular app)
    Role based access control (Yii2 RBAC)

You can keep adding more functionality to following same pattern. Refer documentations to find out more.

DIRECTORY STRUCTURE
-------------------

      app/               Angular v9 frontend application
      backend/           Yii2 backend application

SETUP INSTRUCTIONS
------------------
    1. Clone the repo (git clone https://github.com/databoxtech/yii2-angular-template)
    2. Install yii2 dependecies using composer (cd backend && composer install)
    3. Configure database by editing config/db.php
    4. Initialize database (./yii migrate)
    5. Initialize yii2 rbac by running `./yii migrate --migrationPath=@yii/rbac/migrations`
    6. Initialize basic permissions/role and admin account by running `./yii user/permissions`
    7. Run backend api by running `./yii serve`
    8. Install angular dependencies using npm (cd app && npm install)
    9. Run frontend application by running `ng serve`
    10. Open http://localhost:4200 and login using below credentials,
        Username (email): admin@template.com
        Passowrd: test@123
    