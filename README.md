# Pernod Ricard Innovation Pipeline

### What is this repository for? ###

* Innovation Galllery web site based on Symfony.
* GBD Team at PR HQ

## Installation


2. Build/run containers with (with and without detached mode)

    ```bash
    $ docker-compose build
    $ docker-compose up -d
    ```

## Work in local :

```bash
# Build
docker-compose -f docker-compose.dev.yml build
```
```bash
# Build without cache
docker-compose -f docker-compose.dev.yml build --no-cache
```
```bash
# UP -d
docker-compose -f docker-compose.dev.yml up -d
```
```bash
# Use every command in local
docker-compose -f docker-compose.dev.yml exec php7 <your command>
Example :
docker-compose -f docker-compose.dev.yml exec php7 bin/console doctrine:schema:update --dump-sql
docker-compose -f docker-compose.dev.yml exec php7 bin/console doctrine:generate:entities AppBundle/Entity
docker-compose -f docker-compose.dev.yml exec php7 bin/console assetic:dump --env=prod --no-debug
docker-compose -f docker-compose.dev.yml exec php7 bash
docker-compose -f docker-compose.dev.yml exec php7 composer update
docker-compose -f docker-compose.dev.yml exec php7 bin/console pri:import-all-datas --only-redis
docker-compose -f docker-compose.dev.yml exec php7 bin/console pri:import-all-datas --only-created_at
docker-compose -f docker-compose.dev.yml exec php7 bin/console cache:clear --env=prod
docker-compose -f docker-compose.dev.yml exec php7 bin/console pri:generate_ppt quali_full --innovation_id=27 
docker-compose -f docker-compose.dev.yml exec redis redis-cli
docker-compose -f docker-compose.dev.yml exec php7 bin/console generate:controller
docker-compose -f docker-compose.dev.yml exec php7 bin/console doctrine:generate:entity
docker-compose -f docker-compose.dev.yml exec php7 bin/console assets:install --symlink --relative

Recreate database
docker-compose -f docker-compose.dev.yml exec php7 bin/console doctrine:database:drop --force
docker-compose -f docker-compose.dev.yml exec php7 bin/console doctrine:database:create
```
docker-compose exec php7 bin/console pri:import-all-datas --only-redis

## How it works?


```bash
$ docker-compose ps
     Name                   Command               State              Ports            
--------------------------------------------------------------------------------------
docker_db_1      docker-entrypoint.sh mysqld      Up      0.0.0.0:3306->3306/tcp      
docker_php7_1    docker-php-entrypoint /bui ...   Up      0.0.0.0:80->80/tcp, 9000/tcp
docker_redis_1   docker-entrypoint.sh redis ...   Up      0.0.0.0:6379->6379/tcp      
```

    
## Useful commands
```bash
# bash commands
$ docker-compose exec php7 bash
 
# Composer (e.g. composer update)
$ docker-compose exec php7 composer update

# Same command by using alias
$ docker-compose exec php7 bash
$ sf cache:clear
 
# Retrieve an IP Address (here for the nginx container)
$ docker inspect --format '{{ .NetworkSettings.Networks.dockersymfony_default.IPAddress }}' $(docker ps -f name=nginx -q)
$ docker inspect $(docker ps -f name=nginx -q) | grep IPAddress
 
# MySQL commands
$ docker exec -it innovation-pipeline_db_1 mysql -uroot -p"secret"
 
# Commandes Redis
$ docker-compose exec redis redis-cli
$ List tasks
> keys *
You will need to perform for each key a "type" command:

> type <key>
and depending on the response perform:

for "string": get <key>
for "hash": hgetall <key>
for "list": lrange <key> 0 -1
for "set": smembers <key>
for "zset": zrange <key> 0 -1 withscores
 
# Check CPU consumption
$ docker stats $(docker inspect -f "{{ .Name }}" $(docker ps -q))
 
# Delete all containers
$ docker rm $(docker ps -aq)
 
# Delete all images
$ docker rmi $(docker images -q)
 
# If you have changed the passwords since you first tried  running the containers? 
# docker-compose does extra work to preserve volumes between runs (thus preserving the database);
$ docker-compose rm -v
 
``` 

## Symfony command
```bash
# Install vendor with composer
$ docker-compose exec php7 composer install
 
# Update schema
$ docker-compose exec php7 php app/console doctrine:schema:update --force
 
# Clear cache
$ docker-compose exec php7 php /var/www/symfony/app/console cache:clear # Symfony2
$ docker-compose exec php7 php /var/www/symfony/bin/console cache:clear # Symfony3
 
# Manage right
$ docker-compose exec php7 chown -R www-data:www-data app/cache && rm -rf app/cache/*
$ docker-compose exec php7 chown -R www-data:www-data app/logs
 
# Fixing cache/logs folder
$ sudo chmod -R 777 app/cache app/logs # Symfony2
$ sudo chmod -R 777 var/cache var/logs var/sessions # Symfony3
 
# Add symfony package
$ docker-compose exec -T --user www-data php composer require friendsofsymfony/rest-bundle
# And don't forget to reference it in app/AppKernel.php file
 
# Display All routes
$ docker-compose exec php7  php bin/console debug:router
 
```

## FOS User BUNDLE :
```bash 
#Create First user :  
docker-compose exec php7  php bin/console fos:user:create admin dev@corellis.eu 1pipo2 --super-admin
```
#### More informations : 
https://symfony.com/doc/2.0/bundles/FOSUserBundle/command_line_tools.html


## Import all datas :
```bash 
# Reset your database :  
docker-compose exec php7 bin/console doctrine:database:drop --force
docker-compose exec php7 bin/console doctrine:database:create
docker-compose exec php7 bin/console doctrine:schema:update --force

# Update only redis datas:
docker-compose exec php7 bin/console pri:import-all-datas --only-redis

# Launch import : (estimed time: 15min)
docker-compose exec php7 bin/console pri:import-all-datas

# Import with pictures : (estimed time: A LOT)
docker-compose exec php7 bin/console pri:import-all-datas --with-pictures
# Notes:
- Pictures are mandatory to correctly import all datas. 
- There is a (very) large amount of pictures, around 5gb (july, 2018). If you want import all picture, ask them first to a friend, or be patient and drink coffee.
- you will be prompted to enter the pantheon's password to rsync pictures (if you don't know it, ask it to Florian)
```


## Makefile
Regroupe les commandes utiles au projet
ex : make start


## Package symfony
docker-compose exec -T --user www-data php composer require friendsofsymfony/rest-bundle jms/serializer-bundle predis/predis snc/redis-bundle doctrine/doctrine-fixtures-bundle


## Excel export
```bash 
# Exporting data to excel :  
docker-compose exec php7 bin/console pri:generate_excel (ARGUMENT)
ARGUMENT LIST : 
    active_user 
    newsletter_user
    matrix 
    matrix_without_duplicate
    innovations
```

docker-compose -f docker-compose.dev.yml exec php7 bin/console pri:generate_excel innovations

## Ppt export
```bash 
# Exporting data to ppt :  
docker-compose exec php7 bin/console pri:generate_ppt (ARGUMENT)
ARGUMENT LIST : 
    overview_quali 
    quali
    quali_full
    contributor 
```

## Send promote emails :
```
docker-compose exec php7 bin/console pri:send-promote-innovation-emails
# send to developer email:
docker-compose exec php7 bin/console pri:send-promote-innovation-emails --debug-mode

```

## Worker Helpers :

Documentation : https://github.com/mmucklo/DtcQueueBundle

```bash
# Run queue
docker-compose exec php7 bin/console dtc:queue:run
```

```bash
# some status about the queue if available (ODM/ORM only)
docker-compose exec php7 bin/console dtc:queue:count
```
```bash
# resets errored and/or stalled jobs
docker-compose exec php7 bin/console dtc:queue:reset
```


## Minify css and js :
```bash
docker-compose -f docker-compose.dev.yml exec php7 bin/console assetic:dump --env=prod --no-debug
```

##
https://github.com/TrafeX/docker-php-nginx/blob/master/Dockerfile [++]
https://hub.docker.com/r/andreisusanu/nginx-php7/~/dockerfile/ (bof)
https://hub.docker.com/r/byrnedo/nginx-php7-fpm/~/dockerfile/  [++]
https://github.com/eko/docker-symfony/blob/master/php-fpm/Dockerfile