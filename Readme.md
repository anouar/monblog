# mon BLOG

#### Environnement docker: (centos 7 / php 8 / apache / mariadb / node(webEncore)) (voir docker-compose.yml)

```shell 
docker-compose up -d
```

#### Installation:

```shell 
composer install
```
##### Base de données:
```shell 
php bin/console doctrine:database:create 
php bin/console doctrine:migrations:migrate
```
##### fixtures:
```shell 
php bin/console doctrine:fixtures:load
```
##### jwt Token:
```shell 
php bin/console lexik:jwt:generate-keypair   
```
##### TEST unitaire
```shell 
php ./vendor/bin/phpunit tests/
```

```shell 
Le site est accessible en local sous l'url suivante:
http://monblog.dev:8000            
http://monblog.dev:8000/api (api platform)    
http://localhost:8080/   (adminer BD)
http://localhost:1080 (mailcatcher)                                                                    
```

