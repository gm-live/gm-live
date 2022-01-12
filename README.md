# GM-live backend (live streaming platform demo)

## Project system env
```
php >= 8.0
swoole >= 4.8.0
mysql >= 5.7
redis
```
## How to run?
```
git clone git@github.com:gm-live/gm-live.git
cd gm-live
composer install

# change db and redis info in .env
cp .env.example .env

php bin/hyperf.php migrate
php bin/hyperf.php start
```
