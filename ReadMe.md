//Récupérer les dépendances
Composer require orm-fixtures --dev
Composer require admin

//Vérifier les dépendances
symfony check:requirements

//Connecter votre base de donnée dans le fichier .env

//Exécuter ce fichier pour vider, créer les tables et réaliser les enregistrements
sh schemaUpdateFixture.sh

//Regarder le sql généré :
php bin/console doctrine:schema:update  --dump-sql

//Exécuter le sql
php bin/console doctrine:schema:update  --force

//lister les version php
symfony local:php:list

//Changer de version php si besoin
echo version > .php-version

//Démarrer le projet en http
Symfony server:start --no-tls

//Stopper le server
Symfony server:stop
