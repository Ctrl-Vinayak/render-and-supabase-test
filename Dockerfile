FROM php:8.2-apache

# 1. Installeer Linux benodigdheden & PostgreSQL drivers voor Supabase
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# 2. Schakel Apache Mod_Rewrite in (cruciaal voor Laravel routes)
RUN a2enmod rewrite

# 3. Pas de Apache document root aan naar de 'public' map van Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Kopieer alle projectbestanden naar de container
COPY . /var/www/html

# 5. Installeer Composer (PHP pakketbeheerder) binnen de container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 6. Zet de Linux rechten direct goed voor Render
RUN chown -r www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Stel de poort in die Render verwacht
EXPOSE 80
