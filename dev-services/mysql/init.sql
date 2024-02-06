-- Drop the development user and database if they exist
DROP USER IF EXISTS '${DB_USERNAME}'@'%';
DROP DATABASE IF EXISTS ${DB_DATABASE};

-- Create the development database
CREATE DATABASE ${DB_DATABASE};

-- Create the development user and grant permissions
CREATE USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
