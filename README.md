1. После клонирования репозитория выполнить

    composer install
    
2. в файле config/db.php указываем доступы к базе данных и префикс таблиц
3. В корне проекта создать файл .env, в него скопировать содержимое файла .env.example и поменять параметры подключения к апи МИС
4. Выполнить команду

    yii migrate
    
5. Выполнить команду

    php yii migrate --migrationPath=@yii/rbac/migrations
    
6. Выполнить SQL-запрос, чтобы создать пользователя admin. В запросе поменять prefix

    INSERT INTO `prefix_user` (`id`, `username`, `auth_key`, `password`, `password_hash`, `password_reset_token`, `email`, `status`, `is_active`, `position`, `created_at`, `updated_at`) VALUES
    (1, 'admin', '', '', '$2y$13$qbwbJLHvzFspPDBTfdNUxe7ch7AdAOePMruCeq5U9KSdqa8bgs9fC', NULL, '', 10, 1, 1, 0, 0);
    
7. Отредактировать название сайтв в файле config/web.php 
    $config['name']
    
8. В файле config/params.php установить параметр cancelUnsigned true/false, чтобы отменять неподписанные документы при создании нового в рамках одного планшета

9. В файле config/params.php установить параметр tabletUrl - урл для планшета
