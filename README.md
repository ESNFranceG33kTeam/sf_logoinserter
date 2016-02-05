# sf_logoinserter

setfacl -R -m u:www-data:rwX web/uploads/ app/cache app/logs
setfacl -dR -m u:www-data:rwX web/uploads/ app/cache app/logs

# Install

## Local Install


```shell
git@github.com:ESNFranceG33kTeam/sf_logoinserter.git
```

```shell
php composer.phar install
```

on Mac 

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo chmod +a "$HTTPDUSER allow delete,write,append,file_inherit,directory_inherit" app/cache app/journal app/logs app/spool web/uploads web/organisms
sudo chmod +a "`whoami` allow delete,write,append,file_inherit,directory_inherit" app/cache app/journal app/logs app/spool web/uploads web/organisms
```

on Linux

```shell
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/journal app/logs app/spool web/uploads web/organisms
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/journal app/logs app/spool web/uploads web/organisms
```



