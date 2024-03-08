![Hide vars Logo](https://jgomes.site/images/cs/hide-env-vars.png)

## Introduction

- For projects based in this idea, I don't want to have any public reference of credentials even for local environment, so the env vars are installed right after cloning the project

- In local, Laravel will look for a env file called .env.dev in the bootstrap file

- In prod, Laravel will look for a env file called .env in the bootstrap file

- This env vars files are not versioned in the project repository ( both .env.dev and .env )

- These values are private, are generated in a zip with a password and needs the owner of the project to generate and provide this zip and respective password to unlock this information and allow the instalation to proceed

- This zip needs to be in the root of the project for the init set up

- In local, the serve.sh script is responsible to create the env vars ( .env.dev file + env vars in services - Dockerfile + aux scripts ) with the param ‘load-env-vars'

- In prod, the serve.sh script is not used, but there's other private script that does the same work ( .env file + env vars in services - Dockerfile + aux scripts )

- This zip needs to kept in the .gitignore to ensure we don't send this info to repository

- This zip file can be deleted from the project afterwords when the project is installed with success. The param ‘load-env-vars’ is not needed anymore.

## Requirements

- Request the owner of the project the needed zip and respective password to unlock all credentials in order to set up the project

- The credentials file need to be at the root of the project

## More details about the technical implementation
#### Script 'create_zip.sh' to create the zip file with the password:
![Script to create the zip file with the password](https://jgomes.site/images/cs/hide-env-vars/create_zip.png)

#### Running the script 'create_zip.sh':
![ls](https://jgomes.site/images/cs/hide-env-vars/ls.png)

#### Store the zip file created in the root of the project:
![Where to store Script zip file](https://jgomes.site/images/cs/hide-env-vars/sh_on_project_root.png)

#### Part of the content of the zip:
![The content of the zip](https://jgomes.site/images/cs/hide-env-vars/vars.png)

## How it works:
#### Running the cmd 'serve.sh' with the param 'load-env-vars':
![How in local it works](https://jgomes.site/images/cs/hide-env-vars/setup.png)

#### It creates the .env.dev file
```
APP_DEBUG='true'
APP_ENV='local'
APP_KEY='base64:TskZ8G06T470vcwJ2eM0loIPfxdCO3jM+c5JMl79pPY='
APP_NAME='JGomes-Site-local'
APP_ROUTE_COOKIE_FLAG='local'
APP_URL='http://127.0.0.1:8000'
LOG_CHANNEL='single'
LOG_DEPRECATIONS_CHANNEL='null'
LOG_LEVEL='debug'
MAIL_FROM_ADDRESS='null'
MAIL_FROM_NAME='JGomes-Site-local'
MAIL_LOG_CHANNEL='emails'
MAIL_MAILER='log'
DB_CONNECTION=mysql
DB_DATABASE=jgomes_site_dev
DB_HOST=127.0.0.1
DB_PASSWORD=pass_dev
DB_PORT=3406
DB_USERNAME=user_dev
RABBIT_API_HOST='http://127.0.0.1:15672/api'
RABBIT_CONSUMERS_LIMIT='2'
RABBIT_HOST='127.0.0.1'
RABBIT_MESSAGE_QUEUE='messages_dev'
RABBIT_PASS='pass_dev'
RABBIT_PORT='5672'
RABBIT_USER='user_dev'
REDIS_DB_APP_LABEL='app'
REDIS_DB_APP_NUM='0'
REDIS_DB_MESSAGES_LABEL='messages'
REDIS_DB_MESSAGES_NUM='1'
REDIS_HOST='redis'
REDIS_PASS='pass_dev'
REDIS_PORT='6379'
REDIS_USER='user_dev'
GC_CLOUD_FILE='messages-latest-backup.json'
GC_CLOUD_PATH='jgomes.site/messages/'
GC_HOST_FILE='messages-latest-backup.json'
GC_HOST_PATH='/storage/db-backups/'......=\n-----END PRIVATE KEY-----\n'
REDIS_DB_TESTS_NUM='2'
REDIS_DB_TESTS_LABEL='locations'
REDIS_HOSTS='app:redis:6379:0,messages:redis:6379:1,locations:redis:6379:2'
```
#### It creates the mysql service init-local.sql based on init.sql

- Before the set-up we have the base file init.sql:
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

- After the set-up we have the init-local.sql with the credentials in place::
```
-- Drop the development user and database if they exist
DROP USER IF EXISTS 'user_dev'@'%';

-- Create the development database if it doesn't exist
CREATE DATABASE IF NOT EXISTS jgomes_site_dev;

-- Create the development user and grant permissions
CREATE USER 'user_dev'@'%' IDENTIFIED BY 'pass_dev';
GRANT ALL PRIVILEGES ON jgomes_site_dev.* TO 'user_dev'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
```

#### It creates the rabbitmq service definitions-local.sql based on definitions.sql

- Before the set-up we have the base file definitions.json:
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
- After the set-up we have the definitions-local.json with the credentials in place:
```
{
    "exchanges": [
        {
            "name": "messages_dev",
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
            "name": "user_dev",
            "password": "pass_dev",
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
            "user": "user_dev",
            "vhost": "/",
            "configure": ".*",
            "write": ".*",
            "read": ".*"
        }
    ],
    "queues": [
        {
            "name": "messages_dev",
            "vhost": "/",
            "durable": true,
            "auto_delete": false,
            "arguments": {}
        }
    ],
    "bindings": [
        {
            "source": "messages_dev",
            "vhost": "/",
            "destination": "messages_dev",
            "destination_type": "queue",
            "routing_key": "messages_dev",
            "arguments": {}
        }
    ]
}

```
#### After the set-up what to do?
- After the init setup, the zip file can be deleted
- And now we only need to run './serve.sh'

## Demonstration
#### ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=b1rO8AxdWWU)
