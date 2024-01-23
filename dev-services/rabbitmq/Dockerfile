FROM rabbitmq:latest

ENV RABBITMQ_USER user_dev
ENV RABBITMQ_PASSWORD pass_dev

ENV RABBITMQ_DEFAULT_USER user_dev
ENV RABBITMQ_DEFAULT_PASS pass_dev

ADD rabbitmq.config /etc/rabbitmq/
ADD definitions.json /etc/rabbitmq/

RUN chmod 666 /etc/rabbitmq/*

RUN rabbitmq-plugins enable rabbitmq_management
