## Требования

PHP>=8.2
Composer
MySQL

## Установка

Клонировать репозиторий https://github.com/Borvon/todolist_laravel

### Установить зависимости
composer install

### Создать файл окружения
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=(база данных)
DB_USERNAME=(пользователь)
DB_PASSWORD=(пароль)

### Сгенерировать ключ приложения
php artisan key:generate

### Сгенерировать jwt secret
php artisan jwt:secret

### Применить миграции
php artisan migrate

## Запуск
php artisan serve