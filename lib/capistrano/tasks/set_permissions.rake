namespace :deploy do
  namespace :permissions do
    desc 'Configure les permissions sur les répertoires var/cache, public/uploads et var/log'
    task :setup do
      on roles(:web) do |host|
        info "Configuration des permissions sur #{host}..."
        
        # Récupérer l'utilisateur web (www-data sur la plupart des systèmes Ubuntu)
        web_user = 'www-data'
        
        # Récupérer le chemin du déploiement actuel
        current_release_path = release_path
        
        # Créer les répertoires s'ils n'existent pas
        execute :mkdir, "-p #{current_release_path}/var/cache"
        execute :mkdir, "-p #{current_release_path}/var/log"
        execute :mkdir, "-p #{current_release_path}/public/uploads"
        
        # Configurer les permissions
        execute :sudo, "chown -R ubuntu:#{web_user} #{current_release_path}/var"
        execute :sudo, "chmod -R 775 #{current_release_path}/var"
        execute :sudo, "chown -R ubuntu:#{web_user} #{current_release_path}/public/uploads"
        execute :sudo, "chmod -R 775 #{current_release_path}/public/uploads"
        
        # Configuration spécifique pour cache et log
        execute :sudo, "chown -R #{web_user}:#{web_user} #{current_release_path}/var/cache"
        execute :sudo, "chown -R #{web_user}:#{web_user} #{current_release_path}/var/log"
        execute :sudo, "chown -R #{web_user}:#{web_user} #{current_release_path}/public/uploads"
        execute :sudo, "chmod -R 777 #{current_release_path}/var/cache"
        execute :sudo, "chmod -R 777 #{current_release_path}/var/log"
        execute :sudo, "chmod -R 777 #{current_release_path}/public/uploads"
        info "Permissions configurées avec succès."
      end
    end

    desc 'Prépare les anciennes versions pour la suppression en ajustant les permissions'
    task :prepare_cleanup do
      on roles(:web) do |host|
        info "Préparation des anciens déploiements pour la suppression sur #{host}..."
        
        # Récupérer le chemin des déploiements
        deploy_path = fetch(:deploy_to)
        
        # Modifier les permissions des anciens déploiements pour permettre la suppression
        execute :sudo, "find #{deploy_path}/releases -type d -name 'cache' -o -name 'log' | xargs -I {} sudo chmod -R 775 {}"
        execute :sudo, "find #{deploy_path}/releases -type d -name 'cache' -o -name 'log' | xargs -I {} sudo chown -R ubuntu:ubuntu {}"
        
        info "Les permissions ont été ajustées pour permettre la suppression."
      end
    end
  end

  # Crochet pour exécuter automatiquement la configuration des permissions après le déploiement
  # Nous utilisons 'finished' pour être sûr qu'il s'exécute après toutes les autres étapes
  after 'deploy:finished', 'deploy:permissions:setup'
  # Modifier les permissions avant le nettoyage pour permettre la suppression
  before 'deploy:cleanup', 'deploy:permissions:prepare_cleanup'
end
