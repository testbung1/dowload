FROM php:8.2-apache

# Cài đặt cURL mở rộng
RUN apt-get update && apt-get install -y libcurl4-openssl-dev pkg-config \
    && docker-php-ext-install curl

# Thiết lập giới hạn RAM trực tiếp vào cấu hình PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

COPY index.php /var/www/html/index.php

# Cho phép ghi file cache
RUN touch /var/www/html/repo_cache.json && chmod 777 /var/www/html/repo_cache.json

WORKDIR /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]
