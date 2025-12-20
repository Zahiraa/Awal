# Awal


## Technologies utilisées

- **Backend**: Symfony 6.4, PHP 8.x
- **Frontend**: 
  - Tailwind CSS pour les styles
  - Select2 avec personnalisation pour les catégories
- **Base de données**: Doctrine ORM avec PostgreSQL
- **Emails**: Symfony Mailer
- **Formulaires multilingues**: Formulaires personnalisés avec support RTL

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Symfony CLI
- Node.js et npm/yarn
- Serveur MySQL/MariaDB

## Installation

1. Cloner le dépôt:
```bash
git clone
cd awal
```

2. Configurer les variables d'environnement:
   Copier le fichier `.env` en `.env.local` et ajuster la configuration de la base de données et du mailer:
```
DATABASE_URL=mysql://user:password@127.0.0.1:3306/awal
MAILER_DSN=smtp://localhost:1025
MAILER_FROM_ADDRESS=no-reply@awal.com
MAILER_FROM_NAME="AWAl"
```

3.Installer:
```bash
docker compose build
docker compose up -d
docker compose exec -T php php bin/console assets:install
docker compose exec php sh -c 'if [ ! -f .env.local ]; then cp .env .env.local; fi'
docker compose run --rm php composer install

```

4. Installer les dépendances JavaScript:
```bash
npm install
```

5. Créer la base de données:
```bash
docker compose exec php php bin/console doctrine:database:drop --force --if-exists
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

6. Compiler les assets:
```bash
npm run build
```

7. Lancer le serveur de développement:
```bash
docker compose up -d && npm run watch
```
