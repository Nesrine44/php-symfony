# PrAuthBundle

### Pernod Ricard authentication Single Sign On Bundle for Symfony 3.x or higher

## Current version : 1.0.0

## DESCRIPTION

This bundle provide an external authentication from Pernod Ricard Auth0 Service, and can be used with MyPortal.
PrAuthBundle depends of Symfony FosUserBundle to provide user management.
The FosUserBundle login form is altered to authenticate the user both in FosUserBundle and in Pernod Ricard's system.
When it's filled, all people with "@pernod-ricard" and external users in their username have to authenticate through Auth0.

#### Important note:
When this bundle is enabled, users must use email instead of username to connect to the platform!
You can change this logic, if you want.


## INSTALLATION

#### 1. Install FosUserBundle (if it's not already the case)

To install it, just follow Symfony instructions : https://symfony.com/doc/master/bundles/FOSUserBundle/index.html
Note: You will have to create a User class extending BaseUser (FOS\UserBundle\Model\User)


#### 2. Copy PrAuthBundle in you project

Copy PrAuthBundle directory in src/


#### 2.5 Install Auth0 library

Run this command. You can read full documentation here: https://auth0.com/docs/quickstart/webapp/php
```bash
composer require auth0/auth0-php:"~5.0"
```
Don't forget to configure your fallbackUrl in your auth0 application: https://auth0.com/docs/quickstart/webapp/php#configure-callback-urls
with this : https://your-domain.com/auth0/login


#### 3. Enable PrAuthBundle

Update app/AppKernel.php file to add PrAuthBundle() in registered bundles:

```php 
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        ...
        new PrAuthBundle\PrAuthBundle(),
    ];
}
```


#### 4. Update routing

Update app/config/routing.yml file to add PrAuthBundle() in routes:

```yml
# app/config/routing.yml
# add this at the begining of app/config/routing.yml
pr_auth:
    resource: "@PrAuthBundle/Controller/"
    type:     annotation
    prefix:   /
```


#### 5. Update autoload

Update composer.json file to add PrAuthBundle in autoload:

```json
// composer.json
"autoload": {
    "psr-4": {
        ...
        PrAuthBundle: PrAuthBundle // Add this line
    },
    "classmap": [
        "app/AppKernel.php",
        "app/AppCache.php"
    ]
},
```

Then launch autoload update using this bash command: 

```bash
php bin/console composer dump-autoload 
```


#### 6. Update your User class (created in step 1)

Update extends of your User class to PrAuthUser.

```php 
use PrAuthBundle\Model\PrAuthUser;

/**
 * @ORM\Entity
 * ...
 */
class User extends PrAuthUser{...}
```

Then update your database model using this bash command: 

```bash
php bin/console doctrine:schema:update --force
```

Notes: PrAuthUser add new fields and methods to your User class:
* is_pr_employe (type=boolean) : setted as true if user logged in with Pernod Ricard Employees button 
* firstname (type=string) : generated with user email
* lastname (type=string) 
* getProperUsername() : firstname and lastname concatenated


#### 7. Update FosUser security to disable some access controls and add /ssl_login

Update app/config/security.yml to something like this : 

```yml
# app/config/security.yml
security:
    encoders:
        FOS\UserBundle\Model\UserInterface: bcrypt

    role_hierarchy:
        ... # you own roles

    providers:
        fos_userbundle:
            id: fos_user.user_provider.username_email # we use email to connect to the platform

    firewalls:
        main:
            ... # your own logic

    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        #- { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }  # we dont allow registration
        #- { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY } # we dont allow resetting password
        - { path: ^/auth0/connect, role: IS_AUTHENTICATED_ANONYMOUSLY } # For PrAuthBundle
        - { path: ^/auth0/login, role: IS_AUTHENTICATED_ANONYMOUSLY } # For PrAuthBundle
        - { path: ^/admin/, role: ROLE_ADMIN }
        - { path: ^/, role: ROLE_USER }
```


#### 8. Add PrAuth parameters

Update app/config/parameters.yml file to add PrAuth parameters:

```yml
# app/config/parameters.yml
parameters:
    ...
    pr_auth:
        domain: my.domain # to change with your Auth0 application domain (domain must be like my-app.auth0.com)
        client_id: AZERTYUIOPQSDFGHJKLMWXCVBN # to change with your Auth0 application client_id
        client_secret: AZERTYUIOPQSDFGHJKLMWXCVBN-AZERTYUIOPQSDFGHJKLMWXCVBN # to change with your Auth0 application client_secret
        audience: 'https://my.domain/userinfo' # to change with your Auth0 application domain ( like https://my-app.auth0.com/userinfo )
        scope: 'openid profile email'
        password_key: AZERTYUIOPQSDFGHJKLMWXCVBN # to change by anything you want (why not client_id) : this is used as salt to generate local passwords
        my_portal_authentication_enabled: true # or false if you want to hide Pernod Ricard Employees button
        contact_email: contact@contact.com # to change with your own website contact email
        website_title: My Awesome Pernod Ricard Website # to change with your own website title
        default_roles: [ROLE_USER] # you can change it with your own roles
        removable_roles: [] # roles to remove on update
```

Then add pr_auth to Twig globals:

```yml
# app/config/parameters.yml
twig:
    globals:
        ... # your own twig globals 
        pr_auth: '%pr_auth%' # add this line
```


#### 9. Override FosUserBundle Templates 

Last step is to override FosUserBundle Templates to use PrAuthBundle login template.

* Create a app/config/Resources/FOSUserBundle/ directory
* Create a app/config/Resources/FOSUserBundle/views/ directory
* Create a app/config/Resources/FOSUserBundle/views/Security/ directory
* Create an empty file app/config/Resources/FOSUserBundle/views/Security/login.html.twig

And add this content to app/config/Resources/FOSUserBundle/views/Security/login.html.twig
```php
{# app/config/Resources/FOSUserBundle/views/Security/login.html.twig #}
{% extends "@PrAuth/Security/login.html.twig" %}
```

Then add PrAuthBundle assets to your web/bundles/ directory using this bash command:
```bash
php bin/console assets:install --symlink --relative
```

#### 10. (Optional) Update your current users to use MyPortal authentication
 
If you already have users in your database, you can update them to allow them to use MyPortal authentication using this bash command: 
```bash
php bin/console pr-auth:update-users
``` 

## ROLE ASSIGNMENT

By default (as you see in Installation step 8), default role is ROLE_USER. 
If you want to use your own role, you can change it in your parameters. 
* default_roles: array of roles to add to users using MyPortal Authentication.
* removable_roles: array of roles to remove to users using MyPortal Authentication.

The you can update current user roles to your new role using this bash command:  
```bash
php bin/console pr-auth:update-users
```
