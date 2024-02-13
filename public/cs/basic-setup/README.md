![Ready to Dev logo](https://jgomes.site/images/cs/ready-to-dev.jpeg)

## Introduction

- The goal of this project is to create an easy way to set an environment up and running with just a single command. 


- No need to configure anything manual.


- This project uses the laravel artisan server + dev-services like mysql, phpmyadmin, rabbit, rabbit listeners, redis, redis-commander etc.. all in containers.. 


- No configs in physical machine.. no monolith configs.. all is auto.


- The project is public, whoever the env vars are not in the repository and without this config and respective password the project cannot be mounted. Contact me for more details. 


- Also is not possible to push code directly to master as there is a protection rule for it. Every change needs a PR, and needs to be approved by the owner.

## Requirements

- docker

- docker-compose

- nodejs

- npm

- xdebug

- redis-tools - case Ubuntu / Debian || redis - case macOS

## How to set up

1) Clone the project: git clone git@github.com:jfgomes/site-jgomes.git


2) cd site-jgomes


3) Ensure to have the file env_var_list_local.zip in the root of the project and the password to open it. This is not versioned, and you need to request this to the owner. 


4) In the first run the command is './serve.sh load-env-vars' ( after the first run only './serve.sh' is enough as the env vars are loaded in the first run )


5) And is done.

## Details about the serve.sh script

- This script is NOT designed to run in prod. In prod, It needs specific infra.


- If 'load-env-vars' came as a param of ./serve.sh it will load / reload all the credentials of the project. This information is in a zip file protected by a password that needs to be in the root of the project and this is not versioned in the repo. Need to ask for it.


- It checks if some pending project PID are running. In so, it kills it.


- It mounts all the services defined at dev-services dir by the file docker-composer.yam


- Based on the zip file protected by a password all the credentials in there, if successfully unlocked, will put all the credentials in place.


- It generates the code coverage.


- It runs the composer, the npm and db migrations case some db change is detected.


- It creates the listeners for rabbit.


- It runs the cons based on the scheduler kernel. It persists in background.


## Services and other information

- In local, the web server is provided by the laravel artisan. ( In prod there's a dedicated service for this using apache )


- The services phpmyadmin and redis came directly from the docker hub as it doesn't need customizations. The other services are customized and need to be built.

### docker-compose.yml

```
version: "3.9"
services:
    mysql:
        container_name: jgomes_site_dev_mysql
        build: './mysql'
        env_file:
            - .env
        ports:
            - "3406:3306"
        environment:
            MYSQL_ALLOW_EMPTY_PASSWORD: yes
        volumes:
            - dbData:/var/lib/mysql

    phpmyadmin:
        container_name: jgomes_site_dev_phpmyadmin
        image: phpmyadmin/phpmyadmin
        platform: linux/amd64
        env_file:
            - .env
        ports:
            - "8090:80"
        environment:
            PMA_HOST: mysql
            MYSQL_ALLOW_EMPTY_PASSWORD: yes

    rabbitmq:
        container_name: jgomes_site_dev_rabbit
        build:
            context: './rabbitmq'
        env_file:
            - .env
        ports:
            - "5672:5672"
            - "15672:15672"
        environment:
            -  RABBITMQ_USER=${RABBIT_USER}
            -  RABBITMQ_PASSWORD=${RABBIT_PASS}
            -  RABBITMQ_DEFAULT_USER=${RABBIT_USER}
            -  RABBITMQ_DEFAULT_PASS=${RABBIT_PASS}

    redis:
        container_name: jgomes_site_dev_redis
        restart: always
        image: redis:latest
        command: ["redis-server", "--bind", "${REDIS_HOST}", "--port", "${REDIS_PORT}"]
        volumes:
            - redis:/var/lib/redis
            - redis-config:/usr/local/etc/redis/redis.conf
        ports:
            - "6379:6379"
        networks:
            - redis-network

    redis-commander:
        container_name: jgomes_site_dev_redis-commander
        build: './redis-commander'
        platform: linux/amd64
        restart: always
        ports:
            - "8081:8081"
        networks:
            - redis-network
        depends_on:
            - redis
        environment:
            - REDIS_HOSTS=${REDIS_HOSTS}
            - HTTP_USER=${REDIS_USER}
            - HTTP_PASSWORD=${REDIS_PASS}
networks:
    redis-network:
        driver: bridge
volumes:
    dbData:
    redis:
    redis-config:

```
## List of customized services:

---
### Mysql service:
#### Dockerfile

```
    # Use an official MySQL runtime as a parent image
    FROM mysql:latest
    
    # Copy the database initialization script to the docker-entrypoint-initdb.d directory
    COPY init-local.sql /tmp/init.sql
    
    # Expose the MySQL port
    EXPOSE 3306
    
    # Start MySQL service
    CMD ["mysqld", "--init-file=/tmp/init.sql"]
```

#### init.sql
```
    -- Drop the development user and database if they exist
    DROP USER IF EXISTS '${DB_USERNAME}'@'%';
    
    -- Create the development database if it doesn't exist
    CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};
    
    -- Create the development user and grant permissions
    CREATE USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
    GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%';
    
    -- Flush privileges to apply changes
    FLUSH PRIVILEGES;
```
NOTE: The './serve.sh' script will get the init.sql file and will create a new file called init-local.sql with all the credential in place based on the zip. The Dockerfile for mysql will use this file. The init-local.sql is not versioned.

---
### Rabbitmq service:
#### Dockerfile
```
    FROM rabbitmq:latest
    
    ADD rabbitmq.config /etc/rabbitmq/
    ADD definitions-local.json /etc/rabbitmq/definitions.json
    
    RUN chmod 666 /etc/rabbitmq/*
    RUN rabbitmq-plugins enable rabbitmq_management
```

#### definitions.jsom
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
NOTE: The './serve.sh' script will get the definitions.json file and will create a new file called definitions-local.json with all the credential in place based on the zip. The Dockerfile for rabbitmq will use this file. The definitions-local.json is not versioned.

#### rabbitmq.config
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
NOTE: this will load the definitions in order to make the setup, create, the user. create the user, etc.. 

---
### Redis-commander service:
#### Dockerfile

```
    # redis-commander base image
    FROM rediscommander/redis-commander:latest
    
    # Defined here the env vars
    ENV REDIS_HOSTS=${REDIS_HOSTS}
    ENV HTTP_USER ${REDIS_USER}
    ENV HTTP_PASSWORD ${REDIS_PASS}
    
    # Port expose
    EXPOSE 8081
```
---

## Ready to Dev! diagram

![Ready to Dev! flow diagram](https://jgomes.site/images/diagrams/ready-to-dev4.drawio.png)

## Demonstration 
( Click on the image to watch the video )

[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=EnLrH0_FCnU)

