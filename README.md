# symfony fil rouge

composer install

## requires

composer require symfony/maker-bundle --dev

composer require orm

composer require orm-fixtures --dev

composer require symfony/serializer-pack




## tools

symfony server:start

php bin/console make:entity

php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load

php bin/console make:controller

