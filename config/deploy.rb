# config valid for current version and patch releases of Capistrano
lock "~> 3.19.2"

# Configuration de l'application
set :application, "awal"
set :repo_url, "git@github.com:Zahiraa/awal.git"
# Utiliser l'URL GitHub standard

# Configuration du déploiement
set :deploy_to, "/var/www/awal"
set :keep_releases, 3

# Configuration de Symfony
set :symfony_env, "prod"
set :symfony_directory_structure, :standard
set :controllers_to_clear, ["app_*.php", "config.php"]
set :composer_install_flags, "--no-interaction --optimize-autoloader --no-dev"
set :symfony_console_path, "bin/console"
set :symfony_console_flags, "--no-debug"

set :git_environmental_variables, {
  GIT_SSH_COMMAND: 'ssh -i /home/ubuntu/.ssh/id_ed25519 -o IdentitiesOnly=yes'
}


# Paramètres SSH - Configuration optimisée
set :ssh_options, {
  forward_agent: false,
  verify_host_key: :never,
  auth_methods: %w(publickey),
  # Utiliser la clé du serveur directement
  keys: %w(~/.ssh/awal),
  config: true
}

# Configuration des fichiers et répertoires partagés
append :linked_files, ".env.local"
append :linked_dirs, "var/log", "var/sessions", "var/cache", "public/uploads"

# Configuration des permissions
set :file_permissions_paths, ["var", "var/log", "var/cache", "var/sessions"]
set :file_permissions_users, ["www-data"]

# Configuration des tâches à exécuter après le déploiement
namespace :deploy do
  desc "Exécute les migrations de base de données"
  task :migrations do
    on roles(:db) do
      within release_path do
        execute :php, "#{release_path}/#{fetch(:symfony_console_path)}", "doctrine:migrations:migrate", "--no-interaction", "--env=#{fetch(:symfony_env)}", "--no-debug"
      end
    end
  end

  desc "Exécute les tâches post-déploiement"
  task :post_deploy do
    on roles(:app) do
      within release_path do
        execute :php, "#{release_path}/#{fetch(:symfony_console_path)}", "cache:clear", "--env=#{fetch(:symfony_env)}", "--no-debug"
        execute :php, "#{release_path}/#{fetch(:symfony_console_path)}", "assets:install", "--env=#{fetch(:symfony_env)}", "--no-debug"
      end
    end
  end

  after "deploy:updated", "deploy:migrations"
  after :published, :post_deploy
  after :finishing, :cleanup
end

# Configuration pour webpack encore et les dépendances NPM
namespace :symfony do
  desc "Vérifie et installe les dépendances NPM et construit les assets"
  task :encore_build do
    on roles(:app) do
      within release_path do
        # Vérifier si node_modules existe et est à jour
        test_node_modules = capture("if [ -d #{release_path}/node_modules ] && [ -e #{release_path}/node_modules/.package-lock.json ] && diff -q #{release_path}/package-lock.json #{release_path}/node_modules/.package-lock.json >/dev/null 2>&1; then echo 'UPTODATE'; else echo 'OUTDATED'; fi").strip
        
        if test_node_modules == 'OUTDATED'
          info "Installing NPM dependencies..."
          execute :npm, "install"
          execute :cp, "#{release_path}/package-lock.json", "#{release_path}/node_modules/.package-lock.json"
        else
          info "NPM dependencies are up to date"
        end
        
        # Construction des assets
        info "Building Webpack assets..."
        execute :npm, "run", "build"
      end
    end
  end
  
  # S'assurer que les dépendances composer sont également à jour
  desc "Vérifie les dépendances composer"
  task :composer_check do
    on roles(:app) do
      within release_path do
        if test("[ ! -d #{release_path}/vendor ] || [ ! -f #{release_path}/vendor/autoload.php ]")
          info "Installing Composer dependencies..."
          execute :composer, "install", fetch(:composer_install_flags)
        end
      end
    end
  end
  
  # Hooks pour exécuter les tâches dans l'ordre
  after "deploy:updated", "symfony:composer_check"
  after "symfony:composer_check", "symfony:encore_build"
end
