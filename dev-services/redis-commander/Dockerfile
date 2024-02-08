# redis-commander base image
FROM rediscommander/redis-commander:latest

# Defined here the env vars
ENV REDIS_HOSTS app:redis:6379:0,messages:redis:6379:1
ENV HTTP_USER user_dev
ENV HTTP_PASSWORD pass_dev

# Port expose
EXPOSE 8081
