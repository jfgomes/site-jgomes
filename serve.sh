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
sleep 10

# Set the number of consumers to RABBIT_CONSUMERS_LIMIT one consumer more that .env.dev ( 4 there ) by purpose to test the limit
RABBIT_CONSUMERS_LIMIT=3

# Remove cron logs directory if it exists
rm -rf cronlogs

# Create new cron logs directory
mkdir -p cronlogs

# Refresh all the consumer log files
rm -f cronlogs/output_consumer_*.log

# Turn on the rabbitmq listeners to run the processes automatically as soon as the project runs
for ((i=1; i<=RABBIT_CONSUMERS_LIMIT; i++)); do
    echo "Starting messages consumer $i... you can see the log in cronlogs/output_consumer_$i.log"
    nohup php artisan queue:messages > cronlogs/output_consumer_$i.log 2>&1 &
    sleep 5
    disown
done

# Init server in the background
php artisan serve &

# Code coverage
cd public && ln -s ../reports/ unit-report && cd ..

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

