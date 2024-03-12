apt-get update && apt-get install -y     apt-transport-https     ca-certificates     curl     gnupg     lsb-release \ software-properties-common
apt-get update && apt-get install -y     apt-transport-https     ca-certificates     curl     gnupg     lsb-release    software-properties-common
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg     && echo "deb [signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null     && apt-get update && apt-get install -y docker-ce docker-ce-cli containerd.io
curl -fsSL https://github.com/docker/compose/releases/latest/download/docker-compose-Linux-x86_64 -o /usr/local/bin/docker-compose     && chmod +x /usr/local/bin/docker-compose

   apt-get install apache2
   # shellcheck disable=SC2164
   cd /var/www/html/

   # Add keys
   git clone git@github.com:jfgomes/site-jgomes-prod-infra.git

   # shellcheck disable=SC2164
   cd site-jgomes-prod-infra/

   git clone git@github.com:jfgomes/site-jgomes.git

   docker-compose up -d

   a2dissite default-ssl.conf

   # cp vhosts
   #cp vhost-app-ssl.conf
   #cp a2ensite vhost-80-443.conf

   75  a2dissite 000-default.conf

   80  a2ensite vhost-80-443.conf
   84  a2ensite vhost-app-ssl.conf

   89  systemctl reload apache2


   91  sudo apt-get install libapache2-mod-proxy-html
   92  apt-get install libapache2-mod-proxy-html
   93  apt-get install libapache2-mod-proxy
   94  sudo apt-get install libapache2-mod-proxy
   95  sudo apt-get install apache2-bin
   96  sudo a2enmod proxy
  103  sudo a2enmod proxy_http

    110  sudo a2enmod headers

  111  systemctl restart apache2

  116  docker-compose down
  117  docker-compose up
  118  cd /var/www/html/

  120  cd site-jgomes-prod-infra/


  125  docker-compose down
  126  docker-compose up

  128  cd prod-services/

  130  cd php-apache/

  136  cd ..
  137  docker-compose up --build php-apache
  138  cd prod-services/php-apache/

  141  cd ..
  142  docker-compose up --build php-apache
  143  docker-compose up -d
  144  ls -l
  145  cd site-jgomes/
  146  sudo apt-get install composer
  147  composer update
  148  sudo apt-get install php-curl
  149  sudo systemctl restart apache2

  151  composer update
  152  sudo apt-get install ext-dom
  153  sudo apt-get install php-xml
  154  sudo systemctl restart apache2
  155  composer update
  156
  158  nano .env
  15
  160  cd ..
  161  docker-compose down
  162  docker-compose up -d

  168  npm install
  169  apt install npm
  171  npm run production


  122  sudo chmod 600 crt
  123  sudo chmod 600 ca-bundle
  125  openssl rsa -in key -check
  126  sudo chmod 600 key
  127  openssl rsa -in key -check
  161  rm 000-default.conf
  162  rm default-ssl.conf
  163  nano vhost-80-443.conf
  164  a2dissite vhost-app.conf
  165  sudo systemctl restart apache2
  166


  170  cat vhost-jenkins.conf
  171  nano vhost-jenkins.conf
  172  cat vhost-app-ssl.conf
  173  nano vhost-jenkins.conf

  186  a2dissite vhost-app.conf
  187  sudo systemctl restart apache2
  188  cat /etc/apache2/sites-available/vhost-app-ssl.conf



  197  nano jenkins-ca-bundle
  198  nano jenkins-key
  199  nano jenkins-crt

  202  a2ensite vhost-jenkins.conf
  203  systemctl reload apache2



  184  cd site-jgomes/
  185  php artisan migrate
  188  sudo apt-get install php-mysql
  189  sudo service apache2 restart

  191  php artisan migrate


  195  php artisan migrate
  196  docker ps

  200  cd /etc/apache2/sites-available/
  2
  202  sudo a2enmod ssl
  203  systemctl restart apache2


  209  nano id_rsa
  210  nano id_rsa.pub



  227  sudo chmod 600 crt
  228  sudo chmod 600 ca-bundle


  246  systemctl status apache2.service
  247  ls -l /var/www/html/site-jgomes-prod-infra/certs/key
  248  sudo chmod 600 /var/www/html/site-jgomes-prod-infra/certs/key
  249  ls -l /var/www/html/site-jgomes-prod-infra/certs/key
  250  sudo openssl rsa -in /var/www/html/site-jgomes-prod-infra/certs/key -check
  251  cp /var/www/html/site-jgomes-prod-infra/certs/key .
  252  ls -l
  253  sudo openssl rsa -in key -check
  254  cat key
  255  nano key
  256  sudo openssl rsa -in key -check
  257  cd /var/www/html/site-jgomes-prod-infra/
  258  cd certs/
  259  nano key
  260  nano crt
  261  sudo systemctl restart apache2
  262  cd /etc/apache2/sites-available/
  263  ls -l
  264  rm key
  265  ls -l
  266  rm 000-default.conf
  267  rm default-ssl.conf
  268  nano vhost-80-443.conf
  269  a2dissite vhost-app.conf
  270  sudo systemctl restart apache2


  283  nano jenkins-ca-bundle

  285  nano jenkins-ctr

  287  nano jenkins-key
  288  ls -l
  289  chmod 600 *
  290  ls -l
  291  a2dissite vhost-app.conf

  299  sudo systemctl restart apache2
  300  ls -l
  301  cat jenkins-ca-bundle
  302  nano jenkins-ca-bundle
  303  nano jenkins-key
  304  nano jenkins-crt
  307  a2ensite vhost-jenkins.conf
  308  systemctl reload apache2
  309  history
  310  cd ..
  311  ls -l
  312  cd site-jgomes/
  313  ls -l
  314  php artisan migrate

  320  cd storage/

  324  chmod 777 -Rf framework/cache/
  325  chmod 777 -Rf framework/sessions/

  329  cd public/

  338  cd public/
  339  ls -ls
  340  rm coverage-report
  341  ln -s ../storage/coverage-report/ coverage-report

  343  cd ..

  346  apt install php-pear
  347  pecl install xdebug
  348  apt install php-pear

  350  sudo apt-get install php-dev
  351  pecl install xdebug

  363  echo "xdebug.mode=coverage" | sudo tee -a $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
  364  echo "zend_extension=xdebug.so" | sudo tee -a $(php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||")
  365  sudo service apache2 restart
  366  cd /var/www/html/site-jgomes-prod-infra/site-jgomes/
  367  vendor/bin/phpunit --coverage-html storage/coverage-report
cd public/
ln -s ../storage/coverage-report/ coverage-report

  375  sed -i "s|<head>|<head><title>Coverage</title>|" "storage/coverage-report/index.html" && sed -i "s|<head>|<head><title>Dashboard</title>|" "storage/coverage-report/dashboard.html" && find "storage/coverage-report" -type f -exec sed -i "s#/var/www/html/site-jgomes-prod-infra/site-jgomes/app#(Coverage)#g" {} +
