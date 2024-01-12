## Advanced Setup ( Infra configs )

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

# Set proxy
ProxyPass /phpmyadmin http://localhost:8091/
ProxyPassReverse /phpmyadmin http://localhost:8091/

# Set PMA_ABSOLUTE_URI to allow the loading off scripts
SetEnv PMA_ABSOLUTE_URI "/phpmyadmin"

# Force to set https as this vhost is 443
RequestHeader set X-Forwarded-Proto "https"

# Remove any method restriction for phpMyAdmin
<Location /phpmyadmin>
    <LimitExcept OPTIONS>
        Require all granted
    </LimitExcept>
</Location>

######################## END PROXY REVERSE FOR PHPMYADMIN
```
