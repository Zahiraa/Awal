namespace :deploy do
  namespace :check do
    desc 'Vérifie que les dépendances système sont installées'
    task :dependencies do
      on roles(:all) do |host|
        info "Vérification des dépendances système sur #{host}..."
        
        # Vérifier si PHP est installé
        php_installed = test("which php > /dev/null")
        unless php_installed
          info "PHP n'est pas installé. Installation en cours..."
          
          # Ajouter le PPA pour PHP
          execute :sudo, "apt update"
          execute :sudo, "apt install -y software-properties-common apt-transport-https"
          execute :sudo, "add-apt-repository -y ppa:ondrej/php"
          execute :sudo, "apt update"
          
          # Essayer d'installer PHP 8.3
          php_install_success = false
          begin
            execute :sudo, "apt install -y php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-intl php8.3-gd"
            php_install_success = true
          rescue
            info "Installation de PHP 8.3 échouée, tentative avec PHP 8.2..."
            begin
              execute :sudo, "apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-intl php8.2-gd"
              php_install_success = true
            rescue
              info "Installation de PHP 8.2 échouée, tentative avec PHP 8.1..."
              execute :sudo, "apt install -y php8.1-cli php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-intl php8.1-gd"
              php_install_success = true
            end
          end
          
          if php_install_success
            info "PHP a été installé avec succès."
          else
            error "Impossible d'installer PHP, veuillez le faire manuellement."
            exit 1
          end
        else
          info "PHP est déjà installé."
        end
        
        # Vérifier si Composer est installé
        composer_installed = test("which composer > /dev/null")
        unless composer_installed
          info "Composer n'est pas installé. Installation en cours..."
          execute "cd /tmp && curl -sS https://getcomposer.org/installer | php"
          execute :sudo, "mv /tmp/composer.phar /usr/local/bin/composer"
          execute :sudo, "chmod +x /usr/local/bin/composer"
          info "Composer a été installé avec succès."
        else
          info "Composer est déjà installé."
        end
        
        # Vérifier si MySQL est installé
        mysql_installed = test("which mysql > /dev/null")
        unless mysql_installed
          info "MySQL n'est pas installé. Installation en cours..."
          # Récupérer les valeurs du fichier .env du projet source
          env_content = File.read(File.join(File.dirname(__FILE__), "../../../.env"))
          
          # Extraire les valeurs des variables MySQL
          mysql_root_password = env_content.match(/MYSQL_ROOT_PASSWORD=([^\s\n]+)/)[1] rescue "awal"
          db_name = env_content.match(/MYSQL_DATABASE=([^\s\n]+)/)[1] rescue "awal"
          db_user = env_content.match(/MYSQL_USER=([^\s\n]+)/)[1] rescue "awal"
          db_password = env_content.match(/MYSQL_PASSWORD=([^\s\n]+)/)[1] rescue "awal"
          
          # Définir un mot de passe root pour MySQL
          execute :sudo, "debconf-set-selections <<< 'mysql-server mysql-server/root_password password '"
          execute :sudo, "debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password '"
          execute :sudo, "apt install -y mysql-server"
          
          # S'assurer que MySQL est démarré
          execute :sudo, "systemctl enable --now mysql"
          
          execute :sudo, "mysql -u root -e \"CREATE DATABASE IF NOT EXISTS #{db_name};\""
          execute :sudo, "mysql -u root -e \"CREATE USER IF NOT EXISTS '#{db_user}'@'localhost' IDENTIFIED BY '#{db_password}';\""
          execute :sudo, "mysql -u root -e \"GRANT ALL PRIVILEGES ON #{db_name}.* TO '#{db_user}'@'localhost';\""
          execute :sudo, "mysql -u root -e \"FLUSH PRIVILEGES;\""
          
          # Mettre à jour la variable DATABASE_URL dans .env.local
          execute :sudo, "sed -i 's|DATABASE_URL=\"mysql://.*\@127.0.0.1:3306/.*\"|DATABASE_URL=\"mysql://#{db_user}:#{db_password}@127.0.0.1:3306/#{db_name}\"|g' /var/www/awal/shared/.env.local"
          
          info "MySQL a été installé et configuré avec succès."
        else
          info "MySQL est déjà installé."
        end
      end
    end
  end
  
  # S'assurer que cette tâche s'exécute avant le déploiement
  before 'deploy:starting', 'deploy:check:dependencies'
end
