git clone
composer update
phinx migrate
phinx seed (optional)
php -S localhost:8000 -t public