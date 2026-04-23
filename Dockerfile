FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Copiar todos los archivos del proyecto
COPY . /var/www/html/

# Iniciar Apache
CMD ["apache2-foreground"]
