#
# ALL CHANGES TO THIS FILE MUST BE REVIEWED BY DEVOPS
#

FROM quay.io/maplesyrupgroup/alpine-base-php5:latest

# Add app
COPY php-app /app

# Do not use laravel.log and specify exceptions path
ENV APP_LOG errorlog
ENV APP_EXCEPTIONS_DIR /vendor/maple-syrup-group/q-common/php-app/Exceptions

# Set permissions for build
RUN mkdir -p /app/vendor/ && \
    chown -R build:build /app/vendor/ && \
    mkdir -p /app/bootstrap/cache/ && \
    chown -R build:build /app/bootstrap/cache/

# Run composer install as user 'build' and clean up the cache
USER build
RUN composer install --no-interaction --no-ansi --no-progress --prefer-dist && composer clear-cache --no-ansi --quiet
USER root

# Fix permissions
RUN chown -R root:root /app/vendor/ && \
    chmod -R go-w /app/vendor/ && \
    chown -R www:www /app/bootstrap/cache/ && \
    mkdir -p /app/storage/app/ && \
    chown -R www:www /app/storage/app/ && \
    mkdir -p /app/storage/framework/cache/ && \
    chown -R www:www /app/storage/framework/cache/

# Add custom startup script
COPY rc.local /etc/rc.local

# Record build info
RUN /etc/build-info record search

# Run a healthcheck as user 'www'
RUN s6-setuidgid www php artisan infra:healthcheck
