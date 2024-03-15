FROM rabbitmq:latest

ADD rabbitmq.config /etc/rabbitmq/
ADD definitions-local.json /etc/rabbitmq/definitions.json

RUN chmod 666 /etc/rabbitmq/*
RUN rabbitmq-plugins enable rabbitmq_management
