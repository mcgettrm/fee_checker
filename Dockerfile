FROM php:8.4-cli-alpine

WORKDIR /app

COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd xdebug

# Grab a composer binary
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Drop this outside of the project root so it doesn't get overwritten by the docker-compose volume
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]