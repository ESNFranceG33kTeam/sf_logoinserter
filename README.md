# LogoInserter

# Install

## Local Install


```shell
git clone git@github.com:ESNFranceG33kTeam/sf_logoinserter.git
```
or 

```shell
git clone https://github.com/ESNFranceG33kTeam/sf_logoinserter.git
```

Installez [composer](https://getcomposer.org) :

```shell
curl -sS https://getcomposer.org/installer | php
```

install all vendors

```shell
php composer.phar install
```

create directories

```shell
mkdir -p web/uploads/sections web/uploads/downloadsessions web/uploads/public
```

on Mac 

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs web/uploads/sections web/uploads/downloadsessions web/uploads/public
sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/logs  web/uploads/sections web/uploads/downloadsessions web/uploads/public
```

on Linux

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/uploads/sections web/uploads/downloadsessions web/uploads/public
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/uploads/sections web/uploads/downloadsessions web/uploads/public
```

Deploy assets

```shell
php app/console assets:install
```
 
Update your database

```shell
php app/console doctrine:migrations:migrate
```

Import sections from file

```shell
php app/console esn:section:import
```


