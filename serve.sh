#!/bin/bash

# Verify if the user is not running this script in prod
if [ -e .env ]; then
    echo -e "\n 🤬🤬 No no no... Hey.. you are in prod. This script is only available to run in local environments. \n"
    exit 1
fi

# Verify if the user can use docker
if ! groups "$(whoami)" | grep &>/dev/null '\bdocker\b'; then
    echo -e "\n ❌ You are not in the Docker group. Please add yourself to the Docker group and restart the script."
    # groups $(whoami)
    # sudo usermod -aG docker
fi

CURRENT_DIRECTORY=$(pwd)
ENV_FILE=".env.dev"

# Verify if we have the param 'load-env-vars'
if [ "$1" == "load-env-vars" ]; then

    touch "$ENV_FILE"

    ZIP_FILE=env_vars_list_local.zip
    DESTINATION_FILE=env_vars_list_local.sh

    # Check if env vars zip is present in the project
    if [ ! -f "$ZIP_FILE" ]; then
        echo -e "\n ❌ You don't have the env vars zip in your project. Without this you cannot proceed. Ask the owner to unlock the script... \n"
        exit 1
    fi

    # Prompt the user for the password to unzip
    echo -e "\n 🙋‍♀️ 🙋 Welcome to \033[1;32mready to dev\033[0m script!\n"

    # shellcheck disable=SC2162
    read -s -p " 🔐 Enter the password to unlock the env vars: " PASSWORD
    echo

    echo -e "\n 🔑 Trying to unlock with the password.."

    # Unzip the file using the provided password
    unzip -P "$PASSWORD" "$ZIP_FILE" -d "$(dirname "$DESTINATION_FILE")"

    # Check the return code of the unzip command
    # shellcheck disable=SC2181
    if [ $? -ne 0 ]; then
      echo -e "\n ❌ Wrong password: The provided password is incorrect. Aborting the script...\n"
      exit 1
    fi

    echo -e "\n ✅ 🔓 Correct password: The env vars will be unlocked!"
    sleep 5

    # Run script to add env vars to the project
    chmod +x env_vars_set.sh
    ./env_vars_set.sh

    # Delete unzipped env vars
    rm "$DESTINATION_FILE"
else
    # Check if file .env.dev exists
    if [ ! -f "$ENV_FILE" ]; then
        echo -e "\n ⚠️  The file $ENV_FILE does not exist. Run './serve.sh load-env-vars' to create it with a password. \n"
        exit 1
    fi
fi

# Check if the environment is 'local' before running the script
if [ "$APP_ENV" != "local" ]; then
    echo -e "\n⛔️ This script is only be run in local environments. Probably you don't have the APP_ENV configured yet. Try type: 'APP_ENV=local ./serve.sh' \n"
    exit 1
fi

# shellcheck disable=SC2059
printf "\n \xF0\x9F\xA7\xAD My workdir: $CURRENT_DIRECTORY \n"

# Create new storage/db-backups directory if it not exists and change the permissions
mkdir -p storage/db-backups
chmod 755 storage/db-backups

# exit func
cleanup_and_exit() {
    echo -e " \n\n \xF0\x9F\xA4\x99 Cleaning up and exiting.. \n"

    # Clean up:
    # 1 - Clean logs
    echo -n > "$CURRENT_DIRECTORY/storage/logs/messages.log"
    echo -n > "$CURRENT_DIRECTORY/storage/logs/messages-backups.log"
    echo -n > "$CURRENT_DIRECTORY/storage/logs/emails.log"
    echo -n > "$CURRENT_DIRECTORY/storage/logs/access.log"

    # 2 - Remove and create new cron logs directory if exists
    rm -rf storage/cronlogs
    mkdir -p storage/cronlogs

    # 3 - Remove unneeded dir if exists
    rm -rf dev-services/storage

    # End the script
    exit
}

# Register the func cleanup_and_exit for the sign SIGINT (Ctrl+C)
trap cleanup_and_exit SIGINT

# Get the PID of this script
# shellcheck disable=SC2034
SCRIPT_PID=$$

# Find and kill all the serve.sh processes that can be running except he current
# shellcheck disable=SC2207
SERVE_PIDS=($(pgrep -f "serve.sh" | grep -v $$))

if [ ${#SERVE_PIDS[@]} -gt 0 ]; then
    # shellcheck disable=SC2145
    echo -e " \n \xF0\x9F\xA4\xA6\xE2\x80\x8D\xE2\x99\x82\xEF\xB8\x8F Ups.. found old './serve.sh' processes already running: ( ${SERVE_PIDS[@]} ). \xF0\x9F\x9A\xA7 Let's eliminate it.."

    for PID in "${SERVE_PIDS[@]}"; do
        echo -e " \n    \xF0\x9F\xA5\x8A Killing './serve.sh' process with PID: $PID"
        kill -9 "$PID"
        echo -e "    \xF0\x9F\x92\x80 The './serve.sh' process with PID $PID was killed.."
    done

    echo -e " \n \xF0\x9F\x8D\xBB All './serve.sh' processes killed. Starting a fresh one! Let´s continue.."
else
    echo -e " \n \xF0\x9F\x91\x8D Any './serve.sh' processes found in the system. Let´s continue.."
fi

# Kill artisan default web port to avoid it to create other ports
lsof -ti :8000 | xargs -r kill -9

# shellcheck disable=SC2164
cd dev-services

# Prepare .env vars for dev-services.
if [ -e .env ]; then
    rm .env
fi
ln -s ../.env.dev .env
source .env

############## rabbitmq/definitions env vars set START

# For JSON files it seems it cannot read env vars. Let's doing using other approach:
if [ -e rabbitmq/definitions.json ]; then
    cp rabbitmq/definitions.json rabbitmq/definitions-local.json
else
    echo "Error: rabbitmq/definition.json not found."
    exit 1
fi

# Read the content of rabbitmq/definitions-local JSON file
json_content=$(<rabbitmq/definitions-local.json)

# Replace the env vars to "real" vars
formatted_json=$(echo "$json_content" | sed -e "s/\${RABBIT_MESSAGE_QUEUE}/$RABBIT_MESSAGE_QUEUE/g" -e "s/\${RABBIT_PASS}/$RABBIT_PASS/g" -e "s/\${RABBIT_USER}/$RABBIT_USER/g")

# Save the JSON well formatted with the real env vars
echo "$formatted_json" > rabbitmq/definitions-local.json

############## rabbitmq/definitions env vars set END

############## mysql/init.sql env vars set START
# For sql file it cannot read env vars. Let's doing using other approach:
if [ -e mysql/init.sql ]; then
    cp mysql/init.sql mysql/init-local.sql
else
    echo "Error: mysql/init.sql not found."
    exit 1
fi

# Read the content of mysql/init.sql sql file
content_mysql=$(<mysql/init.sql)

# Replace the env vars to "real" vars
formatted_sql=$(echo "$content_mysql" | sed -e "s/\${DB_USERNAME}/$DB_USERNAME/g" -e "s/\${DB_DATABASE}/$DB_DATABASE/g" -e "s/\${DB_PASSWORD}/$DB_PASSWORD/g")

# Save the sql well formatted with the real env vars
echo "$formatted_sql" > mysql/init-local.sql

############## mysql/init.sql env vars set END

############## redis/redis.conf env vars set START
# For redis/redis.conf file it cannot read env vars. Let's doing using other approach:
if [ -e redis/redis.conf ]; then
    cp redis/redis.conf redis/redis-local.conf
else
    echo "Error: redis/redis.conf not found."
    exit 1
fi

# Read the content of redis/redis.conf file
content_config=$(<redis/redis.conf)

# Replace the env vars to "real" vars
formatted_config=$(echo "$content_config" | sed -e "s/\${REDIS_PORT}/$REDIS_PORT/g" -e "s/\${REDIS_PASS}/$REDIS_PASS/g")

# Save the config well formatted with the real env vars
echo "$formatted_config" > redis/redis-local.conf

############## redis/redis.conf env vars set END

# Check if one of this services is up
SERVICES=("mysql" "phpmyadmin" "rabbitmq" "redis" "redis-commander")

# shellcheck disable=SC2034
for service in "${SERVICES[@]}"; do

    # Check if we have at least one service up
    if docker-compose ps "$service" | grep -q "Up"; then

        # Refresh local docker services
        echo -e " \n \xE2\xAC\x87 Down docker images to refresh.."
        docker-compose down
        break
    fi
done

# Wait a bit
sleep 10

# Kill existing rabbit and redis processes
lsof -ti :5673 | xargs -r kill -9
lsof -ti :6380 | xargs -r kill -9
lsof -ti :15673 | xargs -r kill -9

# Need to have this cleaning ( laravel.log ) here as eliminated consumers in the last command, it generates messages on the log
echo -n > "$CURRENT_DIRECTORY/storage/logs/laravel.log"

# Run docker services
echo -e " \n \xE2\xAC\x86 Up docker images.."
docker-compose up -d

# Back to root
# shellcheck disable=SC2103
cd ..

if [ "$1" == "load-env-vars" ]; then
    # Update Composer
    echo -e "\n 🚀 Updating Composer packages...\n"
    composer update
fi

############## google credentials env var set START

# For JSON files it seems it cannot read env vars. Let's doing using other approach:
if [ -e gc.json ]; then
    cp gc.json gc-local.json
else
    echo "Error: gc.json not found."
    exit 1
fi

# Read the content of the JSON file
json_content_gc=$(<gc-local.json)

# Remove newline characters from the private key
cleaned_private_key=$(echo "$GC_PRIVATE_KEY" | tr -d '\n')

# Escape special characters in the private key to prevent misinterpretation by sed
escaped_private_key=$(printf '%s\n' "$cleaned_private_key" | sed -e 's/[\/&]/\\&/g')

# Substitute the environment variable in the JSON
formatted_json_gc=$(echo "$json_content_gc" | sed "s/{GC_PRIVATE_KEY}/$escaped_private_key/g")

# Remove the $ character at the beginning of the private key
formatted_json_gc="${formatted_json_gc/\$}"

# Save the well-formatted JSON with the actual environment variable
echo "$formatted_json_gc" > gc-local.json

############## google credentials env var set END

# Code coverage
rm -Rf storage/coverage-report
rm -Rf public/coverage-report

# Generate code coverage report
echo -e " \n \xF0\x9F\x93\x8A Run tests and generate code coverage.."
vendor/bin/phpunit --coverage-html storage/coverage-report
cd public && ln -s ../storage/coverage-report/ coverage-report && cd ..

# Refresh all the consumer log files
rm -f storage/cronlogs/output_consumer_*.log

# Wait a bit
sleep 15
echo "
                                                                   dddddddd
RRRRRRRRRRRRRRRRR                                                  d::::::d                                      tttt
R::::::::::::::::R                                                 d::::::d                                   ttt:::t
R::::::RRRRRR:::::R                                                d::::::d                                   t:::::t
RR:::::R     R:::::R                                               d:::::d                                    t:::::t
  R::::R     R:::::R    eeeeeeeeeeee    aaaaaaaaaaaaa      ddddddddd:::::dyyyyyyy           yyyyyyy     ttttttt:::::ttttttt       ooooooooooo
  R::::R     R:::::R  ee::::::::::::ee  a::::::::::::a   dd::::::::::::::d y:::::y         y:::::y      t:::::::::::::::::t     oo:::::::::::oo
  R::::RRRRRR:::::R  e::::::eeeee:::::eeaaaaaaaaa:::::a d::::::::::::::::d  y:::::y       y:::::y       t:::::::::::::::::t    o:::::::::::::::o
  R:::::::::::::RR  e::::::e     e:::::e         a::::ad:::::::ddddd:::::d   y:::::y     y:::::y        tttttt:::::::tttttt    o:::::ooooo:::::o
  R::::RRRRRR:::::R e:::::::eeeee::::::e  aaaaaaa:::::ad::::::d    d:::::d    y:::::y   y:::::y               t:::::t          o::::o     o::::o
  R::::R     R:::::Re:::::::::::::::::e aa::::::::::::ad:::::d     d:::::d     y:::::y y:::::y                t:::::t          o::::o     o::::o
  R::::R     R:::::Re::::::eeeeeeeeeee a::::aaaa::::::ad:::::d     d:::::d      y:::::y:::::y                 t:::::t          o::::o     o::::o
  R::::R     R:::::Re:::::::e         a::::a    a:::::ad:::::d     d:::::d       y:::::::::y                  t:::::t    tttttto::::o     o::::o
RR:::::R     R:::::Re::::::::e        a::::a    a:::::ad::::::ddddd::::::dd       y:::::::y                   t::::::tttt:::::to:::::ooooo:::::o
R::::::R     R:::::R e::::::::eeeeeeeea:::::aaaa::::::a d:::::::::::::::::d        y:::::y                    tt::::::::::::::to:::::::::::::::o
R::::::R     R:::::R  ee:::::::::::::e a::::::::::aa:::a d:::::::::ddd::::d       y:::::y                       tt:::::::::::tt oo:::::::::::oo
RRRRRRRR     RRRRRRR    eeeeeeeeeeeeee  aaaaaaaaaa  aaaa  ddddddddd   ddddd      y:::::y                          ttttttttttt     ooooooooooo
                                                                                y:::::y
                                                                               y:::::y
                                                                              y:::::y
                                                                             y:::::y
                                                                            yyyyyyy



            dddddddd
            d::::::d                                                 !!!
            d::::::d                                                !!:!!
            d::::::d                                                !:::!
            d:::::d                                                 !:::!
    ddddddddd:::::d     eeeeeeeeeeee  vvvvvvv           vvvvvvv     !:::!
  dd::::::::::::::d   ee::::::::::::ee v:::::v         v:::::v      !:::!
 d::::::::::::::::d  e::::::eeeee:::::eev:::::v       v:::::v       !:::!
d:::::::ddddd:::::d e::::::e     e:::::e v:::::v     v:::::v        !:::!
d::::::d    d:::::d e:::::::eeeee::::::e  v:::::v   v:::::v         !:::!
d:::::d     d:::::d e:::::::::::::::::e    v:::::v v:::::v          !:::!
d:::::d     d:::::d e::::::eeeeeeeeeee      v:::::v:::::v           !!:!!
d:::::d     d:::::d e:::::::e                v:::::::::v             !!!
d::::::ddddd::::::dde::::::::e                v:::::::v
 d:::::::::::::::::d e::::::::eeeeeeee         v:::::v               !!!
  d:::::::::ddd::::d  ee:::::::::::::e          v:::v               !!:!!
   ddddddddd   ddddd    eeeeeeeeeeeeee           vvv                 !!!

"

###### Service test connections start
# DB
# Check if there are any changes in the codebase
# Run the database-related commands only when there are changes
php artisan tinker --execute="DB::select('SELECT 1')" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo -e "\n ✅  Successfully pinged Mysql \n"
        # Run migrations
        echo -e " 🚀 Running migrations...\n"
        php artisan migrate --path="database/*"
else
    echo -e "\n ❌  Connection to Mysql failed. \n"
    exit 1
fi

# RabbitMQ
php artisan rabbitmq:ping > /dev/null 2>&1
if [ $? -eq 0 ]; then
    # Success
    echo -e "\n ✅  Successfully pinged RabbitMQ \n"
else
    # Failure
    echo -e "\n ❌  Connection to RabbitMQ failed. \n"
    exit 1
fi

# Redis local port != default = 6379 to avoid misconception
redis-cli -p 6378 ping > /dev/null 2>&1
if [ $? -eq 0 ]; then
    # Success
    echo -e " ✅  Successfully pinged Redis \n"
else
    # Failure
    echo -e " ❌  Connection to Redis failed. \n"
    exit 1
fi
###### Service test connections end

# Init the server and get the PID
php -d apc.enable_cli=1 artisan serve &
SERVER_PID=$!

# Set the number of consumers to RABBIT_CONSUMERS_LIMIT
RABBIT_CONSUMERS_LIMIT=3

echo -e " ✅  All services are up and running.. ( 'ctrl + c' to exit ) \n"
sleep 10

# Verify if cron logs dir exists
CRON_LOGS_DIR="storage/cronlogs"
if [ ! -d "$CRON_LOGS_DIR" ]; then
    mkdir -p "$CRON_LOGS_DIR"
fi

# Turn on the rabbitmq listeners to run the queues
for ((i=1; i<=RABBIT_CONSUMERS_LIMIT; i++)); do
        RABBIT_LOG_FILE="storage/cronlogs/output_consumer_$i.log"

        # Verify log file
        if [ ! -f "$RABBIT_LOG_FILE" ]; then
            touch "$RABBIT_LOG_FILE"
        fi
        echo -e "\n \xF0\x9F\x9A\x80 Running messages consumer $i.. \n"
        nohup php artisan queue:messages --is-scheduled=true >> "$RABBIT_LOG_FILE" 2>&1 &
        sleep 60 # Never less than 10 seconds
        disown
done &

# Monitor the server PID and terminate the backup loop when the server is no longer running
while kill -0 $SERVER_PID 2>/dev/null; do
    MSG_LOG_FILE="storage/cronlogs/messages-backups.log"

    # Verify log file
    if [ ! -f "$MSG_LOG_FILE" ]; then
        touch "$MSG_LOG_FILE"
    fi
    echo -e "\n \xF0\x9F\x9A\x80 Running messages-backups to cloud bucket..\n"
    nohup php artisan db:messages-backup-to-cloud >> "$MSG_LOG_FILE" 2>&1
    sleep 7200
done

# Terminate the script when the loop in background is over
cleanup_and_exit
