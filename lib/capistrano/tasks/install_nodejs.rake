namespace :deploy do
  namespace :nodejs do
    desc 'Installer Node.js et npm'
    task :install do
      on roles(:all) do |host|
        info "Installation de Node.js et npm sur #{host}..."
        
        # Vérification si Node.js est déjà installé
        if test("command -v node >/dev/null 2>&1")
          info "Node.js est déjà installé, version: #{capture("node -v")}"
        else
          # Installation de Node.js et npm via NodeSource
          execute :sudo, "apt-get update"
          execute :sudo, "apt-get install -y ca-certificates curl gnupg"
          execute :sudo, "mkdir -p /etc/apt/keyrings"
          execute "curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | sudo gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg"
          execute :sudo, "echo 'deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main' | sudo tee /etc/apt/sources.list.d/nodesource.list"
          execute :sudo, "apt-get update"
          execute :sudo, "apt-get install -y nodejs"
          
          # Vérification de l'installation
          info "Node.js #{capture('node -v')} et npm #{capture('npm -v')} installés avec succès!"
        end
      end
    end
  end
end
