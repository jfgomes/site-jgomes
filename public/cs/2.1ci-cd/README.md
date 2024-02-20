![Git flow logo](https://cdn-icons-png.flaticon.com/512/2519/2519375.png)

## Introduction

- This is a functional prototype to new projects.


## Diagram overview

![git-branch-protection.png](https://jgomes.site/images/diagrams/wms.drawio.png)

## Details

## Orchestration

## Complete infra CI/CD setup

Update the docker-composer with jenkins service:

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
  jenkins:
    build:
      context: './build/jenkins'
    restart: always
    ports:
      - "8889:8080"
      - "50000:50000"
    volumes:
      - jenkins-data:/var/jenkins_home
volumes:
  app:
  dbData:
  jenkins-data:
```
./build/jenkins
```
FROM jenkins/jenkins

USER root

# Adicional tool and extensions to PHP
RUN apt-get update \
    && apt-get install -y sudo vim curl iputils-ping nano php-cli php-curl php-xml php-json php-mbstring php-tokenizer php-xmlwriter libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Composer instalation
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

USER jenkins
```
jenkins-ssl.conf
```
LoadModule headers_module modules/mod_headers.so

<IfModule mod_ssl.c>
        LoadModule proxy_module modules/mod_proxy.so
        LoadModule proxy_http_module modules/mod_proxy_http.so
        <VirtualHost *:443>

                ServerAdmin zx.gomes@gmail.com
                ServerName jjenkins.xyz
                ErrorLog /var/log/apache2/jenkins_error.log
                CustomLog ${APACHE_LOG_DIR}/jenkins_access.log combined
                SSLEngine on
                SSLCertificateFile /home/jgomes/my/jgomes/cert/jenkins.crt
                SSLCertificateKeyFile /home/jgomes/my/jgomes/cert/jenkins.key
                SSLCertificateChainFile /home/jgomes/my/jgomes/cert/jenkins.ca-bundle

		ProxyRequests Off

		ProxyPass / http://localhost:8889/ nocanon
		ProxyPassReverse / http://localhost:8889/

		AllowEncodedSlashes NoDecode

		RequestHeader set X-Forwarded-Proto "https"
		RequestHeader set X-Forwarded-Port "443"

	        <Location />
	           Order allow,deny
		   Allow from all
		   AllowOverride all
	        </Location>
        </VirtualHost>
</IfModule>
```
structure update with ci/cd Dockerfile: 

![Logo do GitHub](https://jgomes.site/images/project_structure_cicd.png)

Another vhost just to redirect requests that came to port 80 to 443
```
 <VirtualHost *:80>
      ServerName jjenkins.xyz
      ServerAlias www.jjenkins.xyz
      Redirect permanent / https://jjenkins.xyz/
 </VirtualHost>
```

