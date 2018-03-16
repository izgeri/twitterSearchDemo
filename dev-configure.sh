#!/bin/bash -ex

# rewrite 000-default.conf to make /var/www/app/ the DocumentRoot
sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/app/' \
  /etc/apache2/sites-available/000-default.conf 

# start apache
apache2-foreground
