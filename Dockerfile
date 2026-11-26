FROM dunglas/frankenphp:1-php8.4

RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    bcmath \
    mbstring \
    exif \
    gd \
    opcache \
    redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN useradd --create-home --shell /bin/bash --uid 1000 itdel \
    && mkdir -p /app /opt/itdel-app /data/caddy /config/caddy \
    && chown -R itdel:itdel /app /opt/itdel-app /data /config /home/itdel

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/home/itdel/.composer

WORKDIR /opt/itdel-app

# Layer cache: install package dulu (hanya berubah jika composer.json/lock berubah)
COPY --chown=itdel:itdel composer.json composer.lock* ./

USER itdel

RUN --mount=type=cache,target=/home/itdel/.composer/cache,uid=1000,gid=1000 \
    composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
        --no-autoloader

USER root

# Salin sisa kode aplikasi
COPY --chown=itdel:itdel . .

USER itdel

RUN --mount=type=cache,target=/home/itdel/.composer/cache,uid=1000,gid=1000 \
    composer dump-autoload --optimize \
    && mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache

USER root

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER itdel

WORKDIR /app

EXPOSE 8000

VOLUME ["/app"]

ENTRYPOINT ["entrypoint.sh"]
