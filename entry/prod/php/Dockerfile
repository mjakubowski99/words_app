FROM php:8.4-fpm

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
RUN docker-php-ext-configure intl;

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

RUN docker-php-ext-install opcache;

COPY entry/prod/php/memory-limit-php.ini /usr/local/etc/php/conf.d/memory-limit-php.ini
COPY entry/prod/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

RUN chown -R $USER:$USER /usr/bin/composer

COPY --chown=$USER:$USER . /var/www

USER $USER

WORKDIR /var/www/entry

RUN composer install --no-dev

EXPOSE 9000

CMD ["php-fpm"]
