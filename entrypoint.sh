#!/bin/bash
echo "Starting entrypoint script..."

# Vérifier si Redis est installé
if command -v redis-server > /dev/null; then
    echo "Redis server found at: $(which redis-server)"
    echo "Starting Redis server..."
    redis-server /etc/redis/redis.conf &
    sleep 2
    if redis-cli ping > /dev/null; then
        echo "Redis is running successfully"
    else
        echo "Redis failed to start"
        exit 1
    fi
else
    echo "Redis server not found"
    exit 1
fi

# Créez le fichier de log avec les permissions correctes
#touch /var/log/artisan_cron.log
#chmod 666 /var/log/artisan_cron.log
touch /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log

chmod -R 777 /var/www/html/storage

mkdir /var/www/html/app/public
chown -R  www-data:www-data /var/www/html/app/public
chmod -R 775 /var/www/html/app/public

# Lancer Apache en mode foreground
exec apache2-foreground
