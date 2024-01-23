#!/bin/bash

# Kill artisan default port to avoid it to create other ports
lsof -ti :8000 | xargs -r kill -9

# Refresh local docker services
cd dev-services && docker-compose down && docker-compose up -d

# Project vars can go here, like:
export APP_ENV=local

# Back to root
cd ..

# Give some time to dev services be up and running
sleep 15

# Set the number of consumers to RABBIT_CONSUMERS_LIMIT one consumer more that .env.dev ( 4 there ) by purpose to test the limit
RABBIT_CONSUMERS_LIMIT=5

# Remove cron logs directory if it exists
rm -rf cronlogs

# Create new cron logs directory
mkdir -p cronlogs

# Refresh all the consumer log files
rm -f cronlogs/output_consumer_*.log

# Turn on the rabbitmq listeners to run the processes automatically as soon as the project runs
for ((i=1; i<=RABBIT_CONSUMERS_LIMIT; i++)); do
    echo "Starting consumer $i... you can see the log in cronlogs/output_consumer_$i.log"
    nohup php artisan queue:messages > cronlogs/output_consumer_$i.log 2>&1 &
    sleep 5
    disown
done

# Init server in the background
php artisan serve &

