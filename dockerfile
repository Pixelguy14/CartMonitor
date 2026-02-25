FROM php:8.2-apache

# Primero instalamos las extensiones necesarias para MySQL y PDO 
RUN docker-php-ext-install pdo pdo_mysql
# incluimos el soporte para la herramienta "Prepared Statements" vía pdo_mysql

# Segundo habilitamos mod_rewrite de Apache para el Front Controller 
# Esto permite que /productos sea procesado por index.php sin mostrar la extensión
RUN a2enmod rewrite

# Tercero configuramos el DocumentRoot para que apunte a la carpeta 'public'
# Siguiendo practicas de seguridad, el código fuente (app/) nunca debe ser accesible vía URL
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Asegurar permisos y crear enlace simbólico para que storage sea accesible desde public
RUN mkdir -p /var/www/html/storage/images && \
    ln -s /var/www/html/storage /var/www/html/public/storage && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html