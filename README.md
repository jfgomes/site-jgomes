<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About this CV project

This website is just a digital and simple project to show my CV and my WOW to the world.
The storage of this project was home made, so this is a private server in my personal network, with multiple containers that work as services where is only mandatory to have the web server accessible outside of my internal network. The rest of the services are internal.

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

## TR/DR setup
- git clone git@github.com:jfgomes/site-jgomes.git
- composer update
- cp .env.example .env ( Need to add the configs to .env )
- php artisan key:generate
- php artisan serve --port=90 ( The port is not mandatory. By default is 80 )

## Infra configs

docker-composer:

```
version: "3.9"
services:
  php-apache:
    restart: always
    ports:
      - "8888:81"
    build: './build/php'
    volumes:
      - ./site:/var/www/html
    depends_on:
      - mysql
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
      MYSQL_USER: ""

volumes:
  app:
  dbData:
```

./build/php

```
FROM php:8.1-apache

# Copy entrypoint to container and make it our door to enter on
COPY entrypoint.sh /sbin/entrypoint.sh
RUN chmod +x /sbin/entrypoint.sh

# Update package lists and install dependencies
RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        unzip \
        git \
        libonig-dev

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Apache to use the 'public' directory as DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create the working directory and copy the Dockerfile and necessary files
WORKDIR /var/www/html

# Activate rewrie
RUN a2enmod rewrite

# Copy vhost to container nad activate it
COPY app.conf /etc/apache2/sites-available/
RUN a2dissite 000-default
RUN a2ensite app.conf

# Expose port 81
EXPOSE 81

CMD ["/sbin/entrypoint.sh"]
```
app.conf
```
Listen 81
<VirtualHost *:81>
  ServerAdmin admin@localhost
  ServerName localhost
  DocumentRoot /var/www/html/public
  ErrorLog /var/log/apache2/error.log
  CustomLog /var/log/apache2/access.log combined
 <Directory /var/www/html/public>
    Options FollowSymLinks
    AllowOverride None
    AddDefaultCharset utf-8
    DirectoryIndex index.php
    Require all granted
    <IfModule mod_rewrite.c>
        RewriteEngine On

        # Handle Authorization Header
        RewriteCond %{HTTP:Authorization} .
        RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

        # Redirect Trailing Slashes If Not A Folder...
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_URI} (.+)/$
        RewriteRule ^ %1 [L,R=301]

        # Send Requests To Front Controller...
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^ index.php [L]
    </IfModule>
  </Directory>
</VirtualHost>
```
entrypoint.sh
```
#!/bin/bash

# Init cron tab
#cron

# Load env vars
source /etc/apache2/envvars

# Run composer
#cd /var/www/html && composer update --no-scripts

# Set 777 to logs files
cd /var/www/html/ && chmod 777 storage -Rf

# Just give some more seconds to allow mysql to be up and running
#sleep 30

# Run migrations and seeds
#cd /var/www/html && php artisan migrate --force --env=local && php artisan db:seed --force --env=local

FILE=/var/www/html/storage/database/db_backup.sql
if [[ -f "$FILE" ]]; then
    echo "$FILE exists. Lets restore the db...."
    cd /var/www/html && php artisan db:backups --env=local
    echo "Restore done...."
fi

# Start apache and keep it running
exec apache2 -D FOREGROUND
```

./build/mysql

```
FROM mysql:latest
USER root
RUN chmod 755 /var/lib/mysql
```

structure ( project inside the dir "site" and ths ssl cert file are inside the dir "cert" )

![Logo do GitHub](https://jgomes.site/images/project_structure.png)

Vhost to proxy the site to the world (with ssl)

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
                ProxyPass / http://localhost:8888/
                ProxyPassReverse / http://localhost:8888/
        
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
Another vhost just to redirect requests that came to port 80 to 443
```
<VirtualHost *:80>
    ServerName jgomes.site
    ServerAlias www.jgomes.site
    Redirect permanent / https://jgomes.site/
</VirtualHost>
```
Case I need to open phpMyAdmin to would, just update the vhost with:
```
######################## START PROXY FOR PHPMYADMIN

    <Location "/phpmyadmin/">
        ProxyPass "http://localhost:8091/"
        ProxyPassReverse "http://localhost:8091/"

        # Set PMA_ABSOLUTE_URI to allow the loading off scripts
        SetEnv PMA_ABSOLUTE_URI "/phpmyadmin"

        # Force to set https as this vhost is 443
        RequestHeader set X-Forwarded-Proto "https"

        # Remove any method restriction for phpMyAdmin
        <LimitExcept OPTIONS>
            Require all granted
        </LimitExcept>

    </Location>

######################## END PROXY REVERSE FOR PHPMYADMIN
```
