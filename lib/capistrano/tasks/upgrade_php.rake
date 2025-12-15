namespace :php do
  desc 'Mise à jour progressive de PHP vers la version 8.3 sans downtime'
  task :upgrade_to_83 do
    on roles(:app) do |host|
      info "=== Démarrage de la mise à jour PHP vers 8.3 sur #{host} ==="
      
      # Étape 1: Vérifier la version actuelle de PHP
      current_php_version = capture("php -v | head -n 1 | grep -oP '\\d+\\.\\d+' | head -1").strip
      info "Version PHP actuelle: #{current_php_version}"
      
      if current_php_version >= "8.3"
        info "PHP 8.3 ou supérieur est déjà installé. Aucune mise à jour nécessaire."
        next
      end
      
      # Étape 2: Installer PHP 8.3 en parallèle (sans désinstaller l'ancienne version)
      info "Installation de PHP 8.3 en parallèle de PHP #{current_php_version}..."
      execute :sudo, "apt update"
      execute :sudo, "apt install -y software-properties-common apt-transport-https"
      execute :sudo, "add-apt-repository -y ppa:ondrej/php"
      execute :sudo, "apt update"
      
      # Installer PHP 8.3 avec toutes les extensions nécessaires
      info "Installation de PHP 8.3 et ses extensions..."
      execute :sudo, "apt install -y php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-intl php8.3-gd php8.3-soap php8.3-bcmath"
      
      # Étape 3: Vérifier que PHP 8.3-FPM est bien installé et démarré
      info "Démarrage de PHP 8.3-FPM..."
      execute :sudo, "systemctl enable php8.3-fpm"
      execute :sudo, "systemctl start php8.3-fpm"
      
      # Vérifier le statut
      php83_status = capture("sudo systemctl is-active php8.3-fpm").strip
      if php83_status != "active"
        error "PHP 8.3-FPM n'a pas démarré correctement!"
        next
      end
      info "✓ PHP 8.3-FPM est actif et fonctionnel"
      
      # Étape 4: Tester PHP 8.3 avec Composer
      info "Test de compatibilité avec Composer..."
      test_result = capture("cd #{fetch(:deploy_to)}/current && /usr/bin/php8.3 /usr/local/bin/composer check-platform-reqs 2>&1 || echo 'FAILED'")
      
      if test_result.include?("FAILED")
        error "PHP 8.3 ne satisfait pas les exigences de Composer!"
        error test_result
        info "Rollback: conservation de PHP #{current_php_version}"
        next
      end
      info "✓ PHP 8.3 est compatible avec vos dépendances Composer"
      
      # Étape 5: Mettre à jour la configuration Nginx progressivement
      info "Mise à jour de la configuration Nginx..."
      
      # Backup de la config actuelle
      execute :sudo, "cp /etc/nginx/sites-available/sfifa.ma /etc/nginx/sites-available/sfifa.ma.backup-$(date +%Y%m%d-%H%M%S)"
      
      # Remplacer php8.2-fpm.sock par php8.3-fpm.sock
      execute :sudo, "sed -i 's/php8\\.2-fpm\\.sock/php8.3-fpm.sock/g' /etc/nginx/sites-available/sfifa.ma"
      
      # Faire de même pour le fichier default si il existe
      if test("[ -f /etc/nginx/sites-available/default ]")
        execute :sudo, "cp /etc/nginx/sites-available/default /etc/nginx/sites-available/default.backup-$(date +%Y%m%d-%H%M%S)"
        execute :sudo, "sed -i 's/php8\\.2-fpm\\.sock/php8.3-fpm.sock/g' /etc/nginx/sites-available/default"
      end
      
      # Tester la configuration Nginx avant de l'appliquer
      info "Test de la configuration Nginx..."
      nginx_test = capture("sudo nginx -t 2>&1")
      
      if nginx_test.include?("syntax is ok") && nginx_test.include?("test is successful")
        info "✓ Configuration Nginx valide"
        
        # Recharger Nginx avec la nouvelle configuration (reload = sans downtime)
        info "Rechargement de Nginx (sans downtime)..."
        execute :sudo, "systemctl reload nginx"
        info "✓ Nginx rechargé avec succès"
      else
        error "La configuration Nginx contient des erreurs!"
        error nginx_test
        # Restaurer la configuration
        execute :sudo, "cp /etc/nginx/sites-available/sfifa.ma.backup-* /etc/nginx/sites-available/sfifa.ma 2>/dev/null || true"
        next
      end
      
      # Étape 6: Définir PHP 8.3 comme version CLI par défaut
      info "Configuration de PHP 8.3 comme version par défaut..."
      execute :sudo, "update-alternatives --set php /usr/bin/php8.3"
      execute :sudo, "update-alternatives --set phar /usr/bin/phar8.3"
      execute :sudo, "update-alternatives --set phar.phar /usr/bin/phar.phar8.3"
      
      # Vérifier la nouvelle version
      new_php_version = capture("php -v | head -n 1 | grep -oP '\\d+\\.\\d+' | head -1").strip
      info "✓ Nouvelle version PHP CLI: #{new_php_version}"
      
      # Étape 7: Vérifier que le site fonctionne
      info "Vérification du fonctionnement du site..."
      site_check = capture("curl -s -o /dev/null -w '%{http_code}' http://localhost/ || echo '000'").strip
      
      if site_check.start_with?("2") || site_check.start_with?("3")
        info "✓ Le site répond correctement (HTTP #{site_check})"
      else
        warn "⚠ Le site répond avec le code HTTP #{site_check}"
        warn "Vérification manuelle recommandée"
      end
      
      # Étape 8: Optionnel - Arrêter l'ancien PHP-FPM (à faire plus tard si tout va bien)
      info ""
      info "=== Mise à jour terminée avec succès ==="
      info "PHP 8.3 est maintenant actif et sert votre application."
      info ""
      info "⚠ IMPORTANT: L'ancien PHP #{current_php_version}-FPM est toujours installé et actif."
      info "Si tout fonctionne correctement après quelques heures/jours, vous pouvez:"
      info "  1. Arrêter l'ancien service: sudo systemctl stop php#{current_php_version}-fpm"
      info "  2. Le désactiver: sudo systemctl disable php#{current_php_version}-fpm"
      info "  3. (Optionnel) Le désinstaller: sudo apt remove php#{current_php_version}-*"
      info ""
    end
  end
  
  desc 'Rollback vers la version précédente de PHP en cas de problème'
  task :rollback do
    on roles(:app) do |host|
      info "=== Rollback de la configuration PHP ==="
      
      # Trouver le dernier backup de la config Nginx
      backup_file = capture("ls -t /etc/nginx/sites-available/sfifa.ma.backup-* 2>/dev/null | head -1").strip
      
      if backup_file.empty?
        error "Aucun fichier de backup trouvé!"
        next
      end
      
      info "Restauration de la configuration depuis: #{backup_file}"
      execute :sudo, "cp #{backup_file} /etc/nginx/sites-available/sfifa.ma"
      
      # Tester et recharger Nginx
      nginx_test = capture("sudo nginx -t 2>&1")
      if nginx_test.include?("syntax is ok")
        execute :sudo, "systemctl reload nginx"
        info "✓ Configuration restaurée avec succès"
      else
        error "Erreur lors du test de la configuration restaurée"
      end
    end
  end
end
