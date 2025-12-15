1. После клонирования репозитория выполнить

    composer install
    
2. в файле config/db.php указываем доступы к базе данных и префикс таблиц
3. Выполнить команду

    php yii migrate --migrationPath=@yii/rbac/migrations

4. Выполнить команду

    yii migrate

5. Создать папку web/sign/img
