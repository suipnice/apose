#!/usr/bin/bash
set -o allexport
source /etc/sysconfig/httpd
/usr/bin/php /var/www/html_apose/cron/apogee_recup.php >> /var/www/html_apose/cron.log 2>&1
