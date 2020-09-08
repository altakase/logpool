# logpool

## require

* PHP5.3 or higher
* SQLite
* Aide 0.14

## install

```
curl -sS https://getcomposer.org/installer | php
php ./composer.phar install

yum install sqlite
cd {path_to_install}/db
sqlite3 logpool.db < default.sql
chown -R apache: {path_to_install}/db
# or chown -R nginx: {path_to_install}/db

crontab -e
---
*/5 * * * * php {path_to_install}/batch/cron.php
---

```


```
yum install aide
cp -p /etc/aide.conf{,.default}
vim /etc/aide.conf
---
...
# Next decide what directories/files you want in the database.
/var/www/html NORMAL
/var/spool/cron NORMAL
/etc DATAONLY
!/usr/src
!/usr/tmp
!/usr/share
!{path_to_install}/db
---

aide --init
mv /var/lib/aide/aide.db{.new,}.gz
```

Configure your webserver httpd / nginx, and set document root to "web" folder.

