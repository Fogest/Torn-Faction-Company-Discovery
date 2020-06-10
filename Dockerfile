FROM php:7.4-alpine

RUN echo "https://mirror.csclub.uwaterloo.ca/alpine/latest-stable/main" > /etc/apk/repositories
RUN echo "https://mirror.csclub.uwaterloo.ca/alpine/latest-stable/community" >>/etc/apk/repositories

# Install dev dependencies
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    curl-dev \
    imagemagick-dev \
    libtool \
    libxml2-dev \
    postgresql-dev \
    sqlite-dev

# Install production dependencies
RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    g++ \
    gcc \
    git \
    icu-dev \
    icu-libs \
    imagemagick \
    libc-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    make \
    mysql-client \
    nodejs \
    nodejs-npm \
    oniguruma-dev \
    yarn \
    openssh-client \
    postgresql-libs \
    rsync \
    zlib-dev \
    redis

# Install PECL and PEAR extensions
RUN pecl install \
    imagick \
    xdebug \
    redis

# Enable PECL and PEAR extensions
RUN docker-php-ext-enable \
    imagick \
    xdebug \
    redis

# Configure php extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install php extensions
RUN docker-php-ext-install \
    bcmath \
    calendar \
    curl \
    exif \
    gd \
    iconv \
    intl \
    mbstring \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    pcntl \
    tokenizer \
    xml \
    zip

# Install composer
ENV COMPOSER_HOME /composer
ENV PATH ./vendor/bin:/composer/vendor/bin:$PATH
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

# Install PHP_CodeSniffer
RUN composer global require "squizlabs/php_codesniffer=*"

# Install redis
RUN mkdir /data && \
    chown -R redis:redis /data && \
    sed -i 's#logfile /var/log/redis/redis.log#logfile ""#i' /etc/redis.conf && \
    sed -i 's#daemonize yes#daemonize no#i' /etc/redis.conf && \
    sed -i 's#dir /var/lib/redis/#dir /data#i' /etc/redis.conf && \
    echo -e "# placeholder for local options\n" > /etc/redis-local.conf && \
    echo -e "include /etc/redis-local.conf\n" >> /etc/redis.conf

VOLUME ["/data"]
# Expose the ports for redis
EXPOSE 6379

# Cleanup dev dependencies
RUN apk del -f .build-deps

# Setup working directory
WORKDIR /var/www
