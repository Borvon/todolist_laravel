## Функциональность

### 1. Регистрация и аутентификация
 • Регистрация нового пользователя (POST /api/register)  
 • Авторизация пользователя (POST /api/login)  
 • Получение данных текущего пользователя (GET /api/me)  
 
 ### 2. Работа с задачами
 • Получить список всех задач текущего пользователя (GET /api/tasks)  
 • Получить одну задачу (GET /api/tasks/{id})  
 • Создать новую задачу (POST /api/tasks)  
 • Обновить задачу (PUT /api/tasks/{id})  
 • Удалить задачу (DELETE /api/tasks/{id}  

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
