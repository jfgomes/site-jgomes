# Use an official MySQL runtime as a parent image
FROM mysql:latest

# Set environment variables
ENV MYSQL_DATABASE_DEV jgomes_site_test
ENV MYSQL_USER_DEV user_test
ENV MYSQL_PASSWORD_DEV pass_test

# Copy the database initialization script to the docker-entrypoint-initdb.d directory
COPY ./init.sql /docker-entrypoint-initdb.d/

# Expose the MySQL port
EXPOSE 3306

# Start MySQL service
CMD ["mysqld"]
