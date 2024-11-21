FROM php:8.2-apache

# Copy the entrypoint to the container and make it our entrypoint
COPY entrypoint.sh /sbin/entrypoint.sh
RUN chmod +x /sbin/entrypoint.sh

# Update package lists and install dependencies
RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        unzip \
        git \
        libonig-dev \
        cron \
        nano \
        default-mysql-client

# Set the default system editor
ENV EDITOR=nano

# Install and enable the necessary PHP extensions
RUN docker-php-ext-install pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && pecl install apcu \
    && docker-php-ext-enable apcu

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure Apache to use the 'public' directory as DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Create the working directory and copy the Dockerfile and necessary files
WORKDIR /var/www/html

# Enable rewrite
RUN a2enmod rewrite

# Copy the virtual host to the container and activate it
COPY app.conf /etc/apache2/sites-available/
RUN a2dissite 000-default
RUN a2ensite app.conf

# Expose port 81
EXPOSE 81

# Copy the crontab file to the cron.d directory
COPY crontab /etc/cron.d/jgomes-site-cron

RUN echo "" >> /etc/cron.d/jgomes-site-cron

# Give execution rights to the cron job
RUN chmod 0644 /etc/cron.d/jgomes-site-cron

# Apply the cron job
RUN crontab /etc/cron.d/jgomes-site-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

# Start the Apache service
CMD ["/sbin/entrypoint.sh"]
