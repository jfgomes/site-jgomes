## Basic setup ( No need to configure the infra )
- git clone git@github.com:jfgomes/site-jgomes.git
- composer update
- cp .env.example .env ( Need to add the configs to .env )
- php artisan key:generate
- php artisan serve --port=90 ( The port is not mandatory. By default is 80 )
