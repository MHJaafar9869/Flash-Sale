FROM dunglas/frankenphp:1.9-php8.4-bookworm AS base

ARG WWWUSER=1000
ARG WWWGROUP=1000

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    XDG_CONFIG_HOME=/var/www/html/config \
    XDG_DATA_HOME=/var/www/html/data \
    XDEBUG_MODE=off

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y curl git zip unzip libpng-dev libicu-dev nodejs npm netcat-openbsd && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    bcmath curl gd intl mbstring opcache pdo_mysql redis zip pcntl

RUN curl -sLS https://getcomposer.org/installer | php -- \
				--install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist --no-scripts --no-dev

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN if [ -f package.json ]; then npm install && npm run build; fi

RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/config/caddy \
    /var/www/html/data/caddy

RUN chmod -R 755 /var/www/html/config/caddy /var/www/html/data/caddy

COPY Caddyfile /etc/caddy/Caddyfile
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080 5173

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
