#!/bin/bash

# export environment variables in a file
printenv | sed -r "s/'/\\\'/gm" | sed -r "s/^([^=]+=)(.*)\$/export \1'\2'/gm"  >> /root/project_env.sh

# Run supervisord
/usr/local/bin/supervisord -n -c /etc/supervisord.conf

# Update composer
#composer update
