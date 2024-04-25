**Projekt aufsetzen**

1. Composer install
2. .env anpassen und umbennen
3. Php artisan migrate machen
4. DB connection testen/anpassen
5. Schema auswählen

**Laravel Telescope hinzufügen**

composer require laravel/telescope
php artisan telescope:install
php artisan migrate

**Neue Migration erstellen**

php artisan make:migration create_tweets_table

**Neues Model erstellen**

php artisan make:model Tweet

**Neuen Controller erstellen**

Php artisan make:controller TweetController

**Neue Resource erstellen**

php artisan make:resource TweetResource

**Neuer Request erstellen**

php artisan make:request LoginUserRequest

**Neue Factory erstellen**

php artisan make:factory TweetFactory

**Seeder ausführen**

php artisan db:seed

**DB neu migrieren und Seeder anschliessend ausführen**

Php artisan migrate:refresh —seed
php artisan migrate:fresh --seed

**Alle Tests ausführen**

php artisan test

**Bestimmte Tests ausführen**

php artisan test --filter=C1Test
php artisan test --group=example

**Api.php erstellen**

Php artisan install:api

**Wenn 429 too many requests kommt**

php artisan cache:clear