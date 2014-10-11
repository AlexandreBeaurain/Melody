#!/bin/sh
composer update --dev
php app/console propel:model:build
rm -Rf app/propel/migrations/*.php
php app/console propel:migration:generate-diff
php app/console propel:migration:migrate --verbose
php app/console doctrine:schema:update --force
php app/console cache:clear
