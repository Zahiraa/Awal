namespace :deploy do
  namespace :nginx do
    desc 'Installer et configurer Nginx pour sfifa.ma'
    task :setup do
      on roles(:web) do |host|
        info "Configuration de Nginx sur #{host}..."
        
        # Vérifier si Nginx est déjà installé
        nginx_installed = test("which nginx > /dev/null")
        unless nginx_installed
          info "Nginx n'est pas installé. Installation en cours..."
          execute :sudo, "apt-get update"
          execute :sudo, "apt-get install -y nginx"
        else
          info "Nginx est déjà installé."
        end
        
        # Récupérer le chemin du déploiement actuel
        current_path = fetch(:deploy_to)
        
        # Créer le fichier de configuration Nginx pour sfifa.ma
        nginx_config = <<~CONFIG
          server {
              listen 80;
              server_name sfifa.ma www.sfifa.ma;
              
              root #{current_path}/current/public;
              
              location / {
                  try_files $uri /index.php$is_args$args;
              }
              
              location ~ ^/index\\.php(/|$) {
                  fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
                  fastcgi_split_path_info ^(.+\\.php)(/.*)$;
                  include fastcgi_params;
                  fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                  fastcgi_param DOCUMENT_ROOT $realpath_root;
                  internal;
              }
              
              location ~ \\.php$ {
                  return 404;
              }
              
              error_log /var/log/nginx/sfifa_error.log;
              access_log /var/log/nginx/sfifa_access.log;
          }
        CONFIG
        
        # Écrire la configuration dans un fichier temporaire
        upload! StringIO.new(nginx_config), "/tmp/sfifa.ma"
        
        # Déplacer le fichier vers le dossier de configuration Nginx
        execute :sudo, "mv /tmp/sfifa.ma /etc/nginx/sites-available/sfifa.ma"
        
        # Activer le site
        execute :sudo, "ln -sf /etc/nginx/sites-available/sfifa.ma /etc/nginx/sites-enabled/sfifa.ma"
        
        # Configurer le site par défaut pour l'accès direct par IP
        default_config = <<~CONFIG
          server {
              listen 80 default_server;
              listen [::]:80 default_server;
              
              root #{current_path}/current/public;
              
              location / {
                  try_files $uri /index.php$is_args$args;
              }
              
              location ~ ^/index\\.php(/|$) {
                  fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
                  fastcgi_split_path_info ^(.+\\.php)(/.*)$;
                  include fastcgi_params;
                  fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
                  fastcgi_param DOCUMENT_ROOT $realpath_root;
                  internal;
              }
              
              location ~ \\.php$ {
                  return 404;
              }
              
              error_log /var/log/nginx/default_error.log;
              access_log /var/log/nginx/default_access.log;
          }
        CONFIG
        
        # Écrire la configuration par défaut
        upload! StringIO.new(default_config), "/tmp/default"
        execute :sudo, "mv /tmp/default /etc/nginx/sites-available/default"
        execute :sudo, "ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default"
        
        # Vérifier la configuration Nginx
        execute :sudo, "nginx -t"
        
        # Redémarrer Nginx
        execute :sudo, "systemctl restart nginx"
        
        info "Nginx est maintenant configuré pour le domaine sfifa.ma et l'accès direct par IP"
      end
    end
  end
end
