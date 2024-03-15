# Use an official MySQL runtime as a parent image
FROM mysql:latest

# Copy the database initialization script to the docker-entrypoint-initdb.d directory
COPY init-local.sql /tmp/init.sql

# Expose the MySQL port
EXPOSE 3306

# Start MySQL service
CMD ["mysqld", "--init-file=/tmp/init.sql"]
