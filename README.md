<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About this CV project

This website is just a digital and simple project to show my CV to the world.
It was build with:

- [Laravel 10](https://laravel.com/docs/10.x/)
- [PHP 8.1](https://www.php.net/)
- [PHPUnit 10 tests](https://phpunit.de/)
- [PSR-2 guide lines](https://www.php-fig.org/psr/psr-2/)
- [Apache2](https://httpd.apache.org/)
- [MYSQL 8.2](https://www.mysql.com/)
- [PHPMyAdmin 5.2.1](https://www.phpmyadmin.net/)
- [SSL ( Namecheap certificate )](https://www.namecheap.com/)
- [RabbitMQ 3.12.9](https://www.rabbitmq.com/)
- [APCu](https://www.php.net/manual/en/book.apcu.php)
- [HTML + CSS + JS ( Jquery ) - ( Credits to www.themezy.com ) ](https://www.themezy.com/free-website-templates/151-ceevee-free-responsive-website-template)
- [Docker ( with docker-compose - See 'Infra configs' next )](https://docs.docker.com/compose/)


## Infra configs

docker-composer:

```
version: "3.9"
services:
  php-apache:
    restart: always
    ports:
      - "8090:80"
    build: './build/php'
    volumes:
      - ./site:/var/www/html  
  mysql:
    ports:
      - "3406:3306"
    restart: always    
    build: './build/mysql'
    environment:
      MYSQL_ROOT_PASSWORD: ""
      MYSQL_DATABASE: "jgomes"
    volumes:
      - dbData:/var/lib/mysql
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    restart: always
    ports:
      - "8091:80"
    environment:
      PMA_HOST: mysql
      MYSQL_ROOT_PASSWORD: "" 
      MYSQL_USER: "root" 
volumes:
  app:
  dbData:
```

./build/php

```
FROM php:8.1-apache

RUN apt-get update && \
    docker-php-ext-install mysqli pdo pdo_mysql
```

./build/mysql

```
FROM mysql:latest
USER root
RUN chmod 755 /var/lib/mysql
```

structure ( project inside the dir "site" and ths ssl cert file are inside the dir "cert" )

![Logo do GitHub](https://jgomes.site/images/project_structure.png)

vhost to proxy the site to the world (with ssl)

```
LoadModule headers_module modules/mod_headers.so

<IfModule mod_ssl.c>
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule proxy_http_module modules/mod_proxy_http.so

        <VirtualHost *:443>

                ServerAdmin zx.gomes@gmail.com
                ServerName jgomes.site
                ErrorLog /var/log/apache2/jgomes_error.log
                CustomLog ${APACHE_LOG_DIR}/jgomes_access.log combined
                SSLEngine on
                SSLCertificateFile /home/jgomes/my/jgomes/cert/jgomes_site.crt
                SSLCertificateKeyFile /home/jgomes/my/jgomes/cert/jgomes_site.key
                SSLCertificateChainFile /home/jgomes/my/jgomes/cert/jgomes_site.ca-bundle

                ProxyRequests Off

                ProxyPass / http://localhost:8090/public/
                ProxyPassReverse / http://localhost:8090/public/

                Header set Access-Control-Allow-Origin "*"
                Header set Access-Control-Allow-Headers "*"
                Header set Access-Control-Allow-Methods "GET, POST, PUT, DELETE"
                Header set Access-Control-Expose-Headers "*"
                Header set Access-Control-Max-Age "900"

             <Location />
                Order allow,deny
                Allow from all
                AllowOverride all
            </Location>
        
        </VirtualHost>
</IfModule>
```

vhost just to redirect request that came to port 80 to 443

```
 <VirtualHost *:80>
    ServerName jgomes.site
    ServerAlias www.jgomes.site
    Redirect permanent / https://jgomes.site/
 </VirtualHost>

```
