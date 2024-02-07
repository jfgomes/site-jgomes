# Use a imagem base do redis-commander
FROM rediscommander/redis-commander:latest

# Defina as variáveis de ambiente
#ENV REDIS_HOSTS local:redis:6379
ENV HTTP_USER ${REDIS_USER}
ENV HTTP_PASSWORD ${REDIS_PASS}

COPY settings.json /usr/src/app/settings.json

# Expõe a porta desejada (8081 no seu caso)
EXPOSE 8081
