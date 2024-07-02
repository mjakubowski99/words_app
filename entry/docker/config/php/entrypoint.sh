#!/bin/bash

composer install
npm install

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
