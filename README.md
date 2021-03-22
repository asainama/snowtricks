# P6 - SNOWTRICKS

Création d'un site communautaire de partage de figures de snowboard via le framework Symfony.

## Environnement

    * Symfony 5.2
    * Composer 2.0.7
    * PHP 7.2.1
    * MYSQL  8.0.19
    * NODE 14.15.0
    * YARN 1.22.4

## Installation

1. Clonez le répertoire

        ```
            git clone https://github.com/asainama/snowtricks.git
        ```

2. Configuration du env.local

    Créer un fichier .env.local qui devra avoir:

        ```
            APP_ENV=dev
            APP_SECRET=
            DATABASE_URL="mysql://user:password@database_address/snowtricks?serverVersion=VERSION"
            MAILER_URL=smtp://localhost:1025
        ```

3. Installer le projet à l'aide de la commande

        ```
            composer install
        ```

4. Installer les dépendances node avec npm ou yarn:

        ```
            npm install
        ```

5. Créer le build

        ```
            npm run build
        ```

6. Créer la base de données

        ```
            php bin/console doctrine:database:create
        ```

7. Utiliser les migrations pour créer les tables

        ```
            php bin/console doctrine:migrations:migrate
        ```

8. Afin de ne pas avoir un projet vierge installer les fixtures

        ```
            php bin/console doctrine:fixtures:load
        ```

9. Si vous avez rajouter les fixtures l'utilisateur
    > admin@admin.fr avec le mot de passe password est crée

10. Félicitation le projet est maintenant installé.
