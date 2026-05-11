FROM php:8.3-apache

ENV EXTERNAL_HTTPS_PORT=8443

RUN apt-get update \
    && apt-get install -y --no-install-recommends openssl default-mysql-client \
    && docker-php-ext-install pdo_mysql \
    && a2enmod ssl rewrite headers \
    && mkdir -p /etc/apache2/ssl \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout /etc/apache2/ssl/clo2-selfsigned.key \
      -out /etc/apache2/ssl/clo2-selfsigned.crt \
      -subj "/C=ID/ST=Jawa Barat/L=Bandung/O=CLO2 Demo/OU=Security/CN=localhost" \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php-apache/secure-comments.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php-apache/docker-entrypoint.sh /usr/local/bin/secure-comments-entrypoint
COPY src/ /var/www/html/

RUN chmod +x /usr/local/bin/secure-comments-entrypoint \
    && chown -R www-data:www-data /var/www/html

ENTRYPOINT ["secure-comments-entrypoint"]
CMD ["apache2-foreground"]
