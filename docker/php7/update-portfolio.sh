#!/bin/bash
# set env variables
set -a
. /root/project_env.sh
set +a
# run php script
/usr/local/bin/php /var/www/symfony/bin/console pri:update_portfolio