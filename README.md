## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

## About LocumKit

Locumkit is a platform created by locums for locums.

### About Laravel System

This is web system for locumkit. It include web routes as well as API routes for Locumkit Mobile App.

### Running web system

The web system is created in Laravel (PHP Framework). To run it you need to install PHP. You can check PHP version from composer.json. Current version of PHP for locumkit is 8.2

To migrate database and seed run

php artisan migrate:fresh --seed


#### On local

Start php server by `php artisan server` Start schedule job `php artisan schedule:work`

#### On server

On server point all request to public folder of the project. Run a cron job for schedule.
