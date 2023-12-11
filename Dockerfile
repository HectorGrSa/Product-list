# Utilizamos PHP con Apache en la versión 8.3
FROM php:8.3-apache

# Actualizamos e instalamos las dependencias necesarias
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    libsqlite3-dev

# Habilitamos el modulo de Apache para rewrite
RUN a2enmod rewrite

# Instalamos las extensiones de PHP necesarias para SQLite y Zip
RUN docker-php-ext-install zip pdo_sqlite

# Habilitamos la extension de PHP para SQLite
RUN docker-php-ext-enable pdo_sqlite

# Copiamos todo el codigo de la aplicacion al contenedor
COPY . /var/www/html

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalamos las dependencias de Composer
RUN composer install

# Configuramos los permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
