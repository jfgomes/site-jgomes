#!/bin/bash

CURRENT_DIRECTORY=$(pwd)

# shellcheck disable=SC2059
printf "\n \xF0\x9F\xA7\xAD My workdir: $CURRENT_DIRECTORY \n"

# Create new storage/db-backups directory if it not exists and change the permissions
chmod 755 storage/db-backups
mkdir -p storage/db-backups

# exit func
cleanup_and_exit() {
    echo -e " \n\n \xF0\x9F\xA4\x99 Cleaning up and exiting.. \n"

    # Clean up:
    # 1 - Clean logs
    echo -n > "$CURRENT_DIRECTORY/storage/logs/messages.log"
    echo -n > "$CURRENT_DIRECTORY/storage/logs/messages-backups.log"

    # 2 - Remove and create new cron logs directory if exists
    rm -rf storage/cronlogs
    mkdir -p storage/cronlogs

    # End the script
    exit
}

# Register the func cleanup_and_exit for the sign SIGINT (Ctrl+C)
trap cleanup_and_exit SIGINT

# Get the PID of this script
SCRIPT_PID=$$

# Find and kill all the serve.sh processes that can be running
SERVE_PIDS=($(pgrep -f "serve.sh"))

if [ ${#SERVE_PIDS[@]} -gt 0 ]; then
    echo -e " \n \xF0\x9F\xA4\xA6\xE2\x80\x8D\xE2\x99\x82\xEF\xB8\x8F Ups.. found old './serve.sh' processes already running: ( ${SERVE_PIDS[@]} ). \xF0\x9F\x9A\xA7 Let's eliminate each one.."

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

# Check if one of this services is up
SERVICES=("mysql" "phpmyadmin" "rabbitmq")

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

# Kill existing rabbit processes
lsof -ti :5672 | xargs -r kill -9

# Need to have this cleaning ( laravel.log ) here as eliminated consumers in the last command, it generates messages on the log
echo -n > "$CURRENT_DIRECTORY/storage/logs/laravel.log"

# Run docker services
echo -e " \n \xE2\xAC\x86 Up docker images.."
docker-compose up -d

# Project vars can go here, like:
export APP_ENV=local

# Back to root
# shellcheck disable=SC2103
cd ..

# Code coverage
rm -Rf storage/coverage-report
rm -Rf public/coverage-report

# Generate code coverage report
echo -e " \n \xE2\xAC\x86 Generate code coverage.."
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

# Set the number of consumers to RABBIT_CONSUMERS_LIMIT
RABBIT_CONSUMERS_LIMIT=2

# Turn on the rabbitmq listeners to run the queues
for ((i=1; i<=RABBIT_CONSUMERS_LIMIT; i++)); do
    echo -e "\n \xF0\x9F\x9A\x80 Running messages consumer $i.. \n"
    nohup php artisan queue:messages >> storage/cronlogs/output_consumer_$i.log 2>&1 &
    sleep 5
    disown
done &

# Init the server and get the PID
echo -e " \xF0\x9F\x91\x8D All services are up and running.. ( 'ctrl + c' to exit ) \n"
php artisan serve &
SERVER_PID=$!

# Monitor the server PID and terminate the backup loop when the server is no longer running
while kill -0 $SERVER_PID 2>/dev/null; do
    echo -e "\n \xF0\x9F\x9A\x80 Running messages-backups to cloud bucket..\n"
    nohup php artisan db:messages-backup-to-cloud >> storage/cronlogs/messages-backups.log 2>&1
    sleep 3600
done

# Terminate the script when the loop in background is over
cleanup_and_exit
