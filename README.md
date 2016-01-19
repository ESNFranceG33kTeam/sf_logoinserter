# sf_logoinserter

setfacl -R -m u:www-data:rwX web/uploads/ app/cache app/logs
setfacl -dR -m u:www-data:rwX web/uploads/ app/cache app/logs