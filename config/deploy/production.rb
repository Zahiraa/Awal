# Configuration pour l'environnement de production

# Définition du serveur de production
server "51.68.213.123", user: "ubuntu", roles: %w{app db web}

# Configuration spécifique à la production
set :symfony_env, "prod"
set :branch, "deploy-prod"

# Activer l'agent de transfert SSH pour utiliser vos clés locales
set :ssh_options, {
  forward_agent: true,
  auth_methods: %w(publickey),
  port: 22
}

# Configuration de la base de données pour les migrations (optionnel)
# Vous pouvez ajouter ici des variables spécifiques à la production
# set :default_env, {
#   'DATABASE_URL' => 'mysql://sfifa:sfifa@127.0.0.1:3306/sfifa?serverVersion=8.0&charset=utf8mb4'
# }

# Nombre de versions à conserver spécifiquement en production
set :keep_releases, 3
