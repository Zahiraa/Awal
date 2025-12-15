namespace :adminer do
  desc 'Installer Adminer et configurer Nginx pour y accéder via /adminer'
  task :setup do
    on roles(:web) do |host|
      info "Installation d'Adminer sur #{host}..."
      
      # Créer le répertoire pour Adminer s'il n'existe pas
      execute :sudo, "mkdir -p /var/www/adminer"
      
      # Télécharger la dernière version d'Adminer
      execute :sudo, "curl -s -o /var/www/adminer/index.php https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php"
      
      # Configurer les permissions
      execute :sudo, "chown -R www-data:www-data /var/www/adminer"
      execute :sudo, "chmod -R 755 /var/www/adminer"
      
      # Trouver le fichier de configuration Nginx du site
      info "Recherche du fichier de configuration Nginx du site..."
      site_config_path = capture("find /etc/nginx/sites-available -type f -exec grep -l 'server_name' {} \\; | head -1").strip
      
      if site_config_path.empty?
        error "Impossible de trouver le fichier de configuration Nginx du site."
        next
      end
      
      # Vérifier si la configuration Adminer est déjà présente
      adminer_present = capture("grep -q '/adminer' #{site_config_path} && echo 'yes' || echo 'no'").strip == "yes"
      
      if adminer_present
        info "La configuration Adminer est déjà présente dans Nginx."
      else
        # Récupérer le contenu du fichier de configuration Nginx
        site_config = capture("sudo cat #{site_config_path}")
        
        # Créer la configuration Adminer
        adminer_config = <<~CONFIG
        # Configuration pour Adminer
        location /adminer {
            alias /var/www/adminer;
            index index.php;
            
            location ~ \\.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_param SCRIPT_FILENAME $request_filename;
                fastcgi_pass unix:/run/php/php8.3-fpm.sock;
            }
        }
        CONFIG
        
        # Trouver le premier bloc server
        server_block_match = site_config.match(/\s*server\s*\{([^}]*)\}/m)
        
        # Si on ne trouve pas de bloc server, on affiche une erreur
        if server_block_match.nil?
          error "Impossible de trouver un bloc 'server' dans la configuration Nginx."
          next
        end
        
        # Récupérer le bloc server
        server_block = server_block_match[0]
        
        # Insérer la configuration Adminer dans le bloc server
        modified_server_block = server_block.sub(/\s*\}\s*$/, "\n#{adminer_config}\n}")
        
        # Remplacer le bloc server d'origine par le bloc modifié
        modified_site_config = site_config.gsub(server_block, modified_server_block)
        
        # Créer un fichier temporaire avec la nouvelle configuration
        upload! StringIO.new(modified_site_config), "/tmp/modified_nginx.conf"
        
        # Remplacer le fichier de configuration par la version modifiée
        execute :sudo, "cp /tmp/modified_nginx.conf #{site_config_path}"
        
        # Tester la configuration Nginx
        info "Test de la configuration Nginx..."
        nginx_test = capture("sudo nginx -t 2>&1")
        
        if nginx_test.include?("successful")
          # Redémarrer Nginx si le test est réussi
          info "Configuration Nginx valide, redémarrage du service..."
          execute :sudo, "systemctl restart nginx"
          info "Installation d'Adminer terminée. Vous pouvez y accéder via http://votre-domaine.com/adminer"
          info "Utilisez les informations de votre base de données pour vous connecter."
        else
          error "La configuration Nginx n'est pas valide :"
          error nginx_test
          # Restaurer la configuration d'origine
          execute :sudo, "cp -f #{site_config_path}.bak #{site_config_path} 2>/dev/null || true"
        end
      end
    end
  end
end
