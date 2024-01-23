-- Drop the development user and database if they exist
DROP USER IF EXISTS 'user_dev'@'%';
DROP DATABASE IF EXISTS jgomes_site_dev;

-- Create the development database
CREATE DATABASE jgomes_site_dev;

-- Create the development user and grant permissions
CREATE USER 'user_dev'@'%' IDENTIFIED BY 'pass_dev';
GRANT ALL PRIVILEGES ON jgomes_site_dev.* TO 'user_dev'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
