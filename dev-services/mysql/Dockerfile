# Use an official MySQL runtime as a parent image
FROM mysql:latest

# Set environment variables
ENV MYSQL_DATABASE_DEV=${DB_DATABASE}
ENV MYSQL_USER_DEV=${DB_USERNAME}
ENV MYSQL_PASSWORD_DEV=${DB_PASSWORD}

# Copy the database initialization script to the docker-entrypoint-initdb.d directory
COPY ./init.sql /docker-entrypoint-initdb.d/

# Expose the MySQL port
EXPOSE 3306

# Start MySQL service
CMD ["mysqld"]
