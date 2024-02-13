FROM rabbitmq:latest

#( Not workig here. Defined at docker-compose )
#ENV RABBITMQ_USER ${RABBIT_USER}
#ENV RABBITMQ_PASSWORD ${RABBIT_PASS}

#ENV RABBITMQ_DEFAULT_USER ${RABBIT_USER}
#ENV RABBITMQ_DEFAULT_PASS ${RABBIT_PASS}

ADD rabbitmq.config /etc/rabbitmq/
ADD definitions-local.json /etc/rabbitmq/definitions.json

RUN chmod 666 /etc/rabbitmq/*
RUN rabbitmq-plugins enable rabbitmq_management
