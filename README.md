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
```


```
yum install aide
cp -p /etc/aide.conf{,.default}
vim /etc/aide.conf
aide --init
---
...
# Next decide what directories/files you want in the database.
/boot   NORMAL
/bin    NORMAL
/sbin   NORMAL
/lib    NORMAL
/lib64  NORMAL
/opt    NORMAL
/usr    NORMAL
/root   NORMAL
/home   NORMAL
/var/www/html NORMAL
/var/spool/cron NORMAL
/etc DATAONLY
!/usr/src
!/usr/tmp
!/usr/share
---

aide --init
mv /var/lib/aide/aide.db{.new,}.gz
```

Configure your webserver httpd / nginx, and set document root to "web" folder.

