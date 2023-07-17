FROM webdevops/php-nginx:8.2-alpine

# Copy Composer binary from the Composer official Docker image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV WEB_DOCUMENT_ROOT /var/www/app/public

WORKDIR /var/www/app

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN chown -R application:application .