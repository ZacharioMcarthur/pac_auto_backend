# Déploiement ParcAuto (Laravel API)

## 1) Prérequis serveur
- PHP 8.1+
- Composer
- MySQL
- Extensions PHP usuelles Laravel (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)

## 2) Installation
1. `composer install --no-dev --optimize-autoloader`
2. Copier `.env.example` vers `.env`
3. Configurer variables (`APP_ENV`, `APP_DEBUG=false`, DB, mail, CORS, clés tierces)
4. `php artisan key:generate`
5. `php artisan migrate --force`
6. `php artisan storage:link`

## 3) Optimisation prod
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

## 4) Tâches planifiées / queue
Configurer cron:
- `* * * * * php /path/to/project/artisan schedule:run >> /dev/null 2>&1`

Configurer worker queue si `QUEUE_CONNECTION` != `sync`:
- `php artisan queue:work --tries=3`

## 5) Web server
- Nginx/Apache doit pointer sur `public/`
- Autoriser HTTPS en production

## 6) Vérification post-déploiement
- Test `POST /api/login`
- Test endpoint protégé avec bearer token
- Vérifier logs `storage/logs/laravel.log`
