FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libzip-dev

# Instalar extensión GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Instalar ZIP
RUN docker-php-ext-install zip

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
WORKDIR /app
COPY . .

# Instalar dependencias Laravel
RUN composer install --optimize-autoloader --no-interaction

# Instalar Node (para Vite/Tailwind)
RUN apt-get install -y nodejs npm

RUN npm install && npm run build

# Exponer puerto
EXPOSE 8080

# Ejecutar Laravel
CMD php -S 0.0.0.0:$PORT -t public