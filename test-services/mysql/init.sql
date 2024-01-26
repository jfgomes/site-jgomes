-- Drop the development user and database if they exist
DROP USER IF EXISTS 'user_test'@'%';
DROP DATABASE IF EXISTS jgomes_site_test;

-- Create the development database
CREATE DATABASE jgomes_site_test;

-- Create the development user and grant permissions
CREATE USER 'user_test'@'%' IDENTIFIED BY 'pass_test';
GRANT ALL PRIVILEGES ON jgomes_site_test.* TO 'user_test'@'%';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;
