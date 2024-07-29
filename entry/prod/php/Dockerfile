FROM php:8.2.21-fpm

ARG USER
ARG GID
ARG UID

USER root

RUN addgroup --system --gid $GID $USER
RUN adduser --system --ingroup $USER --uid $UID $USER

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    curl \
    g++ \
    gifsicle \
    git \
    jpegoptim \
    libfontconfig1 \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libxrender1 \
    libzip-dev \
    locales \
    optipng \
    pngquant \
    unzip \
    vim \
    zip \
    zlib1g-dev \
    wget \
    gnupg

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# configure php extensions
RUN docker-php-ext-configure intl &&  \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg

# install php extensions
RUN docker-php-ext-install \
    exif \
    gd \
    intl \
    pcntl \
    pdo \
    pgsql \
    pdo_pgsql \
    zip

COPY entry/docker/config/php/memory-limit-php.ini /usr/local/etc/php/conf.d/memory-limit-php.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R $USER:$USER /usr/bin/composer

COPY --chown=$USER:$USER . /var/www

USER $USER

WORKDIR /var/www/entry

RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
