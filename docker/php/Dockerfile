# PHP 8.4.27 = rilis patch untuk CVE-2026-109618 & CVE-2026-109619 (21 Okt 2026)
FROM php:8.4.27-fpm-alpine

ARG UID=1000
ARG GID=1000

# apk upgrade -> ikut memperbarui CA bundle (kasus sertifikat eMudhra) & paket OS lain
RUN apk upgrade --no-cache \
 && apk add --no-cache git unzip curl bash \
      libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev icu-dev oniguruma-dev libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip gd intl bcmath exif pcntl opcache

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Hardening PHP
RUN { \
      echo "expose_php=Off"; \
      echo "display_errors=Off"; \
      echo "log_errors=On"; \
      echo "memory_limit=512M"; \
      echo "upload_max_filesize=20M"; \
      echo "post_max_size=25M"; \
      echo "opcache.enable=1"; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

# User non-root dengan UID/GID sama seperti host agar file tidak jadi milik root
RUN addgroup -g ${GID} app && adduser -D -u ${UID} -G app app \
 && mkdir -p /var/www/html && chown app:app /var/www/html

COPY docker/php/init.sh /usr/local/bin/init.sh
RUN sed -i 's/\r$//' /usr/local/bin/init.sh && chmod +x /usr/local/bin/init.sh

USER app
WORKDIR /var/www/html
EXPOSE 9000

ENTRYPOINT ["init.sh"]
CMD ["php-fpm"]
