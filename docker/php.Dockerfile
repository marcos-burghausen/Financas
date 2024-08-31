# Etapa 1: Construção do Composer
FROM composer:2.6 as build
WORKDIR /app
COPY . .
RUN composer update
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

# Etapa 2: PHP e Apache com Cron
FROM php:8.2-apache-bullseye

# Instala as extensões PHP necessárias
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql

# Instalação do Cron
RUN apt-get update && apt-get install -y cron

# Copia o código da aplicação
COPY --from=build /app /var/www/html

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Adiciona o cron e configura o cron job
RUN echo "* * * * * cd /var/www/html && /usr/local/bin/php artisan schedule:run >> /var/www/html/storage/logs/crontab.log 2>&1" > /etc/cron.d/laravel-scheduler

# Dá permissão de execução ao arquivo crontab
RUN chmod 0644 /etc/cron.d/laravel-scheduler

# Aplica o crontab
RUN crontab /etc/cron.d/laravel-scheduler

# Copie o script de inicialização para o contêiner
COPY docker-entrypoint.sh /usr/local/bin/

# Torne o script executável
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Defina o script como o comando de inicialização
CMD ["docker-entrypoint.sh"]
