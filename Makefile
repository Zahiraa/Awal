COMPOSE=docker compose

install:
	$(COMPOSE) build
	$(COMPOSE) up -d
	$(COMPOSE) exec php sh -c 'if [ ! -f .env.local ]; then cp .env .env.local; fi'
	$(COMPOSE) run --rm php composer install
	$(COMPOSE) exec -T php php bin/console assets:install

start:
	$(COMPOSE) up -d
	npm i && npm run build && npm run watch

stop:
	$(COMPOSE) down

clean_db:
	$(COMPOSE) exec php php bin/console doctrine:database:drop --force --if-exists
	$(COMPOSE) exec php php bin/console doctrine:database:create
	$(COMPOSE) exec php php bin/console doctrine:migrations:migrate --no-interaction
	$(COMPOSE) exec php php bin/console doctrine:fixtures:load --no-interaction
	rm -rf public/uploads/*

logs:
	$(COMPOSE) logs -f

bash:
	$(COMPOSE) exec php bash

migrate:
	$(COMPOSE) exec php php bin/console doctrine:migrations:migrate --no-interaction

entity:
	$(COMPOSE) exec php php bin/console make:entity

assets:
	$(COMPOSE) exec -T php php bin/console assets:install

crud:
	$(COMPOSE) exec php php bin/console make:crud

# ==================================
# Commandes de Déploiement Production
# ==================================

# Mise à jour PHP 8.3 en production (SANS DOWNTIME)
prod-upgrade-php:
	@echo "🚀 Mise à jour PHP 8.3 en production..."
	@echo "⚠️  Cette opération ne causera PAS de downtime"
	@echo "📖 Voir docs/UPGRADE_PHP_83.md pour plus de détails"
	@read -p "Continuer? [y/N] " confirm && [ "$$confirm" = "y" ] || exit 1
	bundle exec cap production php:upgrade_to_83

# Rollback PHP en cas de problème
prod-rollback-php:
	@echo "🔄 Rollback de la configuration PHP..."
	bundle exec cap production php:rollback

# Déployer l'application en production
prod-deploy:
	@echo "🚀 Déploiement en production..."
	bundle exec cap production deploy

# Vérifier l'état du serveur de production
prod-check:
	@echo "🔍 Vérification du serveur de production..."
	bundle exec cap production invoke "php -v"
	bundle exec cap production invoke "nginx -t"
	bundle exec cap production invoke "systemctl status php8.3-fpm --no-pager"

# Voir les logs de production
prod-logs:
	@echo "📋 Logs Nginx (Ctrl+C pour quitter)..."
	bundle exec cap production invoke "sudo tail -f /var/log/nginx/sfifa_error.log"

# Backup de la base de données de production
prod-backup-db:
	@echo "💾 Backup de la base de données..."
	ssh ubuntu@51.68.213.123 "sudo mysqldump -u sfifa -p sfifa > ~/backup_sfifa_\$$(date +%Y%m%d_%H%M%S).sql"
	@echo "✅ Backup créé avec succès"

# Aide pour les commandes de production
prod-help:
	@echo "📖 Commandes de déploiement disponibles:"
	@echo ""
	@echo "  make prod-upgrade-php   - Mettre à jour PHP vers 8.3 (sans downtime)"
	@echo "  make prod-rollback-php  - Rollback de la configuration PHP"
	@echo "  make prod-deploy        - Déployer l'application"
	@echo "  make prod-check         - Vérifier l'état du serveur"
	@echo "  make prod-logs          - Voir les logs en temps réel"
	@echo "  make prod-backup-db     - Backup de la base de données"
	@echo ""
	@echo "📄 Documentation complète: docs/UPGRADE_PHP_83.md"