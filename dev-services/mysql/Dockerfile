# Use an official MySQL runtime as a parent image
FROM mysql:latest

# Set environment variables
ENV MYSQL_DATABASE_DEV jgomes_site_dev
ENV MYSQL_USER_DEV user_dev
ENV MYSQL_PASSWORD_DEV pass_dev

# Copy the database initialization script to the docker-entrypoint-initdb.d directory
COPY ./init.sql /docker-entrypoint-initdb.d/

# Expose the MySQL port
EXPOSE 3306

# Start MySQL service
CMD ["mysqld"]
