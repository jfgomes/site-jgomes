## Production environment

![Ready to Dev logo](https://jgomes.site/images/cs/adilia.png)

## Introduction

- The goal of this is to create an easy way to set a prod environment up and running with just a single command ( WIP ).

## Orchestration

docker-composer:

```
version: "3.9"
services:
    php-apache:
        container_name: jgomes_site_prod_php-apache
        restart: always
        build: './prod-services/php-apache'
        ports:
            - "8888:81"
        volumes:
            - ./site-jgomes:/var/www/html
        depends_on:
            - mysql
        networks:
            - jgomes-site_prod-docker

    mysql:
        container_name: jgomes_site_prod_mysql
        restart: always
        build: './prod-services/mysql'
        ports:
            - "3306:3306"
        environment:
            MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASS}
            MYSQL_DATABASE: ${DB_DATABASE}
            MYSQL_USER: ${DB_USER}
            MYSQL_PASSWORD: ${DB_PASS}
        volumes:
            - dbData:/var/lib/mysql
        networks:
            - jgomes-site_prod-docker

    phpmyadmin:
        container_name: jgomes_site_prod_phpmyadmin
        image: phpmyadmin/phpmyadmin
        restart: always
        ports:
            - "8091:80"
        environment:
            PMA_HOST: mysql
            MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASS}
            MYSQL_ROOT_USER: ${DB_ROOT_USER}
        depends_on:
            - php-apache
        networks:
            - jgomes-site_prod-docker
    
    rabbitmq:
        container_name: jgomes_site_prod_rabbit
        restart: always
        build:
            context: './prod-services/rabbitmq'
        ports:
            - "5672:5672"
            - "15672:15672"
        networks:
            - jgomes-site_prod-docker

    redis:
        container_name: jgomes_site_prod_redis
        restart: always
        image: redis:latest
        command: ["redis-server", "--bind", "${REDIS_HOST}", "--port", "${REDIS_PORT}"]
        volumes:
            - redis:/var/lib/redis
            - redis-config:/usr/local/etc/redis/redis.conf
        ports:
            - "6379:6379"
        networks:
            - jgomes-site_prod-docker

    redis-commander:
        container_name: jgomes_site_prod_redis-commander
        build: './prod-services/redis-commander'
        platform: linux/amd64
        restart: always
        ports:
            - "8081:8081"
        networks:
            - jgomes-site_prod-docker
        depends_on:
            - redis
        environment:
            - REDIS_HOSTS: ${REDIS_HOSTS}
            - HTTP_USER: ${REDIS_USER}
            - HTTP_PASSWORD: ${REDIS_PASS}
volumes:
    app:
    dbData:
    redis:
    redis-config:

networks:
    jgomes-site_prod-docker:
        driver: bridge
        ipam:
            driver: default
            config:
                - subnet: "172.18.0.0/16"
```

## Service list

./build/php-apache/Dockerfile

```
    FROM php:8.2-apache
    
    # Copy the entrypoint to the container and make it our entrypoint
    COPY entrypoint.sh /sbin/entrypoint.sh
    RUN chmod +x /sbin/entrypoint.sh
    
    # Update package lists and install dependencies
    RUN apt-get update \
        && apt-get install -y \
            libzip-dev \
            unzip \
            git \
            libonig-dev \
            cron \
            nano \
            default-mysql-client
    
    # Set the default system editor
    ENV EDITOR=nano
    
    # Install and enable the necessary PHP extensions
    RUN docker-php-ext-install pdo_mysql \
        && docker-php-ext-enable pdo_mysql
    
    # Install Composer globally
    RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    
    # Configure Apache to use the 'public' directory as DocumentRoot
    ENV APACHE_DOCUMENT_ROOT /var/www/html/public
    RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
    RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
    
    # Create the working directory and copy the Dockerfile and necessary files
    WORKDIR /var/www/html
    
    # Enable rewrite
    RUN a2enmod rewrite
    
    # Copy the virtual host to the container and activate it
    COPY app.conf /etc/apache2/sites-available/
    RUN a2dissite 000-default
    RUN a2ensite app.conf
    
    # Expose port 81
    EXPOSE 81
    
    # Copy the crontab file to the cron.d directory
    COPY crontab /etc/cron.d/jgomes-site-cron
    
    RUN echo "" >> /etc/cron.d/jgomes-site-cron
    
    # Give execution rights to the cron job
    RUN chmod 0644 /etc/cron.d/jgomes-site-cron
    
    # Apply the cron job
    RUN crontab /etc/cron.d/jgomes-site-cron
    
    # Create the log file to be able to run tail
    RUN touch /var/log/cron.log
    
    # Start the Apache service
    CMD ["/sbin/entrypoint.sh"]
```

./build/php-apache/app.conf
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

./build/php-apache/entrypoint.sh
```
    #!/bin/bash
    
    # Init cron tab
    service cron start
    
    # Load env vars
    source /etc/apache2/envvars
    
    # Run composer
    #cd /var/www/html && composer update --no-scripts
    
    # Create db backups dir
    cd /var/www/html/storage && mkdir db-backups
    
    # Set 777 to logs files
    cd /var/www/html/ && chmod 777 storage -Rf
    
    # Just give some more seconds to allow mysql to be up and running
    #sleep 30
    
    # Run migrations and seeds
    #cd /var/www/html && php artisan migrate --force --env=local && php artisan db:seed --force --env=local
    
    FILE=/var/www/html/storage/database/db_backup.sql
    if [[ -f "$FILE" ]]; then
        echo "$FILE exists. Lets restore the db...."
    #    cd /var/www/html && php artisan db:backups --env=local
        echo "Restore done...."
    fi
    
    # Start apache and keep it running
    exec apache2 -D FOREGROUND
```

./build/mysql/Dockerfile
```
    # Use an official MySQL runtime as a parent image
    FROM mysql:latest
    
    # Use an official MySQL runtime as a parent image
    FROM mysql:latest
    
    # Set environment variables
    ENV MYSQL_ROOT_USER ${DB_ROOT_USER}
    ENV MYSQL_ROOT_PASSWORD ${DB_ROOT_PASS}
    ENV MYSQL_DATABASE_PROD ${DB_DATABASE_PROD}
    ENV MYSQL_USER_PROD ${DB_USER_PROD}
    ENV MYSQL_PASSWORD_PROD ${DB_PASS_PROD}
    
    # Copy the database initialization script to the docker-entrypoint-initdb.d directory
    COPY ./init.sql /docker-entrypoint-initdb.d/
    
    # Expose the MySQL port
    EXPOSE 3406
    
    # Start MySQL service
    CMD ["mysqld"]
```

./build/rabbitmq/Dockerfile
```
    FROM rabbitmq:latest
    
    ADD rabbitmq.config /etc/rabbitmq/
    ADD definitions.json /etc/rabbitmq/
    
    RUN chmod 666 /etc/rabbitmq/*
    RUN rabbitmq-plugins enable rabbitmq_management
```

./build/rabbitmq/definitions.json
```
{
    "exchanges": [
        {
            "name": "${RABBIT_MESSAGE_QUEUE}",
            "vhost": "/",
            "type": "direct",
            "durable": true,
            "auto_delete": false,
            "internal": false,
            "arguments": {}
        }
    ],
    "users": [
        {
            "name": "${RABBIT_USER}",
            "password": "${RABBIT_PASS}",
            "tags": "administrator"
        }
    ],
    "vhosts": [
        {
            "name": "/"
        }
    ],
    "permissions": [
        {
            "user": "${RABBIT_USER}",
            "vhost": "/",
            "configure": ".*",
            "write": ".*",
            "read": ".*"
        }
    ],
    "queues": [
        {
            "name": "${RABBIT_MESSAGE_QUEUE}",
            "vhost": "/",
            "durable": true,
            "auto_delete": false,
            "arguments": {}
        }
    ],
    "bindings": [
        {
            "source": "${RABBIT_MESSAGE_QUEUE}",
            "vhost": "/",
            "destination": "${RABBIT_MESSAGE_QUEUE}",
            "destination_type": "queue",
            "routing_key": "${RABBIT_MESSAGE_QUEUE}",
            "arguments": {}
        }
    ]
}
```

./build/rabbitmq/rabbitmq.config

```
    [
      {rabbit,
        [
          {loopback_users, []}
        ]
      },
      {rabbitmq_management,
        [
          {load_definitions, "/etc/rabbitmq/definitions.json"}
        ]
      }
    ].
```

./build/redis-commanders/Dockerfile

```
    # redis-commander base image
    FROM rediscommander/redis-commander:latest
    
    # Port expose
    EXPOSE 8081
```

#### Vhost to proxy the site to the world (with ssl)

```
    <IfModule mod_ssl.c>
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule proxy_http_module modules/mod_proxy_http.so
        LoadModule headers_module modules/mod_headers.so
    
        <VirtualHost *:443>
            ServerAdmin zx.gomes@gmail.com
            ServerName jgomes.site
            ErrorLog /var/log/apache2/jgomes_error.log
            CustomLog ${APACHE_LOG_DIR}/jgomes_access.log combined
            SSLEngine on
            SSLCertificateFile /var/www/html/site-jgomes-prod-infra/certs/crt
            SSLCertificateKeyFile /var/www/html/site-jgomes-prod-infra/certs/key
            SSLCertificateChainFile /var/www/html/site-jgomes-prod-infra/certs/ca-bundle
    
            ProxyRequests Off
    
            ProxyPass / http://localhost:8888/
            ProxyPassReverse / http://localhost:8888/
    
            <Location />
                Order allow,deny
                Allow from all
                AllowOverride all
            </Location>
        </VirtualHost>
    </IfModule>
```

#### Another vhost just to redirect requests that came to port 80 to 443
```
    <VirtualHost *:80>
        ServerName jgomes.site
        ServerAlias www.jgomes.site
        Redirect permanent / https://jgomes.site/
    </VirtualHost>
```

#### Case we need to open phpMyAdmin to would, just update the vhost with:
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

#### Case we need to open rabbitmq to would, just update the vhost with:
```
    ######################## START PROXY REVERSE FOR RABBITMQ AND RABBITMQ API

    AllowEncodedSlashes NoDecode
    <Location "/rabbitmq/">
        ProxyPass "http://localhost:15672/"
        ProxyPassReverse "http://localhost:15672/"
    </Location>

    <Location "/rabbitmq/api">
        ProxyPass "http://localhost:15672/api" nocanon
        ProxyPassReverse "http://localhost:15672/"
    </Location>

    ######################## END PROXY REVERSE FOR RABBITMQ AND RABBITMQ API
```

#### Case we need to open redis to would, just update the vhost with:
```
    ######################## START PROXY REVERSE FOR REDIS-COMMANDER

    <Location "/redis/">
        ProxyPass "http://localhost:8081/"
        ProxyPassReverse "http://localhost:8081/"
    </Location>

    ######################## START PROXY REVERSE FOR REDIS-COMMANDER
```

#### /etc/hosts on the host machine
```
127.0.0.1 rabbitmq
127.0.0.1 mysql
127.0.0.1 redis
```
