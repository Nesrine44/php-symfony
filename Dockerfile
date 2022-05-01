FROM php:7.1.0-fpm

ARG debug="false"
ENV PHP_APCU_VERSION 5.1.8
ENV PHP_XDEBUG_VERSION 2.5.0

RUN printf "deb http://archive.debian.org/debian/ jessie main\ndeb-src http://archive.debian.org/debian/ jessie main\ndeb http://security.debian.org jessie/updates main\ndeb-src http://security.debian.org jessie/updates main" > /etc/apt/sources.list

RUN apt-get update \
    && apt-get install -y \
        libicu-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg-dev \
        sudo \
        vim \
        nginx \
        rsync \
        ssh \
        nginx-extras \
        python-setuptools \
        software-properties-common \
        cron \
    && docker-php-source extract \
    && curl -L -o /tmp/apcu-$PHP_APCU_VERSION.tgz https://pecl.php.net/get/apcu-$PHP_APCU_VERSION.tgz \
    && curl -L -o /tmp/xdebug-$PHP_XDEBUG_VERSION.tgz http://xdebug.org/files/xdebug-$PHP_XDEBUG_VERSION.tgz \
    && tar xfz /tmp/apcu-$PHP_APCU_VERSION.tgz \
    && tar xfz /tmp/xdebug-$PHP_XDEBUG_VERSION.tgz \
    && rm -r \
        /tmp/apcu-$PHP_APCU_VERSION.tgz \
        /tmp/xdebug-$PHP_XDEBUG_VERSION.tgz \
    && mv apcu-$PHP_APCU_VERSION /usr/src/php/ext/apcu \
    && mv xdebug-$PHP_XDEBUG_VERSION /usr/src/php/ext/xdebug \
    && docker-php-ext-install \
        apcu \
        intl \
        mbstring \
        mysqli \
        pdo \
        gd \
        pdo_mysql \
        xdebug \
        zip \
        bcmath \
        opcache \
    && pecl install apcu_bc-1.0.3 \
    && docker-php-source delete \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

RUN docker-php-ext-configure gd \
       --with-png-dir=/usr/lib/ \
       --with-jpeg-dir=/usr/lib/ \
       --with-gd

#install Imagemagick & PHP Imagick ext
RUN apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \ && docker-php-ext-enable imagick

# Install supervisor
RUN easy_install supervisor && \
    mkdir -p /var/log/supervisor && \
    mkdir -p /var/run/sshd && \
    mkdir -p /var/run/supervisord

# Add supervisord conf
ADD docker/php7/supervisord.conf /etc/supervisord.conf

# cron scripts
COPY docker/php7/run-queue.sh /root/run-queue.sh
COPY docker/php7/update-portfolio.sh /root/update-portfolio.sh
COPY docker/php7/send-promote-innovation-emails.sh /root/send-promote-innovation-emails.sh
RUN chmod 775 /root/run-queue.sh /root/update-portfolio.sh /root/send-promote-innovation-emails.sh

# Add crontab file in the cron directory
COPY docker/php7/crontab /etc/cron.d/pr-cron
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/pr-cron
RUN touch /var/log/cron.log

# Create socket
#RUN mkdir -p /run/php
#RUN touch /run/php/php7.0-fpm.sock

# Shell
COPY docker/php7/build.sh /build.sh
RUN chmod 775 /build.sh

# VirtualHost
COPY docker/php7/default.prod /etc/nginx/sites-available/default

# Editing Nginx conf
RUN sed -i '16s/sendfile on;/sendfile off;/g' /etc/nginx/nginx.conf
RUN sed -i '/nginx.pid;/a daemon off;' /etc/nginx/nginx.conf
RUN service nginx stop

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

# Copy source files if not debug
COPY htdocs /var/www/symfony
RUN mkdir /var/www/symfony/web/uploads
RUN chmod -R 777 /var/www/symfony/var/cache /var/www/symfony/var/logs /var/www/symfony/var/sessions /var/www/symfony/web/uploads /var/www/symfony/web/exports /var/www/symfony/web/ppt
RUN chown -R www-data /var/www/symfony/var/cache /var/www/symfony/var/logs /var/www/symfony/var/sessions /var/www/symfony/web/uploads /var/www/symfony/web/exports /var/www/symfony/web/ppt

RUN mv /var/www/symfony/app/config/parameters.yml.dist /var/www/symfony/app/config/parameters.yml
RUN mv /var/www/symfony/app/config/environment_parameters.prod.yml /var/www/symfony/app/config/environment_parameters.yml

# remove app_dev.php if not debug
#RUN rm /var/www/symfony/web/app_dev.php

# Adding custom nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Configure PATH
RUN echo export PATH=/usr/sbin:$PATH >> ~/.bashrc

# Export xterm
RUN echo export TERM=xterm >> ~/.bashrc

# forward request and error logs to docker log collector
#RUN ln -sfT /dev/stdout /var/www/symfony/var/logs/dev.log && ln -sfT /dev/stdout /var/www/symfony/var/logs/prod.log

# copy php.ini parameters
COPY docker/php7/php.ini /usr/local/etc/php/conf.d/custom.ini

# save environnement variable
#RUN printenv | sed 's/^\(.*\)$/export \1/g' >> /project_env.sh


# Execution shell on run container
CMD ["/build.sh"]


EXPOSE 80