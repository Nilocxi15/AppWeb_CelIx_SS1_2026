FROM php:8.3-cli-bookworm

# Evitar prompts interactivos durante la instalación
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias del sistema y clientes de PostgreSQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    zip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    postgresql-client \
    ca-certificates \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias para Laravel y PostgreSQL
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    mbstring \
    bcmath \
    gd \
    opcache \
    pcntl

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js (LTS v20) y npm para compilar recursos frontend con Vite si se requiere
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Directorio de trabajo de la aplicación
WORKDIR /var/www/html

# Crear script de entrada directamente en Linux para evitar problemas de saltos de línea CRLF de Windows
RUN printf '#!/bin/bash\n\
set -e\n\
\n\
# Verificar si se especificaron variables de DB para esperar conexión\n\
if [ -n "$DB_HOST" ]; then\n\
    echo "Verificando disponibilidad de PostgreSQL en $DB_HOST:${DB_PORT:-5432}..."\n\
    until pg_isready -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-postgres}" -q; do\n\
        echo "Esperando a que PostgreSQL inicie..."\n\
        sleep 1\n\
    done\n\
    echo "Base de datos PostgreSQL conectada y lista."\n\
fi\n\
\n\
# Si no existe archivo .env dentro de la app, crearlo a partir del template\n\
if [ ! -f .env ]; then\n\
    if [ -f .env.docker.example ]; then\n\
        cp .env.docker.example .env\n\
    elif [ -f .env.example ]; then\n\
        cp .env.example .env\n\
    fi\n\
    php artisan key:generate --no-interaction || true\n\
fi\n\
\n\
# Asegurar directorios de almacenamiento y permisos de escritura\n\
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache\n\
chmod -R 777 storage bootstrap/cache\n\
\n\
exec "$@"\n' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto de Laravel
EXPOSE 8000

# Punto de entrada y comando por defecto
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
