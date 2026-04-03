1. После клонирования репозитория выполнить

    composer install
    
2. создаем файл .env, копируем в него содержимое .env.example и прописываем данные доступа к БД и SMTP
3. Выполнить команду

    php yii migrate --migrationPath=@yii/rbac/migrations

4. Выполнить команду

    yii migrate

5. Установить права на директории /pdf и /web/sign/img 755
