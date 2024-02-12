-- Drop the development user and database if they exist
DROP USER IF EXISTS '${DB_USERNAME}'@'%';

-- Create the development database if it doesn't exist
CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};

-- Create the development user and grant permissions
CREATE USER '${DB_USERNAME}'@'%' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
