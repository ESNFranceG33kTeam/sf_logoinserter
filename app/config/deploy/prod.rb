set :branch, "master"
set :deploy_to, "/home/esnlille/www/logoinserter.esnlille.fr"
set :symfony_env_prod, "prod"
set :domain, "ftp.esnlille.odns.fr"
set :user, "esnlille"
#set :php_bin, "/usr/local/php5.5/bin/php"

role :web,        domain
role :app,        domain, :primary => true
role :db,         domain, :primary => true

#Deploy Strategy
set :deploy_via, :copy
set :deploy_via, :rsync_with_remote_cache
set :copy_cache, "/tmp/#{application}"
set :copy_exclude, [".git/*"]
set :copy_compression, :gzip

# conserver le app_dev.php
set :controllers_to_clear, []