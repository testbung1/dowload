# Sử dụng bản PHP chính thức, nhẹ và bảo mật
FROM php:8.2-cli

# Cài đặt các thư viện cần thiết để đọc repo (SSL, Curl)
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-install bcmath

# Copy file index.php từ GitHub vào trong Docker
COPY index.php /app/index.php

# Đặt thư mục làm việc
WORKDIR /app

# Chạy server PHP khi Docker khởi động
CMD ["php", "-S", "0.0.0.0:80", "index.php"]
